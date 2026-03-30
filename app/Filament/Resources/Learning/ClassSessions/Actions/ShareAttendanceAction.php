<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;

class ShareAttendanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'shareAttendance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Bagikan')
            ->icon('heroicon-o-share')
            ->color(Color::Teal)
            ->link()
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-share')
            ->modalHeading('Bagikan Info Presensi')
            ->modalDescription('Pilih metode untuk membagikan tautan publik status presensi sesi ini.')
            ->modalSubmitActionLabel('Ke WhatsApp')
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                CopyAttendanceLinkAction::make(),
            ])
            ->action(function (array $arguments, $livewire) {
                $course = $livewire->course;
                $session = ClassSession::find($arguments['session'] ?? null);
                $date = $session ? $session->date->toDateString() : null;

                $url = route('share.attendance', ['course' => $course->id, 'date' => $date]);

                $text = "*Info Kelas {$course->name}*\nSesi ke-".($session->session_number ?? '-').' ('.($session->date->translatedFormat('d M Y') ?? '').")\n\nSilakan cek detail/rekap kehadiran melalui tautan ini:\n\n{$url}";

                $escapedText = json_encode($text);

                $livewire->js(
                    <<<JS
const text = {$escapedText};
const url = 'https://wa.me/?text=' + encodeURIComponent(text);
window.open(url, '_blank');
JS
                );
            });
    }
}
