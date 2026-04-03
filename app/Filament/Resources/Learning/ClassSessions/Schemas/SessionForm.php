<?php

namespace App\Filament\Resources\Learning\ClassSessions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(['default' => 2])
                    ->schema([
                        TextInput::make('session_number')
                            ->label('Pertemuan Ke-')
                            ->placeholder('1')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(16)
                            ->autocomplete(false),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->placeholder('Pilih Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('l, d F Y')
                            ->default(now()->toDateString()),

                        TimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->placeholder('08:00')
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Waktu Selesai')
                            ->placeholder('10:00')
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->required(),
                    ]),
            ]);
    }
}
