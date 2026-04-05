<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Resources\Learning\Assignments\AssignmentResource;
use App\Models\ClassSession;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;

class ViewAssignmentsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewAssignments';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tugas')
            ->color(Color::Sky)
            ->icon('heroicon-o-clipboard-document-list')
            ->outlined()
            ->modalHeading('Tugas Sesi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.assignment-modal', (function () use ($arguments) {
                $session = ClassSession::with([
                    'assignments.assignmentSubmissions',
                ])->find($arguments['session'] ?? null);

                if (! $session) {
                    return ['assignment' => null];
                }

                $assignment = $session->assignments?->first();

                if (! $assignment) {
                    return ['assignment' => null];
                }

                $activeStudents = Student::query()
                    ->whereHas('user', fn ($q) => $q->active())
                    ->orderBy('full_name')
                    ->get();

                $submissionMap = $assignment->assignmentSubmissions?->keyBy('student_id');

                $data = (object) [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'type_label' => $assignment->type?->getLabel() ?? 'Tugas',
                    'due_date_formatted' => $assignment->due_date?->translatedFormat('d F Y H:i') ?? '-',
                    'submissions' => $activeStudents->map(fn ($student) => (object) [
                        'student_name' => $student->full_name,
                        'student_number' => $student->student_number,
                        'submitted_at_formatted' => $submissionMap->get($student->id)?->submitted_at?->translatedFormat('d F Y H:i'),
                        'is_submitted' => $submissionMap->has($student->id),
                    ]),
                    'submission_count' => $submissionMap->count(),
                    'total_students' => $activeStudents->count(),
                    'url' => AssignmentResource::getUrl('submit', ['record' => $assignment->id]),
                ];

                return ['assignment' => $data];
            })()));
    }
}
