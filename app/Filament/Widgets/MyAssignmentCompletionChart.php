<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class MyAssignmentCompletionChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Status Penyelesaian Tugas';

    protected ?string $maxHeight = '275px';

    public static function canView(): bool
    {
        return auth()->user()?->student !== null;
    }

    protected function getData(): array
    {
        $studentId = auth()->user()?->student?->id;

        $baseQuery = Assignment::query()
            ->whereHas('assignmentTargets', function (Builder $query) use ($studentId) {
                $query->where('student_id', $studentId)
                    ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                        $q->select('study_group_id')->from('study_group_members')->where('student_id', $studentId);
                    })
                    ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                        $q->select('id')->from('study_groups')->where('leader_id', $studentId);
                    });
            });

        $submittedCount = (clone $baseQuery)->whereHas('assignmentSubmissions', function (Builder $query) use ($studentId) {
            $query->where('student_id', $studentId)
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('study_group_id')->from('study_group_members')->where('student_id', $studentId);
                })
                ->orWhereIn('study_group_id', function ($q) use ($studentId) {
                    $q->select('id')->from('study_groups')->where('leader_id', $studentId);
                });
        })->count();

        $totalCount = $baseQuery->count();

        $notSubmittedCount = $totalCount - $submittedCount;

        return [
            'datasets' => [
                [
                    'label' => 'Tugas',
                    'data' => [$submittedCount, $notSubmittedCount],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)', // Blue 500
                        'rgba(245, 158, 11, 0.8)', // Amber 500
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 158, 11, 1)',
                    ],
                ],
            ],
            'labels' => ['Sudah Dikerjakan', 'Belum Dikerjakan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
