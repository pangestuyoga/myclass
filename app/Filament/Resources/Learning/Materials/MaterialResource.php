<?php

namespace App\Filament\Resources\Learning\Materials;

use App\Enums\RoleEnum;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\RowIndexColumn;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Learning\Materials\Actions\EditMaterialAction;
use App\Filament\Resources\Learning\Materials\Actions\ViewMaterialAction;
use App\Filament\Resources\Learning\Materials\Pages\ManageMaterials;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Material;
use App\Settings\GeneralSettings;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Materi';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $slug = 'learning/materials';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                            ->options(function ($get, ?Material $record) {
                                $courseId = $get('course_id');
                                if (! $courseId) {
                                    return [];
                                }

                                return ClassSession::where('course_id', $courseId)
                                    ->where(function ($query) use ($record) {
                                        $query->doesntHave('materials');
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
                            ->helperText('Kosongkan jika materi tidak terkait dengan sesi tertentu.'),
                    ])
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->label('Judul')
                    ->placeholder('Dasar-dasar PHP')
                    ->autocomplete(false)
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Materi tentang pengenalan PHP...')
                    ->rows(3)
                    ->columnSpanFull(),

                AdvancedFileUpload::make('pdf')
                    ->label('PDF')
                    ->multiple()
                    ->reorderable()
                    ->pdfPreviewHeight(400)
                    ->pdfDisplayPage(1)
                    ->pdfToolbar(true)
                    ->pdfZoomLevel(100)
                    ->pdfFitType(PdfViewFit::FIT)
                    ->pdfNavPanes(true)
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(1024 * 3)
                    ->panelLayout('grid')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...RowIndexColumn::make(),

                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Material $record) => $record->classSession?->session_number ? "Sesi Ke-{$record->classSession->session_number}" : ''),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->date('l, d F Y')
                    ->sortable(),

                ...TimestampColumns::make(),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Mata Kuliah')
                    ->options(function () {
                        return Course::query()
                            ->where('semester', app(GeneralSettings::class)->current_semester)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal Dibuat')
                            ->placeholder('Pilih Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                        );
                    }),

                TrashedFilter::make()
                    ->native(false)
                    ->visible(fn () => auth()->user()->hasRole(RoleEnum::Developer)),
            ])
            ->recordActions([
                ViewMaterialAction::make(),

                EditMaterialAction::make(),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Materi'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateDescription('Belum ada materi pembelajaran yang terdaftar dalam sistem saat ini.')
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
