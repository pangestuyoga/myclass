<?php

namespace App\Filament\Resources\Learning\Assignments\Schemas;

use App\Enums\AssignmentType;
use App\Filament\Resources\Learning\Assignments\Actions\SelectAllStudentsAction;
use App\Models\Course;
use App\Models\Student;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tugas')
                    ->schema([

                        TextInput::make('title')
                            ->label('Judul')
                            ->placeholder('Tugas Pemrograman Web')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
                            ->autofocus()
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                Select::make('course_id')
                                    ->label('Mata Kuliah')
                                    ->options(function () {
                                        return Course::all()
                                            ->groupBy('semester')
                                            ->mapWithKeys(function ($courses, $semester) {
                                                return [
                                                    "Semester $semester" => $courses->pluck('name', 'id')->toArray(),
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->live()
                                    ->required()
                                    ->optionsLimit(100),

                                DateTimePicker::make('due_date')
                                    ->label('Batas Waktu')
                                    ->placeholder('Pilih Tanggal & Waktu')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('l, d F Y H:i'),

                                Select::make('type')
                                    ->label('Tipe Tugas')
                                    ->options(AssignmentType::class)
                                    ->required()
                                    ->native(false)
                                    ->default(AssignmentType::Individual->value)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('student_ids', []);
                                        $set('study_group_ids', []);
                                    }),
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Buatlah program web yang dapat melakukan ...')
                            ->nullable()
                            ->columnSpanFull(),

                        AdvancedFileUpload::make('pdf')
                            ->label('Lampiran PDF')
                            ->pdfPreviewHeight(400)
                            ->pdfDisplayPage(1)
                            ->pdfToolbar(true)
                            ->pdfZoomLevel(100)
                            ->pdfFitType(PdfViewFit::FIT)
                            ->pdfNavPanes(true)
                            ->disk(config('filesystems.default'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(1024 * 5)
                            ->directory('assignments/'.now()->toDateString())
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Target Penugasan')
                    ->schema([
                        Select::make('student_ids')
                            ->label('Mahasiswa')
                            ->options(Student::pluck('full_name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->hintAction(SelectAllStudentsAction::make())
                            ->helperText('Pilih mahasiswa yang menjadi target tugas ini.')
                            ->columnSpanFull()
                            ->hidden(fn (Get $get) => in_array($get('type'), [AssignmentType::Group, AssignmentType::Group->value]))
                            ->required(fn (Get $get) => ! in_array($get('type'), [AssignmentType::Group, AssignmentType::Group->value])),

                        Callout::make('Penugasan Kelompok Otomatis')
                            ->description('Tugas ini akan otomatis ditujukan ke SEMUA kelompok belajar pada mata kuliah ini.')
                            ->info()
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => in_array($get('type'), [AssignmentType::Group, AssignmentType::Group->value])),
                    ]),
            ])
            ->columns(1);
    }
}
