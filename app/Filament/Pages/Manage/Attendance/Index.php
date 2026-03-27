<?php

namespace App\Filament\Pages\Manage\Attendance;

use App\Models\Course;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class Index extends Page
{
    use HasPageShield;

    public static function getPagePermission(): string
    {
        return 'View:AttendanceMonitoring';
    }

    protected static string|UnitEnum|null $navigationGroup = 'Kelola';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Monitoring Presensi';

    protected static ?string $title = 'Monitoring Presensi';

    protected static ?string $slug = 'manage/attendance-monitoring';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.manage.attendance.index';

    protected function getViewData(): array
    {
        return [
            'courses' => $this->getCourses(),
        ];
    }

    private function getCourses(): Collection
    {
        $semester = app(GeneralSettings::class)->current_semester;
        $today = now()->dayOfWeekIso;

        return Course::query()
            ->where('semester', $semester)
            ->with(['lecturer', 'courseSchedules'])
            ->get()
            ->map(function ($course) use ($today) {
                $isOngoing = $course->courseSchedules->contains(function ($schedule) use ($today) {
                    return $schedule->day_of_week == $today;
                });

                $course->lecturer_name = $course->lecturer?->full_name;
                $course->is_ongoing = $isOngoing;
                $course->detail_url = Detail::getUrl(['course' => $course]);

                return $course;
            });
    }
}
