<?php

namespace App\Filament\Resources\Learning\ClassSessions\RelationManagers;

use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Actions\DefaultBulkActions;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Presensi Mahasiswa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->placeholder('Pilih Mahasiswa')
                    ->relationship('student', 'full_name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->helperText('Pilih mahasiswa yang hadir pada pertemuan ini.'),

                DateTimePicker::make('attended_at')
                    ->label('Waktu Presensi')
                    ->default(now())
                    ->native(false)
                    ->helperText('Tanggal dan jam mahasiswa melakukan presensi.'),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.full_name')
            ->columns([
                Tables\Columns\TextColumn::make('student.student_number')
                    ->label('NIM')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attended_at')
                    ->label('Waktu Presensi')
                    ->dateTime('l, d F Y H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->attended_at->format('H:i').' WIB')
                    ->color('primary'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth(Width::Large),
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth(Width::Large),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Presensi Mahasiswa'),
                ]),
            ])
            ->emptyStateDescription('Belum ada data presensi untuk pertemuan ini.');
    }
}
