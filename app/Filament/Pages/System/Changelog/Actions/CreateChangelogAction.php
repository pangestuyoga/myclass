<?php

namespace App\Filament\Pages\System\Changelog\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Models\Changelog;
use Filament\Support\Enums\Width;

class CreateChangelogAction extends CreateAction
{
    public static function getDefaultName(): string
    {
        return 'createChangelog';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->model(Changelog::class)
            ->label('Tambah')
            ->modalHeading('Tambah Riwayat')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->extraModalFooterActions(fn (CreateAction $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Simpan dan Tambah Lagi'),
            ])
            ->modalWidth(Width::FourExtraLarge)
            ->schema(fn ($livewire) => $livewire->changelogFormSchema());
    }
}
