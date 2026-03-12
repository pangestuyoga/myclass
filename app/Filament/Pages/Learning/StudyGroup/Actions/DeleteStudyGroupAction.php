<?php

namespace App\Filament\Pages\Learning\StudyGroup\Actions;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Models\StudyGroup;
use Filament\Support\Icons\Heroicon;

class DeleteStudyGroupAction extends DeleteAction
{
    public static function getDefaultName(): string
    {
        return 'deleteStudyGroup';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(fn (array $arguments): StudyGroup => StudyGroup::findOrFail($arguments['studyGroup']))
            ->label('Hapus')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->iconButton()
            ->tooltip('Hapus')
            ->modalHeading(fn (StudyGroup $record) => "Hapus {$record->name}")
            ->modalDescription('Apakah Anda yakin ingin menghapus kelompok ini? Tindakan ini tidak dapat dibatalkan.')
            ->modalSubmitActionLabel('Hapus')
            ->modalCancelActionLabel('Batal');
    }
}
