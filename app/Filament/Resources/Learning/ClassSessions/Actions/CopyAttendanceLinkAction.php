<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Filament\Actions\Action;

class CopyAttendanceLinkAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'copyAttendanceLink';
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
                $date = $session ? $session->date->toDateString() : null;

                $url = route('share.attendance', ['course' => $course->id, 'date' => $date]);

                $livewire->js("if (navigator.clipboard) { navigator.clipboard.writeText('{$url}').catch(() => {}); }");

                SystemNotification::success(
                    'Tautan Berhasil Disalin ✨',
                    'Tautan telah disalin ke clipboard dan siap untuk dibagikan.'
                )->send();
            });
    }
}
