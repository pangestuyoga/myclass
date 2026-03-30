<?php

namespace App\Filament\Resources\Master\Students;

use App\Filament\Resources\Master\Students\Pages\CreateStudent;
use App\Filament\Resources\Master\Students\Pages\EditStudent;
use App\Filament\Resources\Master\Students\Pages\ListStudents;
use App\Filament\Resources\Master\Students\Schemas\StudentForm;
use App\Filament\Resources\Master\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Mahasiswa';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $slug = 'master/students';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
