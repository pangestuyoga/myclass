<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Enums\NotifStyle;
use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Resources\Learning\Assignments\Schemas\SubmitAssignmentForm;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class SubmitAssignmentPage extends Page implements HasForms
{
    use InteractsWithForms, WithFileUploads;

    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.submit-assignment';

    protected static ?string $title = 'Kumpulkan Tugas';

    public Assignment $record;

    public array $data = [];

    public $file;

    public function mount(Assignment $record): void
    {
        $this->record = $record;

        $this->form?->fill([
            'is_resubmit' => $this->isResubmit,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return SubmitAssignmentForm::configure($schema)
            ->statePath('data');
    }

    #[Computed]
    public function statusCards(): array
    {
        $isSent = $this->record->is_sent_to_lecturer;
        $existing = $this->existingSubmission;

        return [
            [
                'label' => 'Batas Waktu',
                'value' => $this->record?->due_date?->translatedFormat('l, d F Y H:i'),
                'icon' => 'heroicon-o-clock',
                'is_danger' => $this->isOverdue,
                'badge' => $this->isOverdue ? '(Terlewat)' : null,
                'icon_classes' => Arr::toCssClasses([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                    'bg-danger-50 dark:bg-danger-900/20 text-danger-600' => $this->isOverdue,
                    'bg-warning-50 dark:bg-warning-900/20 text-warning-600' => ! $this->isOverdue,
                ]),
            ],
            [
                'label' => 'Status Pengumpulan',
                'value' => $existing ? 'Sudah Dikumpulkan' : ($isSent ? 'Terlambat & Terkunci' : ($this->isOverdue ? 'Belum (Masih Terbuka)' : 'Belum Dikumpulkan')),
                'icon' => $existing ? 'heroicon-o-check-circle' : ($isSent ? 'heroicon-o-lock-closed' : 'heroicon-o-arrow-up-tray'),
                'is_success' => (bool) $existing,
                'is_danger' => ! $existing && $isSent,
                'is_warning' => ! $existing && ! $isSent && $this->isOverdue,
                'icon_classes' => Arr::toCssClasses([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                    'bg-success-50 dark:bg-success-900/20 text-success-600' => $existing,
                    'bg-danger-50 dark:bg-danger-900/20 text-danger-600' => ! $existing && $isSent,
                    'bg-warning-50 dark:bg-warning-900/20 text-warning-600' => ! $existing && ! $isSent && $this->isOverdue,
                ]),
            ],
            [
                'label' => 'Status ke Dosen',
                'value' => $isSent ? 'Telah Dikirim' : 'Belum Dikirim',
                'icon' => $isSent ? 'heroicon-o-paper-airplane' : 'heroicon-o-clock',
                'is_success' => $isSent,
                'badge' => $isSent ? '(Kunci Aktif)' : '(Masih Terbuka)',
                'icon_classes' => Arr::toCssClasses([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                    'bg-success-50 dark:bg-success-900/20 text-success-600' => $isSent,
                    'bg-gray-100 dark:bg-gray-800 text-gray-500' => ! $isSent,
                ]),
            ],
        ];
    }

    #[Computed]
    public function student(): Student
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->student;
    }

    #[Computed]
    public function currentGroup(): ?StudyGroup
    {
        $student = $this->student;

        return $this->record?->studyGroups()
            ->where(fn ($q) => $q->where('leader_id', $student->id)->orWhereHas('students', fn ($sq) => $sq->whereKey($student->id)))
            ->first();
    }

    #[Computed]
    public function existingSubmission(): ?AssignmentSubmission
    {
        $student = $this->student;

        if ($this->record?->type === AssignmentType::Group) {
            $group = $this->currentGroup;
            if (! $group) {
                return null;
            }

            return AssignmentSubmission::where('assignment_id', $this->record?->id)
                ->where('study_group_id', $group->id)
                ->first();
        }

        return AssignmentSubmission::where('assignment_id', $this->record?->id)
            ->where('student_id', $student->id)
            ->first();
    }

    #[Computed]
    public function isResubmit(): bool
    {
        return $this->existingSubmission !== null;
    }

    #[Computed]
    public function isOverdue(): bool
    {
        return now()->isAfter($this->record?->due_date);
    }

    #[Computed]
    public function canSubmit(): bool
    {
        if ($this->record?->type === AssignmentType::Individual) {
            return true;
        }

        $group = $this->currentGroup;

        return $group && $group->leader_id === $this->student?->id;
    }

    #[Computed]
    public function submissionStatus(): object
    {
        $existing = $this->existingSubmission;

        if ($this->isResubmit) {
            return (object) [
                'type' => 'submitted',
                'badge_color' => 'success',
                'badge_label' => SystemNotification::getByKey('labels.assignment_status.submitted'),
            ];
        }

        return (object) [
            'type' => 'none',
            'badge_color' => 'danger',
            'badge_label' => SystemNotification::getByKey('labels.assignment_status.not_submitted'),
        ];
    }

    #[Computed]
    public function overdueHeading(): string
    {
        return SystemNotification::getByKey('submission_overdue.title');
    }

    #[Computed]
    public function overdueDescription(): string
    {
        return SystemNotification::getByKey('submission_overdue.body');
    }

    #[Computed]
    public function submissionHeading(): string
    {
        return $this->isResubmit
            ? SystemNotification::getByKey('submission_update.title')
            : SystemNotification::getByKey('submission_create.title');
    }

    #[Computed]
    public function submissionDescription(): string
    {
        return $this->isResubmit
            ? SystemNotification::getByKey('submission_update.body')
            : SystemNotification::getByKey('submission_create.body');
    }

    #[Computed]
    public function isCheerful(): bool
    {
        return SystemNotification::getNotifStyle() === NotifStyle::Cheerful;
    }

    #[Computed]
    public function groupHintTitle(): string
    {
        return SystemNotification::getByKey('labels.group_submission_hint.title');
    }

    #[Computed]
    public function groupHintDescription(): string
    {
        return SystemNotification::getByKey('labels.group_submission_hint.description');
    }

    #[Computed]
    public function overdueIcon(): string
    {
        return $this->isCheerful ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-lock-closed';
    }

    #[Computed]
    public function submissionIcon(): string
    {
        return $this->isCheerful ? 'heroicon-o-paper-airplane' : 'heroicon-o-document-arrow-up';
    }

    public function getSubmissionFileUrl(): ?string
    {
        return $this->existingSubmission?->getFirstMediaUrl('submission');
    }

    public function submit(): void
    {
        $student = $this->student;
        $assignment = $this->record;

        if ($assignment->type === AssignmentType::Group && ! $this->currentGroup) {
            SystemNotification::send('submission_no_group', type: 'danger')
                ->send();

            return;
        }

        if ($assignment->is_sent_to_lecturer) {
            SystemNotification::send('submission_locked', type: 'danger')
                ->send();

            return;
        }

        if (! $this->canSubmit) {
            SystemNotification::send('submission_not_leader', type: 'danger')
                ->send();

            return;
        }

        $existingSubmission = $this->existingSubmission;
        $state = $this->form?->getState();

        if (! $existingSubmission && empty($state['file'])) {
            SystemNotification::send('submission_file_missing', type: 'warning')
                ->send();

            return;
        }

        if ($existingSubmission) {
            if ($state['file']) {
                $existingSubmission->clearMediaCollection('submission');
                $existingSubmission->addMediaFromDisk($state['file'], config('filesystems.default'))
                    ->withCustomProperties([
                        'feature' => 'assignments',
                        'date' => now()->toDateString(),
                        'doc_type' => 'submission',
                    ])
                    ->toMediaCollection('submission');
            }

            $existingSubmission->update([
                'submitted_at' => now(),
            ]);

            SystemNotification::send('submission_updated')
                ->send();
        } else {
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'study_group_id' => $assignment->type === AssignmentType::Group ? $this->currentGroup?->id : null,
                'submitted_at' => now(),
            ]);

            if ($state['file']) {
                $submission->addMediaFromDisk($state['file'], config('filesystems.default'))
                    ->withCustomProperties([
                        'feature' => 'assignments',
                        'date' => now()->toDateString(),
                        'doc_type' => 'submission',
                    ])
                    ->toMediaCollection('submission');
            }

            SystemNotification::send('submission_success')
                ->send();
        }

        $this->form?->fill([
            'is_resubmit' => $this->isResubmit,
        ]);
        unset($this->existingSubmission, $this->isResubmit);

        $this->dispatch('submission-completed');
    }

    protected function getHeaderActions(): array
    {

        return [
            BackAction::make()
                ->url(url()->previous() !== AssignmentResource::getUrl('submit', ['record' => $this->record->id]) ? url()->previous() : AssignmentResource::getUrl('index')),
        ];
    }
}
