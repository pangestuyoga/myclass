<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Illuminate\Support\Str;

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
            ->color('info')
            ->link()
            ->action(function (array $arguments, $livewire) {
                $course = $livewire->course;
                if (empty($course->sharing_token)) {
                    $course->update(['sharing_token' => Str::random(32)]);
                }

                $session = ClassSession::find($arguments['session'] ?? null);
                $date = $session ? $session->date->toDateString() : null;

                $url = route('share.attendance', ['token' => $course->sharing_token, 'date' => $date]);

                $text = "*Info Kelas {$course->name}*\nSesi ke-".($session->session_number ?? '-').' ('.($session->date->translatedFormat('d M Y') ?? '').")\n\nSilakan cek detail/rekap kehadiran melalui tautan ini:\n\n{$url}";

                $escapedText = json_encode($text);

                $livewire->js(
                    <<<JS
const text = {$escapedText};
const url = 'https://wa.me/?text=' + encodeURIComponent(text);

if (navigator.clipboard) {
    navigator.clipboard.writeText(text).catch(() => {});
}
window.open(url, '_blank');
JS
                );
            });
    }
}
