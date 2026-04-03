<?php

namespace App\Filament\Resources\Learning\Assignments\Schemas;

use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Schemas\Schema;

class SubmitAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AdvancedFileUpload::make('file')
                    ->label(fn ($get) => $get('is_resubmit') ? 'Ganti Berkas PDF (Opsional)' : 'Lampiran Berkas Tugas')
                    ->pdfPreviewHeight(400)
                    ->pdfDisplayPage(1)
                    ->pdfToolbar(true)
                    ->pdfZoomLevel(100)
                    ->pdfFitType(PdfViewFit::FIT)
                    ->pdfNavPanes(true)
                    ->disk(config('filesystems.default'))
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(1024 * 5)
                    ->directory('submissions/'.now()->toDateString())
                    ->required(fn ($get) => ! $get('is_resubmit'))
                    ->hiddenLabel()
                    ->helperText('Hanya file PDF dengan ukuran maksimal 5MB.')
                    ->columnSpanFull(),
            ]);
    }
}
