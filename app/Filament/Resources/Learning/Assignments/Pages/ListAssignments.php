<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Resources\Learning\Assignments\Schemas\AssignmentForm;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentPin;
use App\Models\AssignmentTarget;
use App\Models\StudyGroup;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class ListAssignments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.pages.list-assignments';

    protected static ?string $title = 'Tugas';

    #[Computed]
    public function assignments(): Collection
    {
        $studentProfile = auth()->user()->student;

        $pinnedIds = $this->pinnedIds;

        return Assignment::with(['course', 'assignmentSubmissions' => function ($q) use ($studentProfile) {
            $q->where('student_id', $studentProfile->id)
                ->orWhereHas('studyGroup', fn ($sq) => $sq->whereHas('students', fn ($ssq) => $ssq->whereKey($studentProfile->id)));
        }, 'studyGroups.students'])
            ->whereHas('course', function ($q) {
                $q->where('semester', app(GeneralSettings::class)->current_semester);
            })
            ->where(function ($query) use ($studentProfile) {
                $query->whereHas('students', fn ($q) => $q->whereKey($studentProfile->id))
                    ->orWhereHas('studyGroups', fn ($q) => $q->whereHas('students', fn ($sq) => $sq->whereKey($studentProfile->id)));
            })
            ->when(! empty($pinnedIds), function ($query) use ($pinnedIds) {
                $ids = implode(',', $pinnedIds);
                $query->orderByRaw("FIELD(id, $ids) DESC");
            })
            ->orderBy('due_date')
            ->get();
    }

    #[Computed]
    public function assignmentCards()
    {
        $studentProfile = auth()->user()->student;
        $pinnedIds = $this->pinnedIds;

        return $this->assignments()->map(function ($assignment) use ($studentProfile, $pinnedIds) {
            $submission = $assignment->assignmentSubmissions->first();
            $isSubmitted = $submission !== null;
            $isGroup = $assignment->type === \App\Enums\AssignmentType::Group;

            $isLeader = false;
            if ($isGroup) {
                $userGroup = $assignment->studyGroups->first(function ($g) use ($studentProfile) {
                    return $g->students->contains($studentProfile->id);
                });
                $isLeader = $userGroup && $userGroup->leader_id === $studentProfile->id;
            }

            $isOverdue = now()->isAfter($assignment->due_date);
            $canSubmit = ! $isOverdue;

            $canSubmitByRole = ! $isGroup || $isLeader;
            $canSubmitActual = $canSubmit && $canSubmitByRole;

            $isUrgent = ! $isSubmitted && $canSubmitActual && now()->diffInHours($assignment->due_date) <= 48;
            $isNew = $assignment->created_at->diffInDays(now()) <= 3;
            $isPinned = in_array($assignment->id, $pinnedIds);

            $statusLabel = 'Belum Dikumpulkan';
            $statusColor = 'warning';
            $statusIcon = 'heroicon-o-arrow-up-tray';

            if ($isSubmitted) {
                $statusLabel = '✓ Sudah Dikumpulkan';
                $statusColor = 'success';
                $statusIcon = 'heroicon-o-check-circle';
            } elseif ($isOverdue) {
                $statusLabel = '⏰ Waktu Habis';
                $statusColor = 'danger';
                $statusIcon = 'heroicon-o-clock';
            } elseif ($isGroup && ! $isLeader && ! $isSubmitted) {
                $statusLabel = 'Menunggu Ketua';
                $statusColor = 'gray';
                $statusIcon = 'heroicon-o-user-group';
            }

            return (object) [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'course_name' => $assignment->course?->name ?? '-',
                'due_date_formatted' => $assignment->due_date->translatedFormat('l, d F Y, H:i'),
                'submitted_at_formatted' => ($isSubmitted && $submission->submitted_at) ? $submission->submitted_at->translatedFormat('l, d F Y, H:i') : null,
                'is_submitted' => $isSubmitted,
                'is_group' => $isGroup,
                'is_leader' => $isLeader,
                'is_overdue' => $isOverdue,
                'can_submit_actual' => $canSubmitActual,
                'is_urgent' => $isUrgent,
                'is_new' => $isNew,
                'is_pinned' => $isPinned,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'status_icon' => $statusIcon,
            ];
        });
    }

    #[Computed]
    public function pinnedIds(): array
    {
        $studentProfile = auth()->user()->student;

        return AssignmentPin::where('student_id', $studentProfile->id)
            ->pluck('assignment_id')
            ->toArray();
    }

    public function togglePin(int $assignmentId): void
    {
        $studentProfile = auth()->user()->student;

        $existing = AssignmentPin::where('student_id', $studentProfile->id)
            ->where('assignment_id', $assignmentId)
            ->first();

        if ($existing) {
            $existing->delete();
            SystemNotification::success('Pin Tugas Dilepas 📌', 'Pin pada tugas ini telah berhasil dilepas dari daftar prioritas Anda.')->send();
        } else {
            AssignmentPin::create([
                'student_id' => $studentProfile->id,
                'assignment_id' => $assignmentId,
            ]);
            SystemNotification::success('Tugas Berhasil Di-pin 📍', 'Tugas ini sekarang berada di posisi teratas daftar prioritas Anda.')->send();
        }

        unset($this->assignments, $this->pinnedIds);
    }

    public function editAssignmentAction(): Action
    {
        return EditAction::make('editAssignment')
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

    public function deleteAssignmentAction(): Action
    {
        return DeleteAction::make('deleteAssignment')
            ->record(fn (array $arguments) => Assignment::find($arguments['record']))
            ->after(function () {
                unset($this->assignments);
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
