<?php

namespace App\Filament\Resources\Master\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->placeholder('FSI419')
                    ->autocomplete(false)
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                TextInput::make('name')
                    ->label('Nama')
                    ->placeholder('Sistem Pendukung Keputusan')
                    ->autocomplete(false)
                    ->required()
                    ->maxLength(100),

                TextInput::make('credit')
                    ->label('SKS')
                    ->placeholder('2')
                    ->autocomplete(false)
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(6),

                TextInput::make('semester')
                    ->label('Semester')
                    ->placeholder('3')
                    ->autocomplete(false)
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(8)
                    ->helperText('Semester ke berapa mata kuliah ini diajarkan (1-8).'),

                TextInput::make('lecturer')
                    ->label('Dosen Pengampu')
                    ->placeholder('Ujang')
                    ->maxLength(100)
                    ->helperText('Masukkan nama dosen yang mengampu mata kuliah ini.')
                    ->required(),

            ])
            ->columns(1);
    }
}
