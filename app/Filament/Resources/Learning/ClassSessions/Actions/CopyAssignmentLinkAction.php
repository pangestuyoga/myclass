<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

class CopyAssignmentLinkAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'copyAssignmentLink';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Salin Tautan')
            ->color('gray')
            ->icon('heroicon-o-clipboard-document')
            ->action(function (array $arguments, $livewire) {
                $course = $livewire->course;
                $session = ClassSession::find($arguments['session'] ?? null);

                $url = URL::temporarySignedRoute('share.assignment', now()->addHour(), [
                    'course' => $course->id,
                    'session_id' => $session ? $session->id : null,
                ]);

                $livewire->js("if (navigator.clipboard) { navigator.clipboard.writeText('{$url}').catch(() => {}); }");

                SystemNotification::success(
                    'Tautan Berhasil Disalin ✨🚀',
                    'Tautan telah disalin ke clipboard dan siap untuk dibagikan. 📋',
                    'Penyalinan Tautan Berhasil',
                    'Tautan URL telah berhasil disalin ke papan klip sistem.'
                )->send();
            });
    }
}
