<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudyGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyGroupFactory extends Factory
{
    protected $model = StudyGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'leader_id' => Student::factory(),
        ];
    }
}
