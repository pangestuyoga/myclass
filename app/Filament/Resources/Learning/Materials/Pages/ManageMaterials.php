<?php

namespace App\Filament\Resources\Learning\Materials\Pages;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Learning\Materials\MaterialResource;
use App\Models\Material;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageMaterials extends ManageRecords
{
    protected static string $resource = MaterialResource::class;

    protected static ?string $title = 'Materi';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah')
                ->modalHeading('Tambah Materi')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->extraModalFooterActions(fn (CreateAction $action): array => [
                    $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                        ->label('Simpan dan Tambah Lagi'),
                ])
                ->using(function (array $data): Material {
                    $pdf = $data['pdf'] ?? [];
                    unset($data['pdf']);

                    $record = Material::create($data);

                    if (! empty($pdf)) {
                        foreach ($pdf as $filePath) {
                            $record->addMediaFromDisk($filePath, config('filesystems.default'))
                                ->preservingOriginal()
                                ->withCustomProperties([
                                    'feature' => 'materials',
                                    'date' => now()->toDateString(),
                                    'doc_type' => 'pdf',
                                ])
                                ->toMediaCollection('materials');
                        }
                    }

                    return $record;
                })
                ->modalWidth(Width::FourExtraLarge),
        ];
    }
}
