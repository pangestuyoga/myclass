<?php

namespace App\Filament\Pages\Information\CourseSchedule\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use App\Models\CourseSchedule;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class EditScheduleAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editSchedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Ubah')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->color('warning')
            ->iconButton()
            ->tooltip('Ubah')
            ->record(function (array $arguments): CourseSchedule {
                return CourseSchedule::findOrFail($arguments['schedule']);
            })
            ->modalHeading('Ubah Jadwal Kuliah')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalWidth(Width::Large)
            ->schema(fn ($livewire) => $livewire->scheduleFormSchema());
    }
}
