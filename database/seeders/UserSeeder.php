<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\Sex;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'info.pangestuyoga@gmail.com',
            'username' => 'pangestu',
            'password' => Hash::make('Minimal8@'),
        ]);

        Student::create([
            'user_id' => $user->id,
            'full_name' => 'Yoga Pangestu',
            'student_number' => '2457201008',
            'phone_number' => '082121495806',
            'sex' => Sex::Male,
            'address' => 'Kp. Bakan Sampeu',
            'date_of_birth' => '2003-03-13',
            'place_of_birth' => 'Subang',
        ]);

        $user->assignRole(RoleEnum::Developer);
        $user->assignRole(RoleEnum::Student);
    }
}
