<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\CourseSchedule;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'class_session_id' => ClassSession::factory(),
            'course_schedule_id' => CourseSchedule::factory(),
            'date' => now()->toDateString(),
            'attended_at' => now(),
        ];
    }
}
