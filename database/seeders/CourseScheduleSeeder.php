<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Database\Seeder;

class CourseScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        $timeSlots = [
            ['08:00:00', '10:00:00'],
            ['10:00:00', '12:00:00'],
            ['13:00:00', '15:00:00'],
            ['15:00:00', '17:00:00'],
        ];

        foreach ($courses as $course) {

            $slot = $timeSlots[array_rand($timeSlots)];

            CourseSchedule::create([
                'course_id' => $course->id,
                'day_of_week' => rand(1, 6),
                'start_time' => $slot[0],
                'end_time' => $slot[1],
            ]);
        }
    }
}
