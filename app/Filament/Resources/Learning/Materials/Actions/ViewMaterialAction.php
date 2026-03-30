<?php

namespace App\Filament\Resources\Learning\Materials\Actions;

use App\Models\Material;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class ViewMaterialAction extends ViewAction
{
    public static function getDefaultName(): ?string
    {
        return 'viewMaterial';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(Width::FourExtraLarge)
            ->modalHeading('Lihat Materi')
            ->schema(function (Material $record) {
                return $record->getMedia('materials')->map(function ($item, $index) {
                    return PdfViewerEntry::make('pdf_'.$index)
                        ->hiddenLabel()
                        ->fileUrl($item->getUrl())
                        ->columnSpanFull();
                })->toArray();
            })
            ->visible(fn (Material $record) => $record->hasMedia('materials'));
    }
}
