<?php

namespace App\Filament\Pages\System\Changelog\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use App\Models\Changelog;
use Filament\Support\Enums\Width;

class EditChangelogAction extends EditAction
{
    public static function getDefaultName(): string
    {
        return 'editChangelog';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Ubah')
            ->color('warning')
            ->icon('heroicon-o-pencil-square')
            ->link()
            ->tooltip('Ubah')
            ->record(fn (array $arguments): Changelog => Changelog::findOrFail($arguments['record']))
            ->modalHeading('Ubah Data')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalWidth(Width::ThreeExtraLarge)
            ->schema(fn ($livewire) => $livewire->changelogFormSchema())
            ->fillForm(fn (Changelog $record): array => $record->toArray());
    }
}
