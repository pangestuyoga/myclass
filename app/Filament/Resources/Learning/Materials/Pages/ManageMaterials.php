<?php

namespace App\Filament\Resources\Learning\Materials\Pages;

use App\Filament\Resources\Learning\Materials\Actions\CreateMaterialAction;
use App\Filament\Resources\Learning\Materials\MaterialResource;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterials extends ManageRecords
{
    protected static string $resource = MaterialResource::class;

    protected static ?string $title = 'Materi';

    protected function getHeaderActions(): array
    {
        return [
            CreateMaterialAction::make(),
        ];
    }
}
