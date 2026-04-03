<?php

namespace App\Filament\Resources\Learning\Materials;

use App\Filament\Resources\Learning\Materials\Pages\ManageMaterials;
use App\Filament\Resources\Learning\Materials\Schemas\MaterialForm;
use App\Filament\Resources\Learning\Materials\Tables\MaterialTable;
use App\Models\Material;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Materi';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'learning/materials';

    public static function form(Schema $schema): Schema
    {
        return MaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMaterials::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', function (Builder $query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            });
    }
}
