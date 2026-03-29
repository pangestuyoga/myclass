<?php

namespace App\Console\Commands;

use App\Models\ClassSession;
use App\Models\CourseSchedule;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;

class GenerateDailySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-sessions';

    protected $description = 'Membangun sesi kelas harian secara otomatis berdasarkan jadwal.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dayOfWeek = now()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        $currentSemester = app(GeneralSettings::class)->current_semester;

        $schedules = CourseSchedule::query()
            ->where('day_of_week', $dayOfWeek)
            ->whereHas('course', function ($query) use ($currentSemester) {
                $query->where('semester', $currentSemester);
            })
            ->get();

        $count = 0;
        foreach ($schedules as $schedule) {
            $exists = ClassSession::where('course_id', $schedule->course_id)
                ->whereDate('date', now())
                ->exists();

            if (! $exists) {
                $lastSession = ClassSession::where('course_id', $schedule->course_id)->max('session_number');

                ClassSession::create([
                    'course_id' => $schedule->course_id,
                    'session_number' => ($lastSession ?? 0) + 1,
                    'date' => now()->toDateString(),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ]);
                $count++;
            }
        }

        $this->info("Generated {$count} sessions for ".now()->toDateString());
    }
}
