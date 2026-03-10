<?php

namespace App\Filament\Resources\Master\Students\Pages;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Master\Students\StudentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Mahasiswa';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
