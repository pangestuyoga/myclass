<?php

namespace Database\Seeders;

use App\Enums\Sex;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Dr. Budi Santoso, S.Kom, M.Sc',
            'Prof. Siti Rahmawati, M.T',
            'Dr. Ahmad Fauzan, M.Kom',
            'Ir. Dwi Prasetyo, M.Eng',
            'Dr. Maya Kartika, M.Sc',
            'Dr. Andi Saputra, M.Kom',
            'Prof. Rizky Maulana, Ph.D',
            'Dr. Nanda Putri, M.T',
            'Ir. Fajar Hidayat, M.Eng',
            'Dr. Taufik Ramadhan, M.Kom',
            'Dr. Indah Permata Sari, M.Sc',
            'Prof. Yoga Pratama, Ph.D',
            'Dr. Dian Lestari, M.T',
            'Ir. Arif Nugroho, M.Eng',
            'Dr. Bagus Sekali Namanya, M.Kom',
        ];

        $cities = [
            'Bandung',
            'Jakarta',
            'Surabaya',
            'Yogyakarta',
            'Semarang',
            'Bogor',
            'Depok',
            'Bekasi',
            'Malang',
            'Solo',
            'Padang',
            'Medan',
            'Palembang',
            'Makassar',
            'Denpasar',
        ];

        for ($i = 0; $i < 15; $i++) {

            $userId = DB::table('users')->insertGetId([
                'email' => 'dosen'.($i + 1).'@kampus.ac.id',
                'username' => 'dosen'.($i + 1),
                'password' => Hash::make('Minimal8@'),
            ]);

            DB::table('lecturers')->insert([
                'user_id' => $userId,
                'lecturer_identification_number' => str_pad($i + 1, 10, '0', STR_PAD_LEFT),
                'full_name' => $names[$i],
                'phone_number' => '08123456'.rand(1000, 9999),
                'sex' => $i % 2 === 0 ? Sex::Male->value : Sex::Female->value,
                'address' => 'Jl. Kampus No. '.rand(1, 200),
                'date_of_birth' => Carbon::now()->subYears(rand(30, 55))->toDateString(),
                'place_of_birth' => $cities[$i],
            ]);
        }
    }
}
