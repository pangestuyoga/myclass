<?php

namespace App\Filament\Pages\Information\CourseSchedule\Actions;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Models\CourseSchedule;
use Filament\Support\Icons\Heroicon;

class DeleteScheduleAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'deleteSchedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->record(function (array $arguments): CourseSchedule {
            return CourseSchedule::findOrFail($arguments['schedule']);
        })
            ->label('Hapus')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->iconButton()
            ->tooltip('Hapus')
            ->modalHeading('Hapus Jadwal Kuliah')
            ->modalDescription('Apakah Anda yakin ingin menghapus jadwal ini? Tindakan ini tidak dapat dibatalkan.')
            ->modalSubmitActionLabel('Hapus')
            ->modalCancelActionLabel('Batal');
    }
}
