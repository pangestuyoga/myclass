<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
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
            ->visible(fn () => auth()->user()->can('Create:ClassSession'))
            ->color(Color::Orange)
            ->requiresConfirmation()
            ->modalHeading('Generate Sesi Pembelajaran')
            ->modalDescription('Sistem akan men-generate atau memperbarui sesi secara otomatis berdasarkan jadwal mata kuliah ini.')
            ->schema(function ($livewire) {
                $lastSession = $livewire->course->classSessions()->latest('session_number')->first();
                $lastNumber = $lastSession?->session_number ?? 0;
                $nextNumber = $lastNumber + 1;
                $lastDate = $lastSession?->date ? Carbon::parse($lastSession->date) : now();

                return [
                    TextInput::make('total_sessions')
                        ->label('Jumlah Sesi Baru')
                        ->numeric()
                        ->default(function () use ($lastNumber) {
                            return $lastNumber === 0 ? 16 : 1;
                        })
                        ->minValue(1)
                        ->maxValue(32)
                        ->required()
                        ->placeholder('Contoh: 14')
                        ->helperText($lastNumber > 0
                            ? "Sesi terakhir adalah No. {$lastNumber}. Sesi baru akan dimulai dari No. {$nextNumber}."
                            : 'Belum ada sesi. Sesi akan dibuat mulai dari No. 1.'),

                    DatePicker::make('start_date')
                        ->label($lastNumber > 0
                            ? "Tanggal Sesi Ke-{$nextNumber}"
                            : 'Tanggal Pertemuan Ke-1')
                        ->default(function () use ($lastNumber, $lastDate) {
                            // Default to next week if sessions already exist
                            return $lastNumber > 0 ? $lastDate->addWeek()->toDateString() : now()->toDateString();
                        })
                        ->required()
                        ->native(false)
                        ->displayFormat('l, d F Y'),
                ];
            })
            ->action(function (array $data, $livewire) {
                $course = $livewire->course;
                $schedule = $course->courseSchedules()->first();
                $totalNewSessions = (int) ($data['total_sessions'] ?? 1);

                if (! $schedule) {
                    SystemNotification::send('sessions_generated_failed', type: 'danger')
                        ->send();

                    return;
                }

                $lastSession = $course->classSessions()->latest('session_number')->first();
                $lastNumber = $lastSession?->session_number ?? 0;
                $nextNumber = $lastNumber + 1;
                $maxNumber = $lastNumber + $totalNewSessions;

                $startDate = Carbon::parse($data['start_date']);

                for ($i = $nextNumber; $i <= $maxNumber; $i++) {
                    ClassSession::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'session_number' => $i,
                        ],
                        [
                            'date' => $startDate->copy()->addWeeks($i - $nextNumber)->toDateString(),
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                        ]
                    );
                }

                SystemNotification::send('sessions_generated_success', [
                    'count' => $totalNewSessions,
                    'start' => $nextNumber,
                    'end' => $maxNumber,
                ])->send();
            });
    }
}
