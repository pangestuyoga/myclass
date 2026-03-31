<?php

namespace App\Http\Controllers;

use App\Filament\Support\SystemNotification;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $headings = [
            'hero_title' => SystemNotification::getMessage('Kuliah Jadi Makin Simpel! 🚀📚', 'Sistem Manajemen Kelas'),
            'hero_subtitle' => SystemNotification::getMessage('Gak Ribet Lagi.', 'Sederhana & Terintegrasi.'),
            'feature_session' => SystemNotification::getMessage('Info Sesi Kuliah 🏢📅', 'Manajemen Sesi'),
            'feature_session_desc' => SystemNotification::getMessage('Pantau jadwal kelasmu biar nggak ketinggalan info penting ya! 🕒✨', 'Pemantauan kelas dan penjadwalan terstruktur dan mudah dilihat.'),
            'feature_task' => SystemNotification::getMessage('Tugas & Materi 📝📚', 'Tugas & Materi'),
            'feature_task_desc' => SystemNotification::getMessage('Ambil materi dan kumpulin tugas dalam satu tempat, praktis banget! 🚀📂', 'Pengelolaan repositori perkuliahan dan pengumpulan tugas.'),
            'feature_attendance' => SystemNotification::getMessage('Presensi Digital 🙋‍♂️✅', 'Presensi Digital'),
            'feature_attendance_desc' => SystemNotification::getMessage('Absen makin gampang, tinggal klik langsung tercatat hadir. Gapake lama! 🔥⚡', 'Sistem pendataan kehadiran yang praktis, cepat, dan real-time.'),
            'feature_group' => SystemNotification::getMessage('Grup Belajar 👯‍♀️💬', 'Grup Belajar'),
            'feature_group_desc' => SystemNotification::getMessage('Bikin kelompok biar belajar bareng temen makin asik dan seru! 🌈✨', 'Ruang kolaborasi, diskusi, dan obrolan untuk saling terhubung.'),
        ];

        return view('welcome', compact('headings'));
    }
}
