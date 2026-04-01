<?php

namespace Database\Factories;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
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
            'class_session_id' => ClassSession::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
        ];
    }
}
