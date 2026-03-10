<?php

namespace App\Filament\Resources\Master\Lecturers;

use App\Filament\Resources\Master\Lecturers\Pages\CreateLecturer;
use App\Filament\Resources\Master\Lecturers\Pages\EditLecturer;
use App\Filament\Resources\Master\Lecturers\Pages\ListLecturers;
use App\Filament\Resources\Master\Lecturers\Schemas\LecturerForm;
use App\Filament\Resources\Master\Lecturers\Tables\LecturersTable;
use App\Models\Lecturer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LecturerResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Dosen';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $slug = 'master/lecturers';

    public static function form(Schema $schema): Schema
    {
        return LecturerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LecturersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLecturers::route('/'),
            'create' => CreateLecturer::route('/create'),
            'edit' => EditLecturer::route('/{record}/edit'),
        ];
    }
}
