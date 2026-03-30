<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\ClassSession;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;

class ShareAssignmentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'shareAssignment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Bagikan Tugas')
            ->icon('heroicon-o-paper-airplane')
            ->color(Color::Fuchsia)
            ->link()
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-paper-airplane')
            ->modalHeading('Bagikan Info Tugas')
            ->modalDescription('Pilih metode untuk membagikan tautan publik status pengumpulan tugas sesi ini.')
            ->modalSubmitActionLabel('Ke WhatsApp')
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                CopyAssignmentLinkAction::make(),
            ])
            ->action(function (array $arguments, $livewire) {
                $course = $livewire->course;
                $session = ClassSession::find($arguments['session'] ?? null);

                $url = URL::temporarySignedRoute('share.assignment', now()->addHour(), [
                    'course' => $course->id,
                    'session_id' => $session ? $session->id : null,
                ]);

                $text = "*Info Tugas Kelas {$course->name}*\nSesi ke-".($session->session_number ?? '-').' ('.($session->date->translatedFormat('d M Y') ?? '').")\n\nSilakan cek status pengumpulan tugas melalui tautan publik berikut:\n\n{$url}";

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
