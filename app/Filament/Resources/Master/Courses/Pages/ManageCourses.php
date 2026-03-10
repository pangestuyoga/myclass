<?php

namespace App\Filament\Resources\Master\Courses\Pages;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Master\Courses\CourseResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageCourses extends ManageRecords
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = 'Mata Kuliah';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->modalHeading('Tambah Mata Kuliah')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->extraModalFooterActions(fn (CreateAction $action): array => [
                    $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                        ->label('Simpan dan Tambah Lagi'),
                ])
                ->modalWidth(Width::Large),
        ];
    }
}
