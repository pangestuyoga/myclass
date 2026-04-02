<?php

namespace Database\Factories;

use App\Enums\ChangelogType;
use App\Models\Changelog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChangelogFactory extends Factory
{
    protected $model = Changelog::class;

    public function definition(): array
    {
        static $patchNumber = 0;
        $patchNumber++;

        return [
            'version' => "v1.0.{$patchNumber}",
            'release_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'title' => $this->faker->sentence(4),
            'changes' => $this->faker->sentences(3),
            'type' => $this->faker->randomElement(ChangelogType::cases()),
            'description' => $this->faker->paragraph(),
        ];
    }

    public function feature(): static
    {
        return $this->state(['type' => ChangelogType::Feature]);
    }

    public function bugfix(): static
    {
        return $this->state(['type' => ChangelogType::BugFix]);
    }
}
