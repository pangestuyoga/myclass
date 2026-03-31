<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Resources\Learning\Assignments\Actions\CreateAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\DeleteAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\EditAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\PinAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentPin;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

class ListAssignments extends Page
{
    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.index';

    protected static ?string $title = 'Tugas';

    protected static ?string $recordTitleAttribute = 'title';

    protected function getHeaderActions(): array
    {
        return [
            CreateAssignmentAction::make(),
        ];
    }

    #[Computed]
    public function heading(): string
    {
        return SystemNotification::getMessage('Daftar Tugas Bikin Lemes! 🤣📒', 'Tugas Saya');
    }

    #[Computed]
    public function description(): string
    {
        return SystemNotification::getMessage('Yuk, cek dan kumpulin tugasmu biar tenang hidupnya! Klik aja di tugasnya ya. 🚀👨‍💻', 'Klik pada tugas untuk melihat detail dan mengumpulkan file.');
    }

    public function pinAction(): Action
    {
        return PinAction::make();
    }

    public function editAssignmentAction(): Action
    {
        return EditAssignmentAction::make()
            ->visible(fn () => auth()->user()->can('Update:Assignment'));
    }

    public function deleteAssignmentAction(): Action
    {
        return DeleteAssignmentAction::make()
            ->visible(fn () => auth()->user()->can('Delete:Assignment'));
    }

    #[Computed]
    public function assignments(): Collection
    {
        $studentProfile = auth()->user()->student;

        if (! $studentProfile) {
            return Assignment::with(['course', 'assignmentSubmissions', 'studyGroups.students'])
                ->whereHas('course', function ($q) {
                    $q->where('semester', app(GeneralSettings::class)->current_semester);
                })
                ->get();
        }

        $pinnedIds = $this->pinnedIds;

        $assignments = Assignment::with(['course', 'assignmentSubmissions' => function ($q) use ($studentProfile) {
            $q->where('student_id', $studentProfile->id)
                ->orWhereHas('studyGroup', fn ($sq) => $sq->where('leader_id', $studentProfile->id)->orWhereHas('students', fn ($ssq) => $ssq->whereKey($studentProfile->id)));
        }, 'studyGroups.students'])
            ->whereHas('course', function ($q) {
                $q->where('semester', app(GeneralSettings::class)->current_semester);
            })
            ->where(function ($query) use ($studentProfile) {
                $query->whereHas('students', fn ($q) => $q->whereKey($studentProfile->id))
                    ->orWhereHas('studyGroups', fn ($q) => $q->where('leader_id', $studentProfile->id)->orWhereHas('students', fn ($sq) => $sq->whereKey($studentProfile->id)));
            })
            ->get();

        return $assignments->sort(function ($a, $b) use ($pinnedIds) {
            if (! empty($pinnedIds)) {
                $aIndex = array_search($a->id, $pinnedIds);
                $bIndex = array_search($b->id, $pinnedIds);

                $aPinned = $aIndex !== false;
                $bPinned = $bIndex !== false;

                if ($aPinned && $bPinned) {
                    return $bIndex <=> $aIndex;
                }
                if ($aPinned) {
                    return -1;
                }
                if ($bPinned) {
                    return 1;
                }
            }

            $aPriority = $this->getAssignmentPriority($a);
            $bPriority = $this->getAssignmentPriority($b);

            if ($aPriority !== $bPriority) {
                return $aPriority <=> $bPriority;
            }

            return $a->due_date <=> $b->due_date;
        });
    }

    private function getAssignmentPriority($assignment): int
    {
        $isSubmitted = $assignment->assignmentSubmissions->isNotEmpty();
        $isOverdue = now()->isAfter($assignment->due_date);

        if (! $isOverdue) {
            return $isSubmitted ? 2 : 1;
        }

        // Overdue
        return $isSubmitted ? 3 : 4;
    }

    #[Computed]
    public function assignmentCards()
    {
        $studentProfile = auth()->user()->student;

        if (! $studentProfile) {
            return collect();
        }

        $pinnedIds = $this->pinnedIds;

        return $this->assignments()->map(function ($assignment) use ($studentProfile, $pinnedIds) {
            $submission = $assignment->assignmentSubmissions->first();
            $isSubmitted = $submission !== null;
            $isGroup = $assignment->type === AssignmentType::Group;

            $isLeader = false;
            if ($isGroup) {
                $userGroup = $assignment->studyGroups->first(function ($g) use ($studentProfile) {
                    return $g->leader_id === $studentProfile->id || $g->students->contains($studentProfile->id);
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
                'status_icon' => $statusIcon,
                // Pre-calculated classes
                'card_classes' => Arr::toCssClasses([
                    'group flex w-full rounded-xl border transition-all overflow-hidden relative',
                    'border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm' => $isPinned,
                    'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' => ! $isPinned && $canSubmitActual,
                    'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 opacity-80' => ! $canSubmitActual,
                ]),
                'icon_wrapper_classes' => Arr::toCssClasses([
                    'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg',
                    'bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400' => $isSubmitted,
                    'bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400' => ! $isSubmitted && $isOverdue,
                    'bg-warning-50 dark:bg-warning-900/20 text-warning-600 dark:text-warning-400' => ! $isSubmitted && ! $isOverdue,
                ]),
                'status_badge_classes' => Arr::toCssClasses([
                    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium',
                    'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' => $statusColor === 'amber',
                    'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' => $statusColor === 'success',
                    'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400' => $statusColor === 'danger',
                    'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' => $statusColor === 'warning',
                    'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' => $statusColor === 'gray',
                ]),
                'indicator_icon_classes' => Arr::toCssClasses([
                    'h-5 w-5 opacity-20',
                    'text-primary-500' => $isLeader,
                    'text-gray-400' => ! $isLeader,
                ]),
            ];
        });
    }

    #[Computed]
    public function pinnedIds(): array
    {
        $studentProfile = auth()->user()->student;

        if (! $studentProfile) {
            return [];
        }

        return AssignmentPin::where('student_id', $studentProfile->id)
            ->pluck('assignment_id')
            ->toArray();
    }
}
