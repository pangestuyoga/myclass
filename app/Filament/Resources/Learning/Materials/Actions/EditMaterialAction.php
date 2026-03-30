<?php

namespace App\Filament\Resources\Learning\Materials\Actions;

use App\Filament\Actions\Cheerful\EditAction;
use App\Models\Material;
use Filament\Support\Enums\Width;

class EditMaterialAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'editMaterial';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(Width::FourExtraLarge)
            ->fillForm(function (Material $record): array {
                $data = $record->toArray();

                $data['pdf'] = $record->getMedia('materials')
                    ->map(fn ($media) => $media->getPathRelativeToRoot())
                    ->toArray();

                return $data;
            })
            ->using(function (Material $record, array $data): Material {
                $pdf = $data['pdf'] ?? [];
                unset($data['pdf']);

                $record->update($data);

                $existingMedia = $record->getMedia('materials');
                $mediaMap = [];

                foreach ($existingMedia as $media) {
                    if (in_array($media->getPathRelativeToRoot(), $pdf)) {
                        $mediaMap[$media->getPathRelativeToRoot()] = $media;
                    } else {
                        $media->delete();
                    }
                }

                foreach ($pdf as $path) {
                    if (! isset($mediaMap[$path])) {
                        $newMedia = $record->addMediaFromDisk($path, config('filesystems.default'))
                            ->preservingOriginal()
                            ->withCustomProperties([
                                'feature' => 'materials',
                                'date' => now()->toDateString(),
                                'doc_type' => 'pdf',
                            ])
                            ->toMediaCollection('materials');
                        $mediaMap[$path] = $newMedia;
                    }
                }

                foreach ($pdf as $index => $path) {
                    if (isset($mediaMap[$path])) {
                        $mediaMap[$path]->order_column = $index + 1;
                        $mediaMap[$path]->save();
                    }
                }

                return $record;
            });
    }
}
