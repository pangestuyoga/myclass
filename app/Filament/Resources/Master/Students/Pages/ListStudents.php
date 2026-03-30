<?php

namespace App\Filament\Resources\Master\Students\Pages;

use App\Filament\Resources\Master\Students\Actions\CreateStudentAction;
use App\Filament\Resources\Master\Students\StudentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Mahasiswa';

    protected function getHeaderActions(): array
    {
        return [
            CreateStudentAction::make(),
        ];
    }
}
