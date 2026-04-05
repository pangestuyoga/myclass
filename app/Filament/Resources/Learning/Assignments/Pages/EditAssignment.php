<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Support\SystemNotification;
use App\Models\AssignmentTarget;
use App\Models\StudyGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditAssignment extends EditRecord
{
    protected static string $resource = AssignmentResource::class;

    protected ?string $heading = 'Ubah Tugas';

    protected static ?string $title = 'Ubah Tugas';

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

    protected function getSavedNotification(): ?Notification
    {
        return SystemNotification::update();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['student_ids'] = $this->record?->assignmentTargets()
            ->whereNotNull('student_id')
            ->pluck('student_id')
            ->toArray();

        $data['study_group_ids'] = $this->record?->assignmentTargets()
            ->whereNotNull('study_group_id')
            ->pluck('study_group_id')
            ->toArray();

        $media = $this->record?->getMedia('assignments')->last();
        $relativePath = $media ? $media->getPathRelativeToRoot() : null;

        $data['pdf'] = $relativePath ? [$relativePath] : [];

        return $data;
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $currentMedia = $record->getMedia('assignments')->last();
            $currentPath = $currentMedia ? $currentMedia->getPathRelativeToRoot() : null;

            $studentIds = $data['student_ids'] ?? [];
            $studyGroupIds = $data['study_group_ids'] ?? [];
            $pdf = $data['pdf'] ?? null;

            unset($data['student_ids'], $data['study_group_ids'], $data['pdf']);

            $record->update($data);

            $record->assignmentTargets()->delete();

            if ($record->type === AssignmentType::Individual) {
                foreach ($studentIds as $studentId) {
                    AssignmentTarget::create([
                        'assignment_id' => $record->id,
                        'student_id' => $studentId,
                    ]);
                }
            } else {
                $studyGroupIds = StudyGroup::whereHas('courses', function ($q) use ($record) {
                    $q->where('courses.id', $record->course_id);
                })->pluck('id');

                foreach ($studyGroupIds as $groupId) {
                    AssignmentTarget::create([
                        'assignment_id' => $record->id,
                        'study_group_id' => $groupId,
                    ]);
                }
            }

            $filePath = is_array($pdf) ? reset($pdf) : $pdf;

            if ($filePath !== $currentPath) {
                $record->clearMediaCollection('assignments');

                if (! empty($filePath)) {
                    $record->addMediaFromDisk($filePath, config('filesystems.default'))
                        ->withCustomProperties([
                            'feature' => 'assignments',
                            'date' => now()->toDateString(),
                            'doc_type' => 'tasks',
                        ])
                        ->toMediaCollection('assignments');
                }
            }

            return $record;
        });
    }
}
