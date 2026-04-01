<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSchedule>
 */
class CourseScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'day_of_week' => fake()->numberBetween(1, 7),
            'start_time' => '08:00',
            'end_time' => '10:00',
        ];
    }
}
