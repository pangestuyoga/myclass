<?php

namespace App\Filament\Resources\Master\Courses;

use App\Enums\RoleEnum;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\RowIndexColumn;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Master\Courses\Actions\EditCourseAction;
use App\Filament\Resources\Master\Courses\Pages\ManageCourses;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|UnitEnum|null $navigationGroup = 'Master';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Mata Kuliah';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'master/courses';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->placeholder('FSI419')
                    ->autocomplete(false)
                    ->autofocus()
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...RowIndexColumn::make(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->sortable()
                    ->description(fn (Course $course) => $course->code),

                TextColumn::make('credit')
                    ->label('SKS')
                    ->sortable(),

                TextColumn::make('lecturer')
                    ->label('Dosen Pengampu')
                    ->placeholder('Belum ditentukan')
                    ->searchable(),

                ...TimestampColumns::make(),
            ])
            ->filters([
                TrashedFilter::make()
                    ->native(false)
                    ->visible(fn () => auth()->user()->hasRole(RoleEnum::Developer)),
            ])
            ->recordActions([
                EditCourseAction::make(),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Mata Kuliah'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
            ->defaultSort('code', 'desc')
            ->defaultGroup('semester')
            ->groups([
                Group::make('semester')
                    ->label('Semester')
                    ->getTitleFromRecordUsing(fn (Course $record): string => 'Semester '.$record->semester)
                    ->collapsible(),
            ])
            ->deferFilters(false)
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCourses::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
