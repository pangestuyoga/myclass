<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('??# ###')),
            'name' => fake()->words(3, true),
            'credit' => fake()->numberBetween(1, 6),
            'semester' => fake()->numberBetween(1, 8),
            'lecturer' => fake()->name(),
        ];
    }
}
