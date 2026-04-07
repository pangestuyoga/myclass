<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Enums\NotifStyle;
use App\Filament\Resources\Learning\Assignments\Actions\CreateAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\DeleteAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\EditAssignmentAction;
use App\Filament\Resources\Learning\Assignments\Actions\MarkAsSentAction;
use App\Filament\Resources\Learning\Assignments\Actions\PinAction;
use App\Filament\Resources\Learning\Assignments\Actions\ShareAssignmentAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentPin;
use App\Models\Course;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class ListAssignments extends Page
{
    #[Url]
    public ?string $search = '';

    public int $perPage = 12;

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    #[Url]
    public ?int $course_id = null;

    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.index';

    protected static ?string $title = 'Tugas';

    public static function getPagePermission(): string
    {
        return 'View:CourseSchedule';
    }

    public function mount(): void
    {
        $this->form?->fill([
            'search' => $this->search,
            'course_id' => $this->course_id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('search')
                    ->label('Cari')
                    ->placeholder('Cari Judul Tugas...')
                    ->autocomplete(false)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state) {
                        $this->search = $state;
                        $this->perPage = 12;
                    }),

                Select::make('course_id')
                    ->label('Mata Kuliah')
                    ->placeholder('Semua Mata Kuliah')
                    ->options(function () {
                        return Course::query()
                            ->where('semester', app(GeneralSettings::class)->current_semester)
                            ->whereHas('courseSchedules')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->course_id = $state;
                        $this->perPage = 12;
                    }),
            ])
            ->columns([
                'sm' => 2,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAssignmentAction::make(),
        ];
    }

    #[Computed]
    public function heading(): string
    {
        return SystemNotification::getByKey('labels.assignment_list.title');
    }

    #[Computed]
    public function description(): string
    {
        return SystemNotification::getByKey('labels.assignment_list.description');
    }

    #[Computed]
    public function emptyStateHeading(): string
    {
        return SystemNotification::getByKey('labels.empty_assignment.title');
    }

    #[Computed]
    public function emptyStateDescription(): string
    {
        return SystemNotification::getByKey('labels.empty_assignment.description');
    }

    #[Computed]
    public function icon(): string
    {
        return SystemNotification::getNotifStyle() === NotifStyle::Cheerful
            ? 'heroicon-o-clipboard-document-list'
            : 'heroicon-o-briefcase';
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

    public function shareAssignmentAction(): Action
    {
        return ShareAssignmentAction::make();
    }

    public function markAsSentAction(): Action
    {
        return MarkAsSentAction::make();
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
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', "%{$this->search}%")
                            ->orWhereHas('course', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%"));
                    });
                })
                ->when($this->course_id, function ($query) {
                    $query->where('course_id', $this->course_id);
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
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('course', fn ($cq) => $cq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->course_id, function ($query) {
                $query->where('course_id', $this->course_id);
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
        $isSent = $assignment->is_sent_to_lecturer;
        if ($isSent) {
            return 5; // Closed
        }

        $isSubmitted = $assignment->assignmentSubmissions?->isNotEmpty();
        $isOverdue = now()->isAfter($assignment->due_date);

        if (! $isOverdue) {
            return $isSubmitted ? 3 : 1; // 1: Active, 3: Completed Active
        }

        // Overdue but not sent
        return $isSubmitted ? 4 : 2; // 2: Actionable Overdue, 4: Completed Overdue
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
            $submission = $assignment->assignmentSubmissions?->first();
            $isSubmitted = $submission !== null;
            $isGroup = $assignment->type === AssignmentType::Group;

            $isLeader = false;
            if ($isGroup) {
                $userGroup = $assignment->studyGroups?->first(function ($g) use ($studentProfile) {
                    return $g->leader_id === $studentProfile->id || $g->students?->contains($studentProfile->id);
                });
                $isLeader = $userGroup && $userGroup->leader_id === $studentProfile->id;
            }

            $isSentToLecturer = $assignment->is_sent_to_lecturer;
            $isOverdue = now()->isAfter($assignment->due_date);

            $canSubmit = ! $isSentToLecturer;

            $canSubmitByRole = ! $isGroup || $isLeader;
            $canSubmitActual = $canSubmit && $canSubmitByRole;

            $isUrgent = ! $isSubmitted && $canSubmitActual && now()->diffInHours($assignment->due_date) <= 48;
            $isNew = $assignment->created_at?->diffInDays(now()) <= 3;
            $isPinned = in_array($assignment->id, $pinnedIds);

            if ($isSubmitted) {
                $submissionLabel = SystemNotification::getByKey('labels.assignment_status.submitted');
                $submissionColor = 'success';
                $submissionIcon = 'heroicon-o-check-circle';
            } elseif ($isOverdue) {
                $submissionLabel = SystemNotification::getByKey('labels.assignment_status.overdue');
                $submissionColor = 'danger';
                $submissionIcon = 'heroicon-o-clock';
            } elseif ($isGroup && ! $isLeader && ! $isSubmitted) {
                $submissionLabel = SystemNotification::getByKey('labels.assignment_status.waiting_leader');
                $submissionColor = 'gray';
                $submissionIcon = 'heroicon-o-user-group';
            } else {
                $submissionLabel = SystemNotification::getByKey('labels.assignment_status.not_submitted');
                $submissionColor = 'warning';
                $submissionIcon = 'heroicon-o-arrow-up-tray';
            }

            $deliveryLabel = $isSentToLecturer ? 'Telah Dikirim' : 'Belum Dikirim';
            $deliveryColor = $isSentToLecturer ? 'success' : 'amber';
            $deliveryIcon = $isSentToLecturer ? 'heroicon-o-check-badge' : 'heroicon-o-clock';

            return (object) [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'course_name' => $assignment->course?->name ?? '-',
                'due_date_formatted' => $assignment->due_date?->translatedFormat('l, d F Y H:i'),
                'submitted_at_formatted' => ($isSubmitted && $submission->submitted_at) ? $submission->submitted_at?->translatedFormat('l, d F Y H:i') : null,
                'is_sent_to_lecturer' => $isSentToLecturer,
                'is_submitted' => $isSubmitted,
                'is_group' => $isGroup,
                'is_leader' => $isLeader,
                'is_overdue' => $isOverdue,
                'can_submit_actual' => $canSubmitActual,
                'is_urgent' => $isUrgent,
                'is_new' => $isNew,
                'is_pinned' => $isPinned,
                'submission_status' => (object) [
                    'label' => $submissionLabel,
                    'color' => $submissionColor,
                    'icon' => $submissionIcon,
                    'classes' => Arr::toCssClasses([
                        'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold', // font-semibold for clarity
                        'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' => $submissionColor === 'success',
                        'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400' => $submissionColor === 'danger',
                        'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' => $submissionColor === 'warning',
                        'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' => $submissionColor === 'gray',
                    ]),
                ],
                'delivery_status' => (object) [
                    'label' => $deliveryLabel,
                    'color' => $deliveryColor,
                    'icon' => $deliveryIcon,
                    'classes' => Arr::toCssClasses([
                        'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset', // font-bold and ring for "delivery" status to make it pop
                        'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-900/10 dark:text-success-400 dark:ring-success-500/20' => $deliveryColor === 'success',
                        'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/10 dark:text-amber-400 dark:ring-amber-500/20' => $deliveryColor === 'amber',
                    ]),
                ],
                'url' => AssignmentResource::getUrl('submit', ['record' => $assignment->id]),
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
