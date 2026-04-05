<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;

class ViewAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Presensi')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->link()
            ->modalHeading('Daftar Presensi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.attendance-modal', (function () use ($arguments) {
                $session = ClassSession::with([
                    'attendances.student',
                ])->find($arguments['session'] ?? null);

                if (! $session) {
                    return ['students' => collect(), 'attendedCount' => 0];
                }

                $activeStudents = Student::query()
                    ->whereHas('user', fn ($q) => $q->active())
                    ->orderBy('full_name')
                    ->get();

                $attendanceMap = $session->attendances?->keyBy('student_id');

                $students = $activeStudents->map(fn ($student) => (object) [
                    'student' => $student,
                    'attended_at' => $attendanceMap->get($student->id)?->attended_at?->translatedFormat('d F Y H:i'),
                    'has_attended' => $attendanceMap->has($student->id),
                ]);

                return [
                    'students' => $students,
                    'attendedCount' => $attendanceMap->count(),
                ];
            })()));
    }
}
