<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\Attendance;
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
        $studentId = auth()->user()?->student?->id;

        if (! $studentId) {
            return [];
        }

        $presentCount = Attendance::where('student_id', $studentId)
            ->whereNotNull('attended_at')
            ->count();
        $totalSessions = Attendance::where('student_id', $studentId)->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : 0;

        $baseAssignmentQuery = Assignment::query()
            ->whereHas('assignmentTargets', function (Builder $query) use ($studentId) {
                $query->where('student_id', $studentId)
                    ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                        $q->select('study_group_id')->from('study_group_members')->where('student_id', $studentId);
                    })
                    ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                        $q->select('id')->from('study_groups')->where('leader_id', $studentId);
                    });
            });

        $completedAssignments = (clone $baseAssignmentQuery)->whereHas('assignmentSubmissions', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId)
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('study_group_id')->from('study_group_members')->where('student_id', $studentId);
                })
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('id')->from('study_groups')->where('leader_id', $studentId);
                });
        })->count();

        $pendingAssignments = (clone $baseAssignmentQuery)->whereDoesntHave('assignmentSubmissions', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId)
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('study_group_id')->from('study_group_members')->where('student_id', $studentId);
                })
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('id')->from('study_groups')->where('leader_id', $studentId);
                });
        })
            ->where('due_date', '>=', now())
            ->count();

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
                ->description('Belum dikerjakan')
                ->descriptionIcon($pendingAssignments > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-face-smile')
                ->color($pendingAssignments > 0 ? 'warning' : 'gray'),
        ];
    }
}
