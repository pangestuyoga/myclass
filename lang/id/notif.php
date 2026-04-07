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
        'submission_overdue' => [
            'title' => 'Yah, Batas Waktu Udah Habis! 😭⏳',
            'body' => 'Maaf ya, kamu udah nggak bisa kumpulin tugas ini lagi karena waktunya udah lewat. Tetap semangat buat tugas selanjutnya! 💪',
        ],
        'submission_update' => [
            'title' => 'Kuy Perbarui Tugasmu! 🔄✨',
            'body' => 'Ada yang mau dirubah? Tenang, kamu masih bisa update file tugasmu selama belum lewat deadline kok! 🚀✨',
        ],
        'submission_create' => [
            'title' => 'Kumpulkan Tugas Sekarang! 🚀📚',
            'body' => 'Ayo, jangan sampai telat ya! Segera upload file tugas terbaikmu biar tenang dan nilai maksimal! 💪🎨',
        ],
        'user_activated' => [
            'title' => 'Pengguna Diaktifkan! ✅✨',
            'body' => 'Status akun pengguna berhasil diaktifkan kembali. Siap beraksi! 🚀',
        ],
        'user_deactivated' => [
            'title' => 'Pengguna Dinonaktifkan ⛔👋',
            'body' => 'Status akun pengguna telah berhasil dinonaktifkan. Istirahat dulu ya... 😴',
        ],
        'changelog_read' => [
            'title' => 'Mantap! Sudah Dibaca ✅✨',
            'body' => 'Pembaruan ":title" sudah ditandai. Makin update makin jago! 🚀🔥',
        ],
        'assignment_sent' => [
            'title' => 'Tugas Berhasil Dikirim! 🚀✨',
            'body' => 'Tugas kolektif sudah berhasil ditandai terkirim ke dosen. Status pengumpulan resmi ditutup! 🔒',
        ],
        'assignment_unlocked' => [
            'title' => 'Kunci Tugas Dibuka! 🔓✨',
            'body' => 'Mahasiswa sekarang diperbolehkan kembali untuk mengumpulkan atau memperbarui tugas mereka. 📝',
        ],
        'submission_locked' => [
            'title' => 'Tugas Terkunci! 🔒',
            'body' => 'Mohon maaf, tugas ini sudah ditandai terkirim ke dosen oleh Kosma dan tidak dapat diubah lagi. 🚫',
        ],
        'session_sent' => [
            'title' => 'Sesi Terkirim! 🚀✨',
            'body' => 'Laporan presensi untuk sesi ini telah resmi ditandai terkirim ke dosen. Data aman tersimpan! ✅',
        ],
        'session_unlocked' => [
            'title' => 'Kunci Presensi Dibuka! 🔓✨',
            'body' => 'Mahasiswa sekarang bisa melakukan presensi kembali untuk sesi ini. 📝',
        ],
        'attendance_closed' => [
            'title' => 'Yah, Presensi Sudah Tutup! 😭⌛',
            'body' => 'Maaf ya, sesi presensi ini sudah ditutup oleh Kosma. Lain kali jangan sampai terlambat ya! 💪',
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
                'description' => 'Jangan lupa presensi ya kalau kelasnya udah mulai! Biar tercatat hadir. 💪',
            ],
            'missed_attendance' => [
                'title' => 'Presensi Terlewat! ⌛🏃‍♂️',
                'description' => 'Ayo segera presensi buat kelas yang udah lewat ini sebelum sesi ditutup! Jangan sampai alpa ya. 💪✨',
            ],
            'attendance_history' => [
                'title' => 'Jejak Presensimu 🕰️📜',
                'description' => 'Di sini kamu bisa ngecek semua histori kehadiranmu sebelumnya. Rajin-rajin ya! 🎓',
            ],
            'empty_attendance' => [
                'title' => 'Yah, Presensi Belum Muncul! 💁‍♂️🌀',
                'description' => 'Sabar ya, setelah jadwal perkuliahan kamu muncul baru bisa diabsen di sini. Pantau terus jam kuliahnya! 🕒💪',
            ],
            'assignment_list' => [
                'title' => 'Daftar Tugas Bikin Lemes! 🤣📒',
                'description' => 'Yuk, cek dan kumpulin tugasmu biar tenang hidupnya! Klik aja di tugasnya ya. 🚀👨‍💻',
            ],
            'empty_assignment' => [
                'title' => 'Hore, Belum Ada Tugas! 🎊✨',
                'description' => 'Belum ada tugas baru yang perlu dikerjakan. Hidup tenang tanpa beban tugas itu asik ya! 😊🌈',
            ],
            'changelog' => [
                'title' => 'Catatan Rilis 📜✨',
                'description' => 'Kepoin apa aja yang baru, fitur kece, dan perbaikan bug biar nggak kudet! 🚀🔥',
            ],
            'empty_changelog' => [
                'title' => 'Belum Ada Catatan Rilis 📭',
                'description' => 'Belum ada update yang tercatat nih. Buat mulai catat riwayat pembaruan pertama! 🚀',
            ],
            'today_sessions' => [
                'title' => 'Sesi Kelas Hari Ini ✨🚀',
                'description' => 'Daftar sesi seru yang udah dijadwalkan buat hari ini (:date). Semangat belajar! 💪',
            ],
            'semester_courses' => [
                'title' => 'Daftar Mata Kuliah Semester Ini 📚🎓',
                'description' => 'Pilih aja mata kuliahnya buat ngecek materi sama tugas-tugas seru lainnya! 🧐✨',
            ],
            'empty_course_sessions' => [
                'title' => 'Duh, Belum Ada Sesi Kelas! 🏢🛌',
                'description' => 'Belum ada sesi perkuliahan yang dibuat buat mata kuliah ini. Waktunya istirahat mungkin? 😴✨',
            ],
            'empty_today_sessions' => [
                'title' => 'Yah, Gak Ada Kelas Hari Ini! 😴🏖️',
                'description' => 'Hari ini kosong melompong. Kamu bisa rebahan sebentar atau kejar materi lain! ✨',
            ],
            'empty_semester_courses' => [
                'title' => 'Belum Ada Mata Kuliah Nih! 📚😴',
                'description' => 'Semester ini kayanya masih kosong. Coba hubungi admin kalau ada yang salah ya! ✨',
            ],
            'assignment_status' => [
                'not_submitted' => 'Belum Dikumpulkan',
                'submitted' => '✓ Sudah Dikumpulkan',
                'overdue' => '⏰ Waktu Habis',
                'waiting_leader' => 'Menunggu Ketua',
            ],
            'group_submission_hint' => [
                'title' => 'Tugas Kelompok',
                'description' => 'Hanya <strong>Ketua Kelompok</strong> yang dapat mengumpulkan atau memperbarui tugas ini. Silakan hubungi ketua kelompok Anda.',
            ],
            'attendance_list' => [
                'title' => 'Daftar Kehadiran 📝✨',
            ],
            'session_info' => [
                'title' => 'Informasi Sesi 🏢✨',
            ],
            'submission_list' => [
                'title' => 'Daftar Pengumpulan 🚀✨',
            ],
            'assignment_info_heading' => [
                'title' => 'Informasi Tugas 📚✨',
            ],
            'home' => [
                'hero_title' => 'Kuliah Jadi Makin Simpel! 🚀📚',
                'hero_subtitle' => 'Gak Ribet Lagi.',
                'feature_session' => 'Info Sesi Kuliah 🏢📅',
                'feature_session_desc' => 'Pantau jadwal kelasmu biar nggak ketinggalan info penting ya! 🕒✨',
                'feature_task' => 'Tugas & Materi 📝📚',
                'feature_task_desc' => 'Ambil materi dan kumpulin tugas dalam satu tempat, praktis banget! 🚀📂',
                'feature_attendance' => 'Presensi Digital 🙋‍♂️✅',
                'feature_attendance_desc' => 'Absen makin gampang, tinggal klik langsung tercatat hadir. Gapake lama! 🔥⚡',
                'feature_group' => 'Grup Belajar 👯‍♀️💬',
                'feature_group_desc' => 'Bikin kelompok biar belajar bareng temen makin asik dan seru! 🌈✨',
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
            'today_sessions' => 'heroicon-o-sparkles',
            'semester_courses' => 'heroicon-o-book-open',
            'empty_course_sessions' => 'heroicon-o-presentation-chart-bar',
            'empty_today_sessions' => 'heroicon-o-calendar-days',
            'empty_semester_courses' => 'heroicon-o-academic-cap',
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
        'submission_overdue' => [
            'title' => 'Batas Waktu Telah Berakhir',
            'body' => 'Masa pengumpulan tugas ini telah berakhir. Sistem tidak lagi menerima unggahan baru maupun pembaruan untuk penugasan ini.',
        ],
        'submission_update' => [
            'title' => 'Perbarui Pengumpulan',
            'body' => 'Anda diperbolehkan untuk mengunggah ulang atau memperbarui berkas tugas selama masa pengumpulan masih aktif.',
        ],
        'submission_create' => [
            'title' => 'Kumpulkan Tugas',
            'body' => 'Gunakan formulir ini untuk mengunggah berkas tugas Anda sesuai dengan ketentuan yang berlaku.',
        ],
        'user_activated' => [
            'title' => 'Aktivasi Akun Berhasil',
            'body' => 'Status akun pengguna terpilih telah diubah menjadi aktif.',
        ],
        'user_deactivated' => [
            'title' => 'Penonaktifan Akun Berhasil',
            'body' => 'Status akun pengguna terpilih telah diubah menjadi tidak aktif.',
        ],
        'changelog_read' => [
            'title' => 'Pembaruan Berhasil Ditandai',
            'body' => 'Catatan rilis ":title" telah berhasil ditandai sebagai sudah dibaca.',
        ],
        'assignment_sent' => [
            'title' => 'Konfirmasi Pengiriman Berhasil',
            'body' => 'Seluruh berkas tugas kolektif telah berhasil ditandai sebagai terkirim ke dosen pengampu.',
        ],
        'assignment_unlocked' => [
            'title' => 'Kunci Akses Dibuka',
            'body' => 'Otoritas pengumpulan tugas telah diaktifkan kembali untuk seluruh mahasiswa terkait.',
        ],
        'submission_locked' => [
            'title' => 'Akses Pengumpulan Ditutup',
            'body' => 'Tugas ini telah dalam status final (terkirim ke dosen). Perubahan data tidak lagi dimungkinkan.',
        ],
        'session_sent' => [
            'title' => 'Konfirmasi Pengiriman Laporan',
            'body' => 'Laporan presensi sesi perkuliahan telah berhasil ditandai sebagai terkirim ke dosen pengampu.',
        ],
        'session_unlocked' => [
            'title' => 'Kunci Akses Presensi Dibuka',
            'body' => 'Otoritas presensi telah diaktifkan kembali. Seluruh mahasiswa terkait dapat melakukan pengisian data kehadiran.',
        ],
        'attendance_closed' => [
            'title' => 'Akses Presensi Ditutup',
            'body' => 'Sesi presensi untuk mata kuliah ini telah ditutup secara resmi. Anda tidak dapat melakukan pengisian data kehadiran lagi.',
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
            'missed_attendance' => [
                'title' => 'Presensi Terlewat',
                'description' => 'Segera lakukan presensi untuk sesi perkuliahan yang telah berlalu berikut sebelum akses ditutup.',
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
            'empty_assignment' => [
                'title' => 'Tidak Ada Tugas Terdaftar',
                'description' => 'Belum terdapat tugas baru yang perlu Anda kerjakan untuk saat ini.',
            ],
            'changelog' => [
                'title' => 'Catatan Rilis',
                'description' => 'Lihat riwayat perbaikan bug, pembaruan aplikasi, dan rilis fitur terbaru secara mendalam.',
            ],
            'empty_changelog' => [
                'title' => 'Tidak Ada Riwayat Pembaruan',
                'description' => 'Belum ada catatan rilis yang tersedia. Buat untuk mendokumentasikan pembaruan aplikasi.',
            ],
            'today_sessions' => [
                'title' => 'Sesi Hari Ini',
                'description' => 'Sesi yang dijadwalkan pada :date',
            ],
            'semester_courses' => [
                'title' => 'Daftar Mata Kuliah Semester Ini',
                'description' => 'Pilih mata kuliah untuk melihat dan mengelola semua riwayat sesi.',
            ],
            'empty_course_sessions' => [
                'title' => 'Tidak ada data yang ditemukan',
                'description' => 'Belum ada sesi perkuliahan yang dibuat untuk mata kuliah ini.',
            ],
            'empty_today_sessions' => [
                'title' => 'Tidak ada data yang ditemukan',
                'description' => 'Tidak ada sesi perkuliahan yang dijadwalkan untuk hari ini.',
            ],
            'empty_semester_courses' => [
                'title' => 'Mata Kuliah Belum Terdaftar',
                'description' => 'Belum ada mata kuliah yang terdaftar untuk semester aktif ini.',
            ],
            'assignment_status' => [
                'not_submitted' => 'Belum Dikumpulkan',
                'submitted' => 'Sudah Dikumpulkan',
                'overdue' => 'Waktu Habis',
                'waiting_leader' => 'Menunggu Ketua',
            ],
            'group_submission_hint' => [
                'title' => 'Tugas Kelompok',
                'description' => 'Hanya <strong>Ketua Kelompok</strong> yang memiliki otoritas untuk memperbarui atau mengumpulkan tugas kelompok.',
            ],
            'attendance_list' => [
                'title' => 'Daftar Kehadiran',
            ],
            'session_info' => [
                'title' => 'Informasi Sesi',
            ],
            'submission_list' => [
                'title' => 'Daftar Pengumpulan',
            ],
            'assignment_info_heading' => [
                'title' => 'Informasi Tugas',
            ],
            'home' => [
                'hero_title' => 'Sistem Manajemen Kelas',
                'hero_subtitle' => 'Sederhana & Terintegrasi.',
                'feature_session' => 'Manajemen Sesi',
                'feature_session_desc' => 'Pemantauan kelas dan penjadwalan terstruktur dan mudah dilihat.',
                'feature_task' => 'Tugas & Materi',
                'feature_task_desc' => 'Pengelolaan repositori perkuliahan dan pengumpulan tugas.',
                'feature_attendance' => 'Presensi Digital',
                'feature_attendance_desc' => 'Sistem pendataan kehadiran yang praktis, cepat, dan real-time.',
                'feature_group' => 'Grup Belajar',
                'feature_group_desc' => 'Ruang kolaborasi, diskusi, dan obrolan untuk saling terhubung.',
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
            'today_sessions' => 'heroicon-o-bolt',
            'semester_courses' => 'heroicon-o-academic-cap',
            'empty_course_sessions' => 'heroicon-o-presentation-chart-bar',
            'empty_today_sessions' => 'heroicon-o-calendar-days',
            'empty_semester_courses' => 'heroicon-o-academic-cap',
        ],
    ],
];
