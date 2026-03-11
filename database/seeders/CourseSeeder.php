<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Semester 1
            ['code' => 'SIU101', 'name' => 'Ilmu Amaliah, Sosial, dan Budaya Dasar', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIM101', 'name' => 'Keaswajaan', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIM102', 'name' => 'Ilmu Fiqih I', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIU102', 'name' => 'Pendidikan Anti Korupsi', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIU103', 'name' => 'Bahasa Inggris I', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIU104', 'name' => 'Bahasa Indonesia', 'credit' => 2, 'semester' => 1],
            ['code' => 'SIU105', 'name' => 'Kewarganegaraan', 'credit' => 2, 'semester' => 1],
            ['code' => 'FSI101', 'name' => 'Konsep Basis Data', 'credit' => 2, 'semester' => 1],
            ['code' => 'FSI102', 'name' => 'Konsep Sistem Informasi', 'credit' => 2, 'semester' => 1],
            ['code' => 'FSI103', 'name' => 'Dasar Pemrograman', 'credit' => 2, 'semester' => 1],

            // Semester 2
            ['code' => 'SIM203', 'name' => 'Ilmu Fiqih II', 'credit' => 2, 'semester' => 2],
            ['code' => 'FSI204', 'name' => 'Statistika dan Probabilitas', 'credit' => 2, 'semester' => 2],
            ['code' => 'SIU206', 'name' => 'Bahasa Inggris II', 'credit' => 2, 'semester' => 2],
            ['code' => 'SIU207', 'name' => 'Pendidikan Agama', 'credit' => 2, 'semester' => 2],
            ['code' => 'SIU208', 'name' => 'Pendidikan Pancasila', 'credit' => 2, 'semester' => 2],
            ['code' => 'FSI205', 'name' => 'Financial Technology', 'credit' => 3, 'semester' => 2],
            ['code' => 'FSI206', 'name' => 'Sistem Basis Data', 'credit' => 2, 'semester' => 2],
            ['code' => 'FSI207', 'name' => 'Computational Thinking', 'credit' => 2, 'semester' => 2],
            ['code' => 'FSI208', 'name' => 'Pemrograman Berbasis Web', 'credit' => 3, 'semester' => 2],
            ['code' => 'FSI209', 'name' => 'Pengantar Teknologi Informasi', 'credit' => 2, 'semester' => 2],

            // Semester 3
            ['code' => 'FSI310', 'name' => 'Transformasi Digital', 'credit' => 2, 'semester' => 3],
            ['code' => 'FSI311', 'name' => 'Analisis dan Perancangan Sistem Informasi', 'credit' => 3, 'semester' => 3],
            ['code' => 'FSI312', 'name' => 'Sistem Informasi Manajemen', 'credit' => 3, 'semester' => 3],
            ['code' => 'FSI313', 'name' => 'Business Intelligence', 'credit' => 2, 'semester' => 3],
            ['code' => 'FSI314', 'name' => 'Technopreneurship', 'credit' => 3, 'semester' => 3],
            ['code' => 'FSI315', 'name' => 'Matematika Diskrit', 'credit' => 3, 'semester' => 3],
            ['code' => 'FSI316', 'name' => 'Interaksi Manusia Komputer', 'credit' => 2, 'semester' => 3],
            ['code' => 'FSI317', 'name' => 'Communication Skill', 'credit' => 2, 'semester' => 3],
            ['code' => 'FSI318', 'name' => 'Tata Kelola Teknologi Informasi', 'credit' => 2, 'semester' => 3],

            // Semester 4
            ['code' => 'FSI419', 'name' => 'Sistem Pendukung Keputusan', 'credit' => 2, 'semester' => 4],
            ['code' => 'FSI420', 'name' => 'Pemrograman Berorientasi Object', 'credit' => 3, 'semester' => 4],
            ['code' => 'FSI421', 'name' => 'Manajemen Projek Sistem Informasi', 'credit' => 3, 'semester' => 4],
            ['code' => 'FSI422', 'name' => 'Sistem Operasi', 'credit' => 3, 'semester' => 4],
            ['code' => 'FSI423', 'name' => 'UI/UX', 'credit' => 3, 'semester' => 4],
            ['code' => 'FSI424', 'name' => 'Etika Profesi dan Profesional', 'credit' => 2, 'semester' => 4],
            ['code' => 'FSI425', 'name' => 'Jaringan Komputer', 'credit' => 3, 'semester' => 4],
            ['code' => 'FSI426', 'name' => 'Arsitektur Enterprise', 'credit' => 2, 'semester' => 4],

            // Semester 5
            ['code' => 'FSI527', 'name' => 'Software Testing dan Quality Assurance', 'credit' => 2, 'semester' => 5],
            ['code' => 'FSI528', 'name' => 'Proyek Perangkat Lunak', 'credit' => 3, 'semester' => 5],
            ['code' => 'FSI529', 'name' => 'Metodologi Penelitian dan Penulisan Ilmiah', 'credit' => 3, 'semester' => 5],
            ['code' => 'FSI530', 'name' => 'Pemrograman Aplikasi Bergerak', 'credit' => 3, 'semester' => 5],
            ['code' => 'FSI531', 'name' => 'Big Data', 'credit' => 3, 'semester' => 5],
            ['code' => 'FSI532', 'name' => 'Pemrograman IoT', 'credit' => 3, 'semester' => 5],

            // Semester 6
            ['code' => 'FSI633', 'name' => 'Manajemen Proses Bisnis', 'credit' => 2, 'semester' => 6],
            ['code' => 'FSI634', 'name' => 'Pemrograman Aplikasi Bergerak 2', 'credit' => 3, 'semester' => 6],
            ['code' => 'FSI635', 'name' => 'Fundamental ERP', 'credit' => 3, 'semester' => 6],
            ['code' => 'FSI636', 'name' => 'Audit Sistem Informasi', 'credit' => 3, 'semester' => 6],
            ['code' => 'FSI637', 'name' => 'Keamanan Jaringan', 'credit' => 2, 'semester' => 6],
            ['code' => 'FSI638', 'name' => 'Keamanan Sistem Informasi', 'credit' => 3, 'semester' => 6],

            // Semester 7
            ['code' => 'MBKM701', 'name' => 'Pertukaran Pelajar', 'credit' => 20, 'semester' => 7],
            ['code' => 'SIU709', 'name' => 'Skripsi I', 'credit' => 2, 'semester' => 7],

            // Semester 8
            ['code' => 'SIU810', 'name' => 'Skripsi II', 'credit' => 4, 'semester' => 8],
            ['code' => 'FSI839', 'name' => 'Startup Digital', 'credit' => 2, 'semester' => 8],
        ];

        $lecturers = Lecturer::pluck('id')->toArray();

        shuffle($lecturers);

        $lecturerSemesterMap = [];

        foreach ($courses as $i => &$course) {

            $semester = $course['semester'];

            if ($i < count($lecturers)) {
                $lecturerId = $lecturers[$i];
            } else {

                do {
                    $lecturerId = $lecturers[array_rand($lecturers)];
                } while (
                    isset($lecturerSemesterMap[$lecturerId]) &&
                    in_array($semester, $lecturerSemesterMap[$lecturerId])
                );
            }

            $course['lecturer_id'] = $lecturerId;

            $lecturerSemesterMap[$lecturerId][] = $semester;
        }

        Course::insert($courses);
    }
}
