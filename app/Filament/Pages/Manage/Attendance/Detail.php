<?php

namespace App\Filament\Pages\Manage\Attendance;

use App\Filament\Actions\BackAction;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Student;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class Detail extends Page
{
    use HasPageShield;

    public static function getPagePermission(): string
    {
        return 'View:AttendanceMonitoring';
    }

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Detail Monitoring Presensi';

    protected static ?string $slug = 'manage/attendance-monitoring/{course}';

    protected string $view = 'filament.pages.manage.attendance.detail';

    public Course $course;

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(Index::getUrl()),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'selectedCourse' => $this->course,
            'meetingHistory' => $this->getMeetingHistory(),
            'activeMeetingStats' => $this->getActiveMeetingStats(),
        ];
    }

    private function getMeetingHistory(): Collection
    {
        return Attendance::query()
            ->whereHas('courseSchedule', function ($q) {
                $q->where('course_id', $this->course->id);
            })
            ->select('date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($att) {
                $attendedCount = Attendance::whereHas('courseSchedule', function ($q) {
                    $q->where('course_id', $this->course->id);
                })
                    ->whereDate('date', $att->date)
                    ->count();

                $totalStudents = Student::count();

                return (object) [
                    'date' => [
                        'month' => $att->date->translatedFormat('M'),
                        'day' => $att->date->translatedFormat('d'),
                    ],
                    'formatted_date' => $att->date->translatedFormat('l, d F Y'),
                    'attended_count' => $attendedCount,
                    'total_students' => $totalStudents,
                    'share_url' => route('share.attendance', [
                        'token' => $this->course->sharing_token,
                        'date' => $att->date->toDateString(),
                    ]),
                ];
            });
    }

    private function getActiveMeetingStats(): ?object
    {
        $today = now()->dayOfWeekIso;
        $nowTime = now()->format('H:i');

        $activeSchedule = CourseSchedule::where('course_id', $this->course->id)
            ->where('day_of_week', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->first();

        if (! $activeSchedule) {
            return null;
        }

        $attendedIds = Attendance::where('course_schedule_id', $activeSchedule->id)
            ->whereDate('date', now()->toDateString())
            ->pluck('student_id')
            ->toArray();

        $totalStudents = Student::count();

        $absentStudents = Student::whereNotIn('id', $attendedIds)->get();

        return (object) [
            'time_range' => $activeSchedule->start_time->format('H:i').' - '.$activeSchedule->end_time->format('H:i'),
            'attended_count' => count($attendedIds),
            'total_students' => $totalStudents,
            'absent_students' => $absentStudents,
            'percentage' => round((count($attendedIds) / $totalStudents) * 100),
        ];
    }
}
