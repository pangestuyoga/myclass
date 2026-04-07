<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use Filament\Actions\Action;

class MarkAsSentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'markAsSentAction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(function (array $arguments, ?ClassSession $record) {
            $session = $record ?? ClassSession::find($arguments['record'] ?? null);

            return $session?->is_sent_to_lecturer ? 'Buka Kunci' : 'Tandai Terkirim';
        })
            ->icon(function (array $arguments, ?ClassSession $record) {
                $session = $record ?? ClassSession::find($arguments['record'] ?? null);

                return $session?->is_sent_to_lecturer ? 'heroicon-o-lock-open' : 'heroicon-o-check-circle';
            })
            ->color(function (array $arguments, ?ClassSession $record) {
                $session = $record ?? ClassSession::find($arguments['record'] ?? null);

                return $session?->is_sent_to_lecturer ? 'warning' : 'success';
            })
            ->link()
            ->visible(fn () => auth()->user()->hasRole(['Kosma', 'Developer']))
            ->action(function (array $arguments, $livewire, ?ClassSession $record) {
                $session = $record ?? ClassSession::find($arguments['record'] ?? null);

                if (! $session) {
                    return;
                }

                $session->update([
                    'is_sent_to_lecturer' => ! $session->is_sent_to_lecturer,
                ]);

                SystemNotification::send($session->is_sent_to_lecturer ? 'session_sent' : 'session_unlocked')
                    ->send();

                if (isset($livewire->sessions)) {
                    unset($livewire->sessions);
                }
            });
    }
}
