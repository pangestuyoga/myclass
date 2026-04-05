<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Filament\Actions\Action;
use Illuminate\Support\Facades\URL;

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
                $date = $session ? $session->date?->toDateString() : null;

                $url = URL::temporarySignedRoute('share.attendance', now()->addHour(), ['course' => $course->id, 'date' => $date]);

                $livewire->js("if (navigator.clipboard) { navigator.clipboard.writeText('{$url}').catch(() => {}); }");

                SystemNotification::send('link_copied')
                    ->send();
            });
    }
}
