<?php

namespace App\Filament\Resources\Learning\Attendances\Pages;

use App\Filament\Resources\Learning\Attendances\AttendanceResource;
use App\Filament\Support\SystemNotification;
use App\Models\Attendance;
use App\Models\CourseSchedule;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Resources\Pages\Page;

class ManageAttendances extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static ?string $title = 'Presensi Kuliah';

    protected static ?string $navigationLabel = 'Presensi';

    protected string $view = 'filament.resources.learning.attendances.pages.list-attendances';

    public function getSchedules()
    {
        $student = auth()->user()->student;

        if (! $student) {
            return collect();
        }

        $today = now()->dayOfWeekIso;
        $semester = app(GeneralSettings::class)->current_semester;

        return CourseSchedule::query()
            ->where('day_of_week', $today)
            ->whereHas('course', function ($q) use ($semester) {
                $q->where('semester', $semester);
            })
            ->with(['course.lecturer', 'attendances' => function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->whereDate('date', now()->toDateString());
            }])
            ->orderBy('start_time')
            ->get();
    }

    public function getScheduleCards()
    {
        return $this->getSchedules()
            ->map(function ($schedule) {
                $attendance = $schedule->attendances->first();
                $isAttended = $attendance !== null;

                $now = now();
                $startTime = now()->setTimeFrom($schedule->start_time);

                $canAttend = $now->greaterThanOrEqualTo($startTime);

                $statusLabel = 'Belum Presensi';
                $statusColor = 'warning';
                $statusIcon = 'heroicon-o-clock';

                if ($isAttended) {
                    $statusLabel = 'Hadir';
                    $statusColor = 'success';
                    $statusIcon = 'heroicon-o-check-circle';
                } elseif (! $canAttend) {
                    $statusLabel = 'Belum Dimulai';
                    $statusColor = 'gray';
                    $statusIcon = 'heroicon-o-lock-closed';
                }

                return (object) [
                    'id' => $schedule->id,
                    'course_name' => $schedule->course->name,
                    'lecturer_name' => $schedule->course->lecturer?->full_name ?? 'Belum Ditentukan',
                    'time_range' => $schedule->start_time->format('H:i').' - '.$schedule->end_time->format('H:i'),
                    'is_attended' => $isAttended,
                    'can_attend' => $canAttend && ! $isAttended,
                    'status_label' => $statusLabel,
                    'status_color' => $statusColor,
                    'status_icon' => $statusIcon,
                    'attended_at' => $isAttended ? $attendance->attended_at->format('H:i') : null,
                ];
            });
    }

    protected function getViewData(): array
    {
        return [
            'scheduleCards' => $this->getScheduleCards(),
        ];
    }

    public function attend(int $scheduleId): void
    {
        /** @var User $user */
        $user = auth()->user();
        $student = $user->student;
        if (! $student) {
            return;
        }

        $schedule = CourseSchedule::find($scheduleId);
        if (! $schedule) {
            return;
        }

        // Check if already attended
        $existing = Attendance::where('student_id', $student->id)
            ->where('course_schedule_id', $scheduleId)
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($existing) {
            SystemNotification::warning(
                'Presensi Sudah Tercatat ⚠️',
                'Anda telah melakukan presensi untuk jadwal perkuliahan ini pada hari ini.'
            )->send();

            return;
        }

        // Check time
        $startTime = now()->setTimeFrom($schedule->start_time);
        if (now()->lessThan($startTime)) {
            SystemNotification::warning(
                'Presensi Belum Dibuka ⏳',
                'Waktu presensi untuk jadwal perkuliahan ini belum dimulai. Silakan lakukan presensi setelah waktu dimulai.'
            )->send();

            return;
        }

        Attendance::create([
            'student_id' => $student->id,
            'course_schedule_id' => $scheduleId,
            'date' => now()->toDateString(),
            'attended_at' => now(),
        ]);

        SystemNotification::success(
            'Presensi Berhasil Dicatat ✅',
            'Kehadiran Anda telah berhasil direkam ke dalam sistem.'
        )->send();
    }
}
