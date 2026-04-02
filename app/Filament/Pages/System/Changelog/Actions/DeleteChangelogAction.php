<?php

namespace App\Filament\Pages\System\Changelog\Actions;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Models\Changelog;

class DeleteChangelogAction extends DeleteAction
{
    public static function getDefaultName(): string
    {
        return 'deleteChangelog';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(fn (array $arguments): Changelog => Changelog::findOrFail($arguments['record']))
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->link()
            ->tooltip('Hapus')
            ->modalHeading('Hapus Data')
            ->modalDescription('Apakah Anda yakin ingin menghapus riwayat ini? Tindakan ini tidak dapat dibatalkan.')
            ->modalSubmitActionLabel('Hapus')
            ->modalCancelActionLabel('Batal');
    }
}
