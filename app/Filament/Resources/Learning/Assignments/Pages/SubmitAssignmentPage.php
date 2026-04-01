<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class SubmitAssignmentPage extends Page
{
    use WithFileUploads;

    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.submit-assignment';

    protected static ?string $title = 'Kumpulkan Tugas';

    public Assignment $record;

    public $file = null;

    #[Computed]
    public function statusCards(): array
    {
        return [
            [
                'label' => 'Batas Waktu',
                'value' => $this->record->due_date?->translatedFormat('l, d F Y, H:i'),
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
                'value' => $this->isOverdue ? 'Ditutup' : 'Terbuka',
                'icon' => $this->isOverdue ? 'heroicon-o-lock-closed' : 'heroicon-o-check-circle',
                'is_danger' => $this->isOverdue,
                'is_success' => ! $this->isOverdue,
                'icon_classes' => Arr::toCssClasses([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                    'bg-danger-50 dark:bg-danger-900/20 text-danger-600' => $this->isOverdue,
                    'bg-success-50 dark:bg-success-900/20 text-success-600' => ! $this->isOverdue,
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

        return $this->record->studyGroups()
            ->where(fn ($q) => $q->where('leader_id', $student->id)->orWhereHas('students', fn ($sq) => $sq->whereKey($student->id)))
            ->first();
    }

    #[Computed]
    public function existingSubmission(): ?AssignmentSubmission
    {
        $student = $this->student;

        if ($this->record->type === AssignmentType::Group) {
            $group = $this->currentGroup;
            if (! $group) {
                return null;
            }

            return AssignmentSubmission::where('assignment_id', $this->record->id)
                ->where('study_group_id', $group->id)
                ->first();
        }

        return AssignmentSubmission::where('assignment_id', $this->record->id)
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
        return now()->isAfter($this->record->due_date);
    }

    #[Computed]
    public function canSubmit(): bool
    {
        if ($this->record->type === AssignmentType::Individual) {
            return true;
        }

        $group = $this->currentGroup;

        return $group && $group->leader_id === $this->student->id;
    }

    #[Computed]
    public function submissionStatus(): object
    {
        $existing = $this->existingSubmission;

        if ($this->isResubmit) {
            return (object) [
                'type' => 'submitted',
                'badge_color' => 'success',
                'badge_label' => 'Sudah Dikumpulkan',
            ];
        }

        return (object) [
            'type' => 'none',
            'badge_color' => 'danger',
            'badge_label' => 'Tidak Mengumpulkan',
        ];
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
            SystemNotification::danger(
                'Gagal Mengumpulkan 🚫',
                'Anda tidak terdaftar dalam kelompok manapun yang ditugaskan untuk tugas ini. 😟',
                'Kesalahan Pengumpulan Tugas',
                'Data akun Anda tidak ditemukan dalam daftar kelompok yang ditugaskan untuk tugas ini.'
            )->send();

            return;
        }

        if (! $this->canSubmit) {
            SystemNotification::danger(
                'Akses Ditolak 🚫',
                'Hanya ketua kelompok yang diizinkan untuk mengumpulkan atau memperbarui tugas kelompok. 👑',
                'Batasan Otoritas Pengumpulan',
                'Hanya ketua kelompok yang memiliki otoritas untuk memperbarui atau mengumpulkan tugas kelompok.'
            )->send();

            return;
        }

        $existingSubmission = $this->existingSubmission;

        if (! $existingSubmission && ! $this->file) {
            SystemNotification::warning(
                'Pilih File Dulu Dong! 📁',
                'Silakan pilih file untuk dikumpulkan agar sistem bisa menyimpannya ya! 😊',
                'Kelengkapan Berkas Diperlukan',
                'Mohon sertakan lampiran berkas sebelum melanjutkan proses pengumpulan tugas.'
            )->send();

            return;
        }

        $this->validate([
            'file' => [
                'nullable',
                'file',
                'max:'.(1024 * 5),
                'mimes:pdf',
            ],
        ], [
            'file.max' => 'Ukuran file maksimal 5MB.',
            'file.mimes' => 'Hanya file PDF yang diizinkan.',
        ]);

        if ($existingSubmission) {
            if ($this->file) {
                $existingSubmission->clearMediaCollection('submission');
                $existingSubmission->addMedia($this->file->getRealPath())
                    ->usingName($this->file->getClientOriginalName())
                    ->usingFileName($this->file->getClientOriginalName())
                    ->toMediaCollection('submission');
            }

            $existingSubmission->update([
                'submitted_at' => now(),
            ]);

            SystemNotification::success(
                'Keren! Tugas Sudah Update ✨',
                'File tugas Anda telah berhasil diunggah ulang dan diperbarui di sistem. 📤',
                'Pembaruan Berkas Berhasil',
                'Berkas tugas telah berhasil diunggah ulang dan divalidasi oleh sistem.'
            )->send();
        } else {
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'study_group_id' => $assignment->type === AssignmentType::Group ? $this->currentGroup?->id : null,
                'submitted_at' => now(),
            ]);

            $submission->addMedia($this->file->getRealPath())
                ->usingName($this->file->getClientOriginalName())
                ->usingFileName($this->file->getClientOriginalName())
                ->toMediaCollection('submission');

            SystemNotification::success(
                'Yeay! Tugas Terkumpul! 🎉',
                'Berhasil! Tugas Anda telah tercatat dengan aman di sistem. Semangat! 🎈',
                'Konfirmasi Pengumpulan Berhasil',
                'Seluruh berkas tugas Anda telah berhasil diverifikasi dan disimpan oleh sistem.'
            )->send();
        }

        $this->file = null;
        unset($this->existingSubmission, $this->isResubmit);

        $this->dispatch('submission-completed');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(AssignmentResource::getUrl()),
        ];
    }
}
