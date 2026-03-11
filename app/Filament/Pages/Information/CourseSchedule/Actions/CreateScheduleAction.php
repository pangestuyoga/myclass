<?php

namespace App\Filament\Pages\Information\CourseSchedule\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Models\CourseSchedule;
use Filament\Support\Enums\Width;

class CreateScheduleAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createSchedule';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->model(CourseSchedule::class)
            ->label('Tambah')
            ->modalHeading('Tambah Jadwal Kuliah')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->extraModalFooterActions(fn (CreateAction $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Simpan dan Tambah Lagi'),
            ])
            ->modalWidth(Width::Large)
            ->schema(fn ($livewire) => $livewire->scheduleFormSchema());
    }
}
