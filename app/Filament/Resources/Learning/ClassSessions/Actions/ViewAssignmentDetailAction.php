<?php

namespace App\Filament\Resources\Learning\ClassSessions\Actions;

use App\Models\Assignment;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\Width;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class ViewAssignmentDetailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'viewAssignmentDetail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn (array $arguments) => Assignment::find($arguments['record'], ['*'])?->title ?? 'Detail Tugas')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->infolist(function (array $arguments) {
                $record = Assignment::find($arguments['record'], ['*']);
                if (! $record) {
                    return [];
                }

                $entries = [
                    TextEntry::make('description')
                        ->label('Deskripsi')
                        ->html()
                        ->columnSpanFull(),
                    TextEntry::make('due_date')
                        ->label('Batas Waktu')
                        ->dateTime('l, d F Y, H:i')
                        ->columnSpanFull(),
                ];

                $mediaEntries = $record->getMedia('assignments')->map(function ($item, $index) {
                    return PdfViewerEntry::make('pdf_'.$index)
                        ->label('Lampiran PDF')
                        ->fileUrl($item->getUrl())
                        ->columnSpanFull();
                })->toArray();

                return array_merge($entries, $mediaEntries);
            });
    }
}
