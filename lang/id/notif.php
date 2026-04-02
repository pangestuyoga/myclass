<?php

return [
    'cheerful' => [
        // Standard Actions
        'create' => [
            'title' => 'Hore! Data Tersimpan 🎉✨',
            'body' => 'Data baru berhasil ditambahkan! Sistem sudah menyimpannya dengan aman. 🚀💪',
        ],
        'update' => [
            'title' => 'Mantap! Data Diperbarui ✅🔥',
            'body' => 'Perubahan berhasil disimpan! Data sekarang sudah up-to-date dan segar lagi. ✨👌',
        ],
        'delete' => [
            'title' => 'Oke, Data Dihapus 🗑️👋',
            'body' => 'Data tersebut sudah berhasil dihapus dari sistem. Semuanya bersih dan rapi sekarang! 😊🚮',
        ],
        'force_delete' => [
            'title' => 'Selamat Tinggal Selamanya 👋😢',
            'body' => 'Data telah dihapus permanen dan tidak bisa kembali. Semoga ini keputusan yang tepat! 🚮💨',
        ],
        'restore' => [
            'title' => 'Welcome Back! Data Pulih ♻️✨',
            'body' => 'Data berhasil dikembalikan! Hati-hati ya, jangan sampai terhapus lagi. 😉👍',
        ],
        'status_updated' => [
            'title' => 'Status Berubah! 🔄✨',
            'body' => 'Status data berhasil diperbarui. Perubahan langsung aktif ya! 👍',
        ],
        'bulk_delete' => [
            'title' => 'Oke, Banyak Data Dihapus 🗑️👋',
            'body' => 'Semua data yang dipilih berhasil dihapus. Sistem makin lega deh! 😊',
        ],
        'bulk_force_delete' => [
            'title' => 'Bye Bye Semua! 👋🔥',
            'body' => 'Data yang dipilih sudah dihapus permanen. Bersih total! 🧹💨',
        ],
        'bulk_restore' => [
            'title' => 'Hore! Banyak Data Pulih ♻️🎉',
            'body' => 'Data-data tersebut sudah kembali aktif. Selamat bekerja kembali! 💪✨',
        ],

        // Custom Messages
        'password_changed' => [
            'title' => 'Password Berhasil Diganti 🔑✨',
            'body' => 'Password untuk :name sudah diperbarui. Jangan sampai lupa lagi ya! 😉🔐',
        ],
        'profile_updated' => [
            'title' => 'Profil Kece Diperbarui! 👤🌈',
            'body' => 'Data profil kamu sudah berhasil disimpan. Tampil makin oke deh! 🚀✨',
        ],
        'account_security_updated' => [
            'title' => 'Keamanan Akun Diperbarui 🔐',
            'body' => 'Anda telah mengubah kata sandi. Demi keamanan, silakan masuk kembali ya! 🚪🔑',
        ],
        'login_success' => [
            'title' => 'Selamat Datang Kembali! 👋✨',
            'body' => 'Senang melihatmu lagi, :name! Ayo lanjut belajar dan berkarya hari ini. 💪🚀',
        ],
        'login_failed' => [
            'title' => 'Ups! Ada yang Salah 🙅‍♂️❌',
            'body' => 'Email atau password kamu nggak pas nih. Coba dicek lagi ya, pelan-pelan saja! 😉🔍',
        ],
        'access_suspended' => [
            'title' => 'Akses Ditangguhkan ⛔',
            'body' => 'Maaf, Anda tidak dapat masuk karena status akun saat ini sedang tidak aktif. Silakan hubungi Administrator ya! 😴',
        ],
        'login_invalid' => [
            'title' => 'Waduh! Gagal Masuk 🛑⚠️',
            'body' => 'Ada masalah saat memproses login kamu. Coba lagi sebentar lagi ya! 🙏🌀',
        ],
        'assignment_pinned' => [
            'title' => 'Tugas Disematkan! 📌✨',
            'body' => 'Tugas ini sekarang ada di paling atas biar gampang kamu pantau! 🚀',
        ],
        'assignment_unpinned' => [
            'title' => 'Sematkan Dilepas 📍👋',
            'body' => 'Tugas sudah kembali ke posisi semula di daftar. 👌',
        ],
        'link_copied' => [
            'title' => 'Link Berhasil Disalin! 🔗✨',
            'body' => 'Link tugas sudah tersimpan di clipboard kamu. Tinggal share deh! 🚀👌',
        ],
        'attendance_already_recorded' => [
            'title' => 'Presensi Sudah Tercatat ⚠️',
            'body' => 'Anda telah melakukan presensi untuk sesi perkuliahan ini. Jangan lupa belajar yang rajin ya! 📚✨',
        ],
        'attendance_not_started' => [
            'title' => 'Sabar Yaa! Belum Dibuka ⏳',
            'body' => 'Waktu presensi untuk sesi perkuliahan ini belum dimulai nih. Tunggu sebentar lagi, ya! ☕😊',
        ],
        'attendance_success' => [
            'title' => 'Mantap! Presensi Berhasil ✨🚀',
            'body' => 'Kehadiran Anda telah berhasil direkam. Semangat belajarnya hari ini! 💪✅',
        ],
        'sessions_generated_failed' => [
            'title' => 'Oops! Gagal Generate 🚫',
            'body' => 'Jadwal belum ditentukan untuk mata kuliah ini, jadi sistem bingung mau buat sesi kapan. 😟',
        ],
        'sessions_generated_success' => [
            'title' => 'Hore! Sesi Siap ✨🚀',
            'body' => ':count Sesi baru (No. :start sampai :end) berhasil dibuat otomatis. 🎉',
        ],
        'submission_no_group' => [
            'title' => 'Gagal Mengumpulkan 🚫',
            'body' => 'Anda tidak terdaftar dalam kelompok manapun yang ditugaskan untuk tugas ini. 😟',
        ],
        'submission_not_leader' => [
            'title' => 'Akses Ditolak 🚫',
            'body' => 'Hanya ketua kelompok yang diizinkan untuk mengumpulkan atau memperbarui tugas kelompok. 👑',
        ],
        'submission_file_missing' => [
            'title' => 'Pilih File Dulu Dong! 📁',
            'body' => 'Silakan pilih file untuk dikumpulkan agar sistem bisa menyimpannya ya! 😊',
        ],
        'submission_updated' => [
            'title' => 'Keren! Tugas Sudah Update ✨',
            'body' => 'File tugas Anda telah berhasil diunggah ulang dan diperbarui di sistem. 📤',
        ],
        'submission_success' => [
            'title' => 'Yeay! Tugas Terkumpul! 🎉',
            'body' => 'Berhasil! Tugas Anda telah tercatat dengan aman di sistem. Semangat! 🎈',
        ],
        'user_activated' => [
            'title' => 'Pengguna Diaktifkan! ✅✨',
            'body' => 'Status akun pengguna berhasil diaktifkan kembali. Siap beraksi! 🚀',
        ],
        'user_deactivated' => [
            'title' => 'Pengguna Dinonaktifkan ⛔👋',
            'body' => 'Status akun pengguna telah berhasil dinonaktifkan. Istirahat dulu ya... 😴',
        ],

        // UI Labels & Descriptions
        'labels' => [
            'assignment_info' => [
                'title' => 'Informasi Tugas 📝✨',
                'description' => 'Jelaskan tugasnya apa, kapan deadline-nya, dan buat sesi berapa. Semangat! 💪',
            ],
            'assignment_target' => [
                'title' => 'Target Penugasan 🎯🧑‍🎓',
                'description' => 'Pilih siapa saja yang bakal ngerjain tugas ini. Jangan sampai salah sasaran ya! 🏹',
            ],
            'account_credentials' => [
                'title' => 'Kredensial Akun 🔑✨',
                'description' => 'Pake email dan username ini buat login ya. Oh iya, password awalnya <code class="text-sm bg-gray-100 text-red-400 px-1.5 py-0.5 rounded font-mono">Minimal8@</code> 🥳',
            ],
            'student_data' => [
                'title' => 'Data Mahasiswa 🎓📚',
                'description' => 'Lengkapi data mahasiswa di bawah ini dengan benar ya! 🤓📝',
                'description_profile' => 'Pastikan data akademikmu sesuai dengan yang di sistem, jangan sampai salah loh! 🤓📝',
            ],
            'user_account' => [
                'title' => 'Akun Pengguna 👤✨',
                'description' => 'Kelola informasi detail akun Anda di sini ya biar nggak kudet! 😉🚀',
            ],
            'account_security' => [
                'title' => 'Keamanan Akun 🔐⚡',
                'description' => 'Rajin-rajin ganti password ya, biar akunmu aman dan nggak dibobol orang! 🕵️‍♂️🛡️',
            ],
            'appearance_settings' => [
                'title' => 'Pengaturan Tampilan & UX 🎨✨',
                'description' => 'Personalisasi pengalaman aplikasi Anda agar lebih nyaman dan sesuai selera! 🌈🚀',
            ],
            'help_center' => [
                'title' => 'Butuh Bantuan? Tanya Saja! 💬✨',
            ],
            'study_group_list' => [
                'title' => 'Daftar Grup Seru! 💃🕺',
                'description' => 'Lihat semua kelompok belajarmu di sini. Makin kompak makin asik! 🤝📚',
            ],
            'empty_study_group' => [
                'title' => 'Wah, Belum Ada Grup! 👯‍♂️🌀',
                'description' => 'Belum ada kelompok belajar yang terdaftar. Ayo ajak teman-temanmu bikin kelompok biar makin seru belajarnya! 🤝📄',
            ],
            'course_schedule_hint' => 'Geser ke samping buat intip hari lainnya ya! 📅✨',
            'empty_course_schedule' => [
                'title' => 'Yah, Belum Ada Jadwal Nih! 📄💤',
                'description' => 'Belum ada jadwal perkuliahan yang terdaftar. Mungkin hari ini kamu bisa istirahat dulu? 😉💤',
            ],
            'attendance_section' => [
                'title' => 'Ayo Presensi Dulu! 🙋‍♂️✨',
                'description' => 'Jangan lupa absen ya kalau kelasnya udah mulai! Biar tercatat hadir. 💪',
            ],
            'attendance_history' => [
                'title' => 'Jejak Presensimu 🕰️📜',
                'description' => 'Di sini kamu bisa ngecek semua histori kehadiranmu sebelumnya. Rajin-rajin ya! 🎓',
            ],
            'empty_attendance' => [
                'title' => 'Yah, Absensi Belum Muncul! 💁‍♂️🌀',
                'description' => 'Sabar ya, setelah jadwal perkuliahan kamu muncul baru bisa diabsen di sini. Pantau terus jam kuliahnya! 🕒💪',
            ],
            'assignment_list' => [
                'title' => 'Daftar Tugas Bikin Lemes! 🤣📒',
                'description' => 'Yuk, cek dan kumpulin tugasmu biar tenang hidupnya! Klik aja di tugasnya ya. 🚀👨‍💻',
            ],
            'changelog' => [
                'title' => 'Catatan Rilis 📜✨',
                'description' => 'Kepoin apa aja yang baru, fitur kece, dan perbaikan bug biar nggak kudet! 🚀🔥',
            ],
            'empty_changelog' => [
                'title' => 'Belum Ada Catatan Rilis 📭',
                'description' => 'Belum ada update yang tercatat nih. Buat mulai catat riwayat pembaruan pertama! 🚀',
            ],
        ],

        // Icons
        'icons' => [
            'assignment_info' => 'heroicon-o-document-text',
            'assignment_target' => 'heroicon-o-users',
            'account_credentials' => 'heroicon-o-key',
            'student_data' => 'heroicon-o-academic-cap',
            'user_account' => 'heroicon-o-face-smile',
            'account_security' => 'heroicon-o-shield-check',
            'appearance_settings' => 'heroicon-o-swatch',
        ],
    ],
    'formal' => [
        // Standard Actions
        'create' => [
            'title' => 'Data Berhasil Disimpan',
            'body' => 'Data baru telah berhasil ditambahkan ke dalam sistem.',
        ],
        'update' => [
            'title' => 'Perubahan Tersimpan',
            'body' => 'Perubahan pada data telah berhasil diperbarui dan disimpan.',
        ],
        'delete' => [
            'title' => 'Data Dihapus',
            'body' => 'Data tersebut telah berhasil dihapus dari sistem aplikasi.',
        ],
        'force_delete' => [
            'title' => 'Data Dihapus Permanen',
            'body' => 'Data tersebut telah dihapus secara permanen dari sistem.',
        ],
        'restore' => [
            'title' => 'Data Berhasil Dipulihkan',
            'body' => 'Data yang dihapus sebelumnya telah berhasil dipulihkan ke dalam sistem.',
        ],
        'status_updated' => [
            'title' => 'Status Diperbarui',
            'body' => 'Status data telah berhasil diperbarui dan telah diterapkan.',
        ],
        'bulk_delete' => [
            'title' => 'Data Berhasil Dihapus',
            'body' => 'Seluruh data yang dipilih telah berhasil dihapus dari sistem.',
        ],
        'bulk_force_delete' => [
            'title' => 'Data Dihapus Permanen',
            'body' => 'Data yang dipilih telah berhasil dihapus secara permanen.',
        ],
        'bulk_restore' => [
            'title' => 'Data Berhasil Dipulihkan',
            'body' => 'Seluruh data yang dipilih telah berhasil dikembalikan ke posisi semula.',
        ],

        // Custom Messages
        'password_changed' => [
            'title' => 'Kata Sandi Berhasil Diperbarui',
            'body' => 'Kata sandi untuk :name telah berhasil diperbarui di dalam sistem.',
        ],
        'profile_updated' => [
            'title' => 'Profil Berhasil Diperbarui',
            'body' => 'Perubahan pada informasi profil Anda telah berhasil disimpan.',
        ],
        'account_security_updated' => [
            'title' => 'Pembaruan Kredensial',
            'body' => 'Kata sandi Anda telah berhasil diubah. Harap lakukan otentikasi ulang untuk melanjutkan sesi.',
        ],
        'login_success' => [
            'title' => 'Otentikasi Berhasil',
            'body' => 'Selamat datang kembali, :name. Anda telah berhasil masuk ke dalam sistem.',
        ],
        'login_failed' => [
            'title' => 'Otentikasi Gagal',
            'body' => 'Email atau kata sandi yang Anda masukkan tidak sesuai dengan record kami.',
        ],
        'access_suspended' => [
            'title' => 'Akses Akun Terbatas',
            'body' => 'Akun Anda saat ini dalam status tidak aktif. Mohon hubungi pihak Administrator untuk klarifikasi lebih lanjut.',
        ],
        'login_invalid' => [
            'title' => 'Kesalahan Sistem',
            'body' => 'Terjadi kesalahan saat memproses permintaan masuk Anda. Silakan coba lagi.',
        ],
        'assignment_pinned' => [
            'title' => 'Tugas Disematkan',
            'body' => 'Tugas telah berhasil disematkan ke bagian atas daftar penugasan.',
        ],
        'assignment_unpinned' => [
            'title' => 'Sematkan Dilepas',
            'body' => 'Status sematan pada tugas ini telah berhasil dilepaskan.',
        ],
        'link_copied' => [
            'title' => 'Tautan Berhasil Disalin',
            'body' => 'Tautan tugas telah disalin ke papan klip perangkat Anda.',
        ],
        'attendance_already_recorded' => [
            'title' => 'Pemberitahuan Presensi',
            'body' => 'Status kehadiran Anda untuk sesi perkuliahan ini telah tercatat sebelumnya dalam sistem.',
        ],
        'attendance_not_started' => [
            'title' => 'Sesi Presensi Belum Dimulai',
            'body' => 'Akses pengisian presensi untuk sesi ini belum diaktifkan sesuai dengan jadwal yang ditentukan.',
        ],
        'attendance_success' => [
            'title' => 'Konfirmasi Presensi Berhasil',
            'body' => 'Informasi kehadiran Anda telah berhasil diverifikasi dan disimpan secara resmi ke dalam sistem.',
        ],
        'sessions_generated_failed' => [
            'title' => 'Eksekusi Dibatalkan',
            'body' => 'Penjadwalan resmi belum dikonfigurasi untuk mata kuliah ini. Proses pembuatan sesi otomatis tidak dapat dilanjutkan.',
        ],
        'sessions_generated_success' => [
            'title' => 'Verifikasi Sesi Berhasil',
            'body' => 'Seluruh :count sesi pembelajaran tambahan (No. :start sampai :end) telah berhasil dikonfigurasi secara otomatis.',
        ],
        'submission_no_group' => [
            'title' => 'Kesalahan Pengumpulan Tugas',
            'body' => 'Data akun Anda tidak ditemukan dalam daftar kelompok yang ditugaskan untuk tugas ini.',
        ],
        'submission_not_leader' => [
            'title' => 'Batasan Otoritas Pengumpulan',
            'body' => 'Hanya ketua kelompok yang memiliki otoritas untuk memperbarui atau mengumpulkan tugas kelompok.',
        ],
        'submission_file_missing' => [
            'title' => 'Kelengkapan Berkas Diperlukan',
            'body' => 'Mohon sertakan lampiran berkas sebelum melanjutkan proses pengumpulan tugas.',
        ],
        'submission_updated' => [
            'title' => 'Pembaruan Berkas Berhasil',
            'body' => 'Berkas tugas telah berhasil diunggah ulang dan divalidasi oleh sistem.',
        ],
        'submission_success' => [
            'title' => 'Konfirmasi Pengumpulan Berhasil',
            'body' => 'Seluruh berkas tugas Anda telah berhasil diverifikasi dan disimpan oleh sistem.',
        ],
        'user_activated' => [
            'title' => 'Aktivasi Akun Berhasil',
            'body' => 'Status akun pengguna terpilih telah diubah menjadi aktif.',
        ],
        'user_deactivated' => [
            'title' => 'Penonaktifan Akun Berhasil',
            'body' => 'Status akun pengguna terpilih telah diubah menjadi tidak aktif.',
        ],

        // UI Labels & Descriptions
        'labels' => [
            'assignment_info' => [
                'title' => 'Informasi Tugas',
                'description' => 'Lengkapi informasi detail penugasan.',
            ],
            'assignment_target' => [
                'title' => 'Target Penugasan',
                'description' => 'Tentukan sasaran penerima penugasan ini.',
            ],
            'account_credentials' => [
                'title' => 'Akun',
                'description' => 'Alamat Surel dan Nama Pengguna akan digunakan untuk masuk ke dalam sistem. Kata sandi bawaan adalah <code class="text-sm bg-gray-100 text-red-400 px-1.5 py-0.5 rounded font-mono">Minimal8@</code>',
            ],
            'student_data' => [
                'title' => 'Informasi Mahasiswa',
                'description' => 'Masukkan seluruh informasi akademik dan personal mahasiswa dengan benar.',
                'description_profile' => 'Pastikan data akademik Anda sesuai dengan record sistem.',
            ],
            'user_account' => [
                'title' => 'Akun',
                'description' => 'Kelola informasi detail akun Anda agar selalu terkini.',
            ],
            'account_security' => [
                'title' => 'Keamanan Akun',
                'description' => 'Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.',
            ],
            'appearance_settings' => [
                'title' => 'Preferensi Tampilan',
                'description' => 'Sesuaikan gaya bahasa, warna tema, dan jenis huruf aplikasi Anda.',
            ],
            'help_center' => [
                'title' => 'Informasi & Bantuan',
            ],
            'study_group_list' => [
                'title' => 'Kelompok Belajar',
                'description' => 'Daftar kelompok belajar Anda dan teman-teman sekelas.',
            ],
            'empty_study_group' => [
                'title' => 'Tidak ada data yang ditemukan',
                'description' => 'Belum ada kelompok belajar yang terdaftar untuk kriteria ini. Klik tombol tambah untuk memulai pembuatan kelompok.',
            ],
            'course_schedule_hint' => 'Geser ke samping untuk melihat hari lainnya.',
            'empty_course_schedule' => [
                'title' => 'Tidak ada data yang ditemukan',
                'description' => 'Belum ada jadwal perkuliahan yang terdaftar untuk kriteria ini. Hubungi administrator jika jadwal Anda belum muncul.',
            ],
            'attendance_section' => [
                'title' => 'Sesi Kuliah',
                'description' => 'Silakan melakukan presensi sesuai dengan waktu kelas.',
            ],
            'attendance_history' => [
                'title' => 'Riwayat Presensi',
                'description' => 'Berikut adalah riwayat kehadiran Anda pada sesi perkuliahan.',
            ],
            'empty_attendance' => [
                'title' => 'Tidak ada data yang ditemukan',
                'description' => 'Setelah jadwal Anda muncul, Anda dapat melakukan presensi di sini. Pastikan Anda mengecek kembali pada jam perkuliahan.',
            ],
            'assignment_list' => [
                'title' => 'Tugas Saya',
                'description' => 'Klik pada tugas untuk melihat detail dan mengumpulkan file.',
            ],
            'changelog' => [
                'title' => 'Catatan Rilis',
                'description' => 'Lihat riwayat perbaikan bug, pembaruan aplikasi, dan rilis fitur terbaru secara mendalam.',
            ],
            'empty_changelog' => [
                'title' => 'Tidak Ada Riwayat Pembaruan',
                'description' => 'Belum ada catatan rilis yang tersedia. Buat untuk mendokumentasikan pembaruan aplikasi.',
            ],
        ],

        // Icons
        'icons' => [
            'assignment_info' => 'heroicon-o-information-circle',
            'assignment_target' => 'heroicon-o-user-group',
            'account_credentials' => 'heroicon-o-user',
            'student_data' => 'heroicon-o-identification',
            'user_account' => 'heroicon-o-user',
            'account_security' => 'heroicon-o-lock-closed',
            'appearance_settings' => 'heroicon-o-paint-brush',
        ],
    ],
];
