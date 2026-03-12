<?php

namespace App\Filament\Resources\Master\Lecturers\Schemas;

use App\Enums\Sex;
use App\Models\Course;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LecturerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Dosen')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->placeholder('John Doe')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
                            ->autofocus()
                            ->helperText('Masukkan nama lengkap dosen beserta gelar akademik.')
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('lecturer_identification_number')
                                    ->label('NIDN')
                                    ->placeholder('1234567890')
                                    ->autocomplete(false)
                                    ->required()
                                    ->numeric()
                                    ->rules(['digits:10'])
                                    ->unique(ignoreRecord: true),

                                TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->placeholder('0812 3456 78910')
                                    ->autocomplete(false)
                                    ->required()
                                    ->maxLength(20)
                                    ->minLength(10)
                                    ->rules(['regex:/^[0-9 ]+$/'])
                                    ->mask('9999 9999 99999'),

                                Radio::make('sex')
                                    ->label('Jenis Kelamin')
                                    ->options(Sex::class)
                                    ->required()
                                    ->inline(),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Jl. Contoh No. 123, Kota, Provinsi')
                            ->autocomplete(false)
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('date_of_birth')
                                    ->label('Tanggal Lahir')
                                    ->placeholder('Pilih Tanggal')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('l, d F Y')
                                    ->maxDate(Carbon::now()->subYears(25)),

                                TextInput::make('place_of_birth')
                                    ->label('Tempat Lahir')
                                    ->placeholder('Purwakarta')
                                    ->autocomplete(false)
                                    ->required()
                                    ->maxLength(50)
                                    ->minLength(3),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Mata Kuliah')
                    ->description('Daftar mata kuliah yang diampu oleh dosen ini.')
                    ->schema([
                        Select::make('courses')
                            ->label('Mata Kuliah')
                            ->multiple()
                            ->relationship('courses', 'name')
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
                            ->preload()
                            ->live()
                            ->required(),
                    ]),
            ])
            ->columns(1);
    }
}
