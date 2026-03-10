<?php

namespace App\Filament\Resources\Master\Lecturers\Pages;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Master\Lecturers\LecturerResource;
use Filament\Resources\Pages\ListRecords;

class ListLecturers extends ListRecords
{
    protected static string $resource = LecturerResource::class;

    protected static ?string $title = 'Dosen';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah'),
        ];
    }
}
