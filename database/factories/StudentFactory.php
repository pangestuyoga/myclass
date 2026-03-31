<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'student_number' => fake()->unique()->regexify('[1-9]{1}[0-9]{9}'),
            'phone_number' => '0812'.fake()->numerify('########'),
            'sex' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'date_of_birth' => fake()->dateTimeBetween('-30 years', '-16 years')->format('Y-m-d'),
            'place_of_birth' => fake()->city(),
        ];
    }
}
