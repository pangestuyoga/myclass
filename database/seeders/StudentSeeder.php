<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::factory()
            ->count(50)
            ->create()
            ->each(fn (Student $student) => $student->user->assignRole(RoleEnum::Student));
    }
}
