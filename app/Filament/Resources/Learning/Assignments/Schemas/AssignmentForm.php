<?php

namespace App\Filament\Resources\Learning\Assignments\Schemas;

use App\Enums\AssignmentType;
use App\Enums\NotifStyle;
use App\Filament\Resources\Learning\Assignments\Actions\SelectAllStudentsAction;
use App\Filament\Support\SystemNotification;
use App\Models\Assignment;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
use App\Settings\GeneralSettings;
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
                Section::make(SystemNotification::getMessage('Informasi Tugas 📝✨', 'Informasi Tugas'))
                    ->description(SystemNotification::getMessage('Jelaskan tugasnya apa, kapan deadline-nya, dan buat sesi berapa. Semangat! 💪', 'Lengkapi informasi detail penugasan.'))
                    ->icon(SystemNotification::getNotifStyle() === NotifStyle::Cheerful ? 'heroicon-o-document-text' : 'heroicon-o-information-circle')
                    ->schema([

                        TextInput::make('title')
                            ->label('Judul')
                            ->placeholder('Tugas Pemrograman Web')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
                            ->columnSpanFull(),

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
                                    ->options(function (Get $get, ?Assignment $record) {
                                        $courseId = $get('course_id');
                                        if (! $courseId) {
                                            return [];
                                        }

                                        return ClassSession::where('course_id', $courseId)
                                            ->where(function ($query) use ($record) {
                                                $query->doesntHave('assignments');
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
                                    ->helperText('Kosongkan jika tugas tidak terkait dengan sesi tertentu.'),

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
                                        $set('class_session_id', null);
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

                Section::make(SystemNotification::getMessage('Target Penugasan 🎯🧑‍🎓', 'Target Penugasan'))
                    ->description(SystemNotification::getMessage('Pilih siapa saja yang bakal ngerjain tugas ini. Jangan sampai salah sasaran ya! 🏹', 'Tentukan sasaran penerima penugasan ini.'))
                    ->icon(SystemNotification::getNotifStyle() === NotifStyle::Cheerful ? 'heroicon-o-users' : 'heroicon-o-user-group')
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
