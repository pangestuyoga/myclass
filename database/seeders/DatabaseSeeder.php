<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->isLocal()) {
            $this->call([
                ShieldSeeder::class,
                CourseSeeder::class,
                CourseScheduleSeeder::class,
            ]);
        } else {
            $this->call([
                ShieldSeeder::class,
                UserSeeder::class,
                StudentSeeder::class,
                CourseSeeder::class,
                CourseScheduleSeeder::class,
            ]);
        }
    }
}
