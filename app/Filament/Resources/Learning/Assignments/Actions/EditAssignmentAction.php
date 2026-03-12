<?php

namespace App\Filament\Resources\Learning\Assignments\Actions;

use App\Enums\AssignmentType;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Resources\Learning\Assignments\Schemas\AssignmentForm;
use App\Models\Assignment;
use App\Models\AssignmentTarget;
use App\Models\StudyGroup;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class EditAssignmentAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editAssignmentAction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-pencil-square')
            ->color('warning')
            ->tooltip('Ubah')
            ->size('sm')
            ->iconButton()
            ->record(fn (array $arguments) => Assignment::find($arguments['record']))
            ->modalHeading(fn (Assignment $record) => "Ubah {$record->title}")
            ->modalWidth(Width::FourExtraLarge)
            ->schema(fn (Schema $schema) => AssignmentForm::configure($schema)->getComponents())
            ->fillForm(function (Assignment $record): array {
                $data = $record->toArray();

                $data['student_ids'] = $record->assignmentTargets()
                    ->whereNotNull('student_id')
                    ->pluck('student_id')
                    ->toArray();

                $data['study_group_ids'] = $record->assignmentTargets()
                    ->whereNotNull('study_group_id')
                    ->pluck('study_group_id')
                    ->toArray();

                $media = $record->getMedia('assignments')->last();
                $relativePath = $media ? $media->getPathRelativeToRoot() : null;
                $data['pdf'] = $relativePath ? [$relativePath] : [];

                return $data;
            })
            ->using(function (Assignment $record, array $data): Assignment {
                return DB::transaction(function () use ($record, $data) {
                    $currentMedia = $record->getMedia('assignments')->last();
                    $currentPath = $currentMedia ? $currentMedia->getPathRelativeToRoot() : null;

                    $studentIds = $data['student_ids'] ?? [];
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
                                ->preservingOriginal()
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
            })
            ->after(function () {
                unset($this->assignments);
            });
    }
}
