<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Support\SystemNotification;
use App\Models\AssignmentTarget;
use App\Models\StudyGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateAssignment extends CreateRecord
{
    protected static string $resource = AssignmentResource::class;

    protected ?string $heading = 'Tambah Tugas';

    protected static ?string $title = 'Tambah Tugas';

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ListAssignments::getUrl()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return SystemNotification::create();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    public function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $studentIds = $data['student_ids'] ?? [];
            $studyGroupIds = $data['study_group_ids'] ?? [];
            $pdf = $data['pdf'] ?? null;

            unset($data['student_ids'], $data['study_group_ids'], $data['pdf']);

            $assignment = $this->getResource()::getModel()::create($data);

            if ($assignment->type === AssignmentType::Individual) {
                foreach ($studentIds as $studentId) {
                    AssignmentTarget::create([
                        'assignment_id' => $assignment->id,
                        'student_id' => $studentId,
                    ]);
                }
            } else {
                $studyGroupIds = StudyGroup::whereHas('courses', function ($q) use ($assignment) {
                    $q->where('courses.id', $assignment->course_id);
                })->pluck('id');

                foreach ($studyGroupIds as $groupId) {
                    AssignmentTarget::create([
                        'assignment_id' => $assignment->id,
                        'study_group_id' => $groupId,
                    ]);
                }
            }

            if (! empty($pdf)) {
                $filePath = is_array($pdf) ? reset($pdf) : $pdf;

                $assignment->addMediaFromDisk($filePath, config('filesystems.default'))
                    ->withCustomProperties([
                        'feature' => 'assignments',
                        'date' => now()->toDateString(),
                        'doc_type' => 'tasks',
                    ])
                    ->toMediaCollection('assignments');
            }

            return $assignment;
        });
    }
}
