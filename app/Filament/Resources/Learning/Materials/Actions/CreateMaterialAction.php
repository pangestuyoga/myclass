<?php

namespace App\Filament\Resources\Learning\Materials\Actions;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Models\Material;
use Filament\Support\Enums\Width;

class CreateMaterialAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'createMaterial';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Tambah')
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
            ->modalWidth(Width::FourExtraLarge);
    }
}
