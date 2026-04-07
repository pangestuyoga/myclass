<?php

namespace App\Filament\Pages\System\Changelog\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\Changelog;
use Filament\Actions\Action;

class MarkAsReadAction extends Action
{
    public static function getDefaultName(): string
    {
        return 'markAsReadAction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(fn (array $arguments): Changelog => Changelog::findOrFail($arguments['record']))
            ->label('Tandai Sudah Baca')
            ->icon('heroicon-m-check-badge')
            ->size('sm')
            ->color('primary')
            ->action(function (Changelog $record, $livewire) {
                if (! $record->users()->where('user_id', auth()->id())->exists()) {
                    $record->users()->attach(auth()->id(), ['read_at' => now()]);

                    SystemNotification::send('changelog_read', ['title' => $record->title])->send();

                    $livewire->dispatch('refresh-changelog');
                    $livewire->dispatch('refresh-expansion', id: $livewire->changelogs()->first()?->id);
                }
            });
    }
}
