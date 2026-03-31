<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassSession;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->student !== null;
    }

    protected function getStats(): array
    {
        $student = auth()->user()?->student;
        $studentId = $student?->id;

        if (! $studentId) {
            return [];
        }

        // Attendance Stats
        $presentCount = Attendance::where('student_id', $studentId)
            ->count();

        $totalSessions = ClassSession::where('date', '<=', now()->startOfDay())
            ->count();

        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;

        // Base Assignment Query (Assignments involving the student)
        $baseAssignmentQuery = Assignment::query()
            ->whereHas('assignmentTargets', function (Builder $query) use ($studentId) {
                $query->where('student_id', $studentId);
            });

        // Completed Assignments (Student has submitted)
        $completedAssignments = (clone $baseAssignmentQuery)->whereHas('assignmentSubmissions', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->count();

        // Pending Assignments (Not submitted yet)
        $pendingAssignments = (clone $baseAssignmentQuery)->whereDoesntHave('assignmentSubmissions', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->count();

        return [
            Stat::make('Kehadiran', $presentCount.' / '.$totalSessions.' Sesi')
                ->description($attendanceRate.'% Tingkat Kehadiran')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($attendanceRate >= 75 ? 'success' : 'danger'),

            Stat::make('Tugas Selesai', $completedAssignments.' Tugas')
                ->description('Telah dikumpulkan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Tugas Mendatang', $pendingAssignments.' Tugas')
                ->description($pendingAssignments > 0 ? 'Belum dikerjakan' : 'Semua tugas telah dikerjakan')
                ->descriptionIcon($pendingAssignments > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-face-smile')
                ->color($pendingAssignments > 0 ? 'warning' : 'gray'),
        ];
    }
}
