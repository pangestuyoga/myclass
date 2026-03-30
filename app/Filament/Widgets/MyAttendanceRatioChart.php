<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;

class MyAttendanceRatioChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Persentase Presensi';

    protected ?string $maxHeight = '275px';

    public static function canView(): bool
    {
        return auth()->user()?->student !== null;
    }

    protected function getData(): array
    {
        $studentId = auth()->user()?->student?->id;

        $presentCount = Attendance::where('student_id', $studentId)
            ->whereNotNull('attended_at')
            ->count();

        $absentCount = Attendance::where('student_id', $studentId)
            ->whereNull('attended_at')
            ->where('date', '<=', today())
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Presensi',
                    'data' => [$presentCount, $absentCount],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)', // Emerald 500
                        'rgba(244, 63, 94, 0.8)',  // Rose 500
                    ],
                    'borderColor' => [
                        'rgba(16, 185, 129, 1)',
                        'rgba(244, 63, 94, 1)',
                    ],
                ],
            ],
            'labels' => ['Hadir', 'Tidak / Belum Hadir'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
