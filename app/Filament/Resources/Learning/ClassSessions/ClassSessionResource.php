<?php

namespace App\Filament\Resources\Learning\ClassSessions;

use App\Enums\RoleEnum;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Learning\ClassSessions\Pages\ListCourseSessions;
use App\Filament\Resources\Learning\ClassSessions\Pages\ManageClassSessions;
use App\Filament\Resources\Learning\ClassSessions\RelationManagers\AttendanceRelationManager;
use App\Models\ClassSession;
use App\Models\Course;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static ?string $navigationLabel = 'Sesi Kelas';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Sesi Kelas';

    protected static ?string $pluralModelLabel = 'Sesi Kelas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('course_id')
                            ->label('Mata Kuliah')
                            ->placeholder('Pilih Mata Kuliah')
                            ->options(function () {
                                return Course::query()
                                    ->where('semester', app(GeneralSettings::class)->current_semester)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
                                    return;
                                }

                                $schedule = \App\Models\CourseSchedule::where('course_id', $state)->first();

                                if ($schedule) {
                                    $set('start_time', $schedule->start_time?->format('H:i'));
                                    $set('end_time', $schedule->end_time?->format('H:i'));
                                }

                                // Ambil pertemuan terakhir + 1
                                $lastSession = \App\Models\ClassSession::where('course_id', $state)->max('session_number');
                                $set('session_number', ($lastSession ?? 0) + 1);
                            })
                            ->columnSpanFull(),

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
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session_number')
                    ->label('Pertemuan Ke-')
                    ->sortable()
                    ->badge(),

                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (ClassSession $record) => $record->course->code),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('l, d F Y')
                    ->sortable()
                    ->description(fn (ClassSession $record) => $record->start_time->format('H:i').' - '.$record->end_time->format('H:i').' WIB'),

                TextColumn::make('attendances_count')
                    ->counts('attendances')
                    ->label('Presensi')
                    ->badge()
                    ->color('success'),

                TextColumn::make('materials_count')
                    ->counts('materials')
                    ->label('Materi')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('assignments_count')
                    ->counts('assignments')
                    ->label('Tugas')
                    ->badge()
                    ->color('info'),

                ...TimestampColumns::make(),
            ])
            ->filters([
                TrashedFilter::make()
                    ->native(false)
                    ->visible(fn () => auth()->user()->hasRole(RoleEnum::Developer)),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::TwoExtraLarge),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Sesi Kelas'),
                ]),
            ])
            ->emptyStateIcon(Heroicon::OutlinedPresentationChartBar)
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }

    public static function getRelations(): array
    {
        return [
            AttendanceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClassSessions::route('/'),
            'course' => ListCourseSessions::route('/course/{courseId}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', function (Builder $query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            });
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
