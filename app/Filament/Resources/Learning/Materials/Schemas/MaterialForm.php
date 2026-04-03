<?php

namespace App\Filament\Resources\Learning\Materials\Schemas;

use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Material;
use App\Settings\GeneralSettings;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->options(function () {
                                return Course::query()
                                    ->where('semester', app(GeneralSettings::class)->current_semester)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->live()
                            ->required(),

                        Select::make('class_session_id')
                            ->label('Pertemuan Ke-')
                            ->placeholder('Pilih Pertemuan (Opsional)')
                            ->options(function ($get, ?Material $record) {
                                $courseId = $get('course_id');
                                if (! $courseId) {
                                    return [];
                                }

                                return ClassSession::where('course_id', $courseId)
                                    ->where(function ($query) use ($record) {
                                        $query->doesntHave('materials');
                                        if ($record && $record->class_session_id) {
                                            $query->orWhere('id', $record->class_session_id);
                                        }
                                    })
                                    ->orderBy('session_number')
                                    ->pluck('session_number', 'id')
                                    ->map(fn ($num) => "Sesi Ke-$num")
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->helperText('Kosongkan jika materi tidak terkait dengan sesi tertentu.'),
                    ])
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('Judul')
                    ->placeholder('Dasar-dasar PHP')
                    ->autocomplete(false)
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Materi tentang pengenalan PHP...')
                    ->rows(3)
                    ->columnSpanFull(),

                AdvancedFileUpload::make('pdf')
                    ->label('PDF')
                    ->multiple()
                    ->reorderable()
                    ->pdfPreviewHeight(400)
                    ->pdfDisplayPage(1)
                    ->pdfToolbar(true)
                    ->pdfZoomLevel(100)
                    ->pdfFitType(PdfViewFit::FIT)
                    ->pdfNavPanes(true)
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(1024 * 3)
                    ->panelLayout('grid')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
