<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Colors\Color;

class GenerateSessionsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generateSessions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Generate Sesi')
            ->color(Color::Orange)
            ->requiresConfirmation()
            ->modalHeading('Generate Sesi Pembelajaran')
            ->modalDescription('Sistem akan men-generate atau memperbarui sesi 1 sampai 16 secara otomatis berdasarkan jadwal mata kuliah ini.')
            ->schema([
                DatePicker::make('start_date')
                    ->label('Tanggal Pertemuan Ke-1')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('l, d F Y'),
            ])
            ->action(function (array $data, $livewire) {
                $course = $livewire->course;
                $schedule = $course->courseSchedules()->first();

                if (! $schedule) {
                    SystemNotification::danger(
                        'Oops! Gagal Generate 🚫',
                        'Jadwal belum ditentukan untuk mata kuliah ini, jadi sistem bingung mau buat sesi kapan. 😟',
                        'Eksekusi Dibatalkan',
                        'Penjadwalan resmi belum dikonfigurasi untuk mata kuliah ini. Proses pembuatan sesi otomatis tidak dapat dilanjutkan.'
                    )->send();

                    return;
                }

                $startDate = Carbon::parse($data['start_date']);

                for ($i = 1; $i <= 16; $i++) {
                    ClassSession::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'session_number' => $i,
                        ],
                        [
                            'date' => $startDate->copy()->addWeeks($i - 1)->toDateString(),
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                        ]
                    );
                }

                SystemNotification::success(
                    'Hore! Sesi Siap ✨🚀',
                    '16 Sesi berhasil digenerate dan diperbarui. Selamat mengajar! 🎉',
                    'Verifikasi Sesi Berhasil',
                    'Seluruh 16 sesi pembelajaran telah berhasil dikonfigurasi secara otomatis ke dalam sistem.'
                )->send();
            });
    }
}
