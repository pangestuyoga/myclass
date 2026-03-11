<?php

namespace App\Filament\Resources\Learning\Assignments\Pages;

use App\Enums\AssignmentType;
use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Livewire\Attributes\Computed;

class SubmissionDetailPage extends Page
{
    protected static string $resource = AssignmentResource::class;

    protected string $view = 'filament.resources.learning.assignments.pages.submission-detail';

    protected static ?string $title = 'Detail Rekap Pengumpulan';

    public Assignment $record;

    #[Computed]
    public function statCards(): array
    {
        return [
            [
                'label' => $this->record->type === AssignmentType::Individual ? 'Total Mahasiswa' : 'Total Kelompok',
                'value' => $this->totalCount,
                'icon' => 'heroicon-o-user-group',
                'color' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-900',
            ],
            [
                'label' => 'Sudah Kumpul',
                'value' => $this->doneCount,
                'icon' => 'heroicon-o-check-circle',
                'color' => 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-400',
            ],
            [
                'label' => 'Belum Kumpul',
                'value' => $this->totalCount - $this->doneCount,
                'icon' => 'heroicon-o-x-circle',
                'color' => 'bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400',
            ],
            [
                'label' => 'Persentase',
                'value' => $this->percentage.'%',
                'icon' => 'heroicon-o-chart-bar',
                'color' => 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400',
            ],
        ];
    }

    #[Computed]
    public function submissionSummary(): Collection
    {
        $assignment = $this->record->load(['students', 'studyGroups', 'course']);
        $isIndividual = $assignment->type === AssignmentType::Individual;

        if ($isIndividual) {
            $targets = $assignment->students->keyBy('id');

            $submissions = AssignmentSubmission::with('student')
                ->where('assignment_id', $assignment->id)
                ->get()
                ->keyBy('student_id');

            return $targets->map(function ($student) use ($submissions) {
                $submission = $submissions->get($student->id);
                $isSubmitted = $submission !== null;

                return (object) [
                    'id' => $student->id,
                    'is_individual' => true,
                    'primary_name' => $student->full_name,
                    'secondary_info' => $student->student_number,
                    'submission' => $submission,
                    'submitted' => $isSubmitted,
                    'submitted_at_formatted' => $isSubmitted ? $submission->submitted_at?->translatedFormat('l, d F Y, H:i') : '-',
                    'has_file' => $isSubmitted && $submission->hasMedia('submission'),
                ];
            })->sortBy('secondary_info')->values();
        } else {
            $targets = $assignment->studyGroups->keyBy('id');

            $submissions = AssignmentSubmission::with(['studyGroup', 'student'])
                ->where('assignment_id', $assignment->id)
                ->get()
                ->keyBy('study_group_id');

            return $targets->map(function ($group) use ($submissions) {
                $submission = $submissions->get($group->id);
                $isSubmitted = $submission !== null;

                return (object) [
                    'id' => $group->id,
                    'is_individual' => false,
                    'primary_name' => $group->name,
                    'secondary_info' => $isSubmitted ? $submission->student->full_name : '-',
                    'submission' => $submission,
                    'submitted' => $isSubmitted,
                    'submitted_at_formatted' => $isSubmitted ? $submission->submitted_at?->translatedFormat('l, d F Y, H:i') : '-',
                    'has_file' => $isSubmitted && $submission->hasMedia('submission'),
                ];
            })->sortBy('primary_name')->values();
        }
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->submissionSummary->count();
    }

    #[Computed]
    public function doneCount(): int
    {
        return $this->submissionSummary->where('submitted', true)->count();
    }

    #[Computed]
    public function percentage(): int
    {
        $total = $this->totalCount;

        return $total > 0 ? (int) round(($this->doneCount / $total) * 100) : 0;
    }

    #[Computed]
    public function isOverdue(): bool
    {
        return now()->isAfter($this->record->due_date);
    }

    public function getAssignmentInfo(): array
    {
        return [
            'title' => $this->record->title,
            'course' => $this->record->course?->name ?? '-',
            'due_date' => $this->record->due_date?->translatedFormat('l, d F Y, H:i'),
            'type' => $this->record->type->value,
            'is_overdue' => $this->isOverdue,
        ];
    }

    public function previewSubmissionAction(): Action
    {
        return Action::make('previewSubmission')
            ->record(fn (array $arguments) => AssignmentSubmission::find($arguments['submissionId']))
            ->modalHeading(fn (AssignmentSubmission $record) => 'File Tugas: '.($record->study_group_id ? $record->studyGroup->name : $record->student->full_name))
            ->modalWidth(Width::SixExtraLarge)
            ->infolist([
                PdfViewerEntry::make('submission')
                    ->hiddenLabel()
                    ->fileUrl(fn (AssignmentSubmission $record) => $record->getFirstMediaUrl('submission'))
                    ->columnSpanFull(),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup');
    }

    protected function getHeaderActions(): array
    {
        return [

            BackAction::make()
                ->url(AssignmentResource::getUrl()),
        ];
    }
}
