<?php

namespace App\Filament\Resources\Learning\Materials;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Learning\Materials\Pages\ManageMaterials;
use App\Models\Course;
use App\Models\Material;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use UnitEnum;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Materi';

    protected static ?int $navigationSort = 3;

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
                            ->live()
                            ->required()
                            ->optionsLimit(100),

                        DatePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->placeholder('Pilih Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('l, d F Y')
                            ->default(now()->toDateString()),
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
                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->date('l, d F Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->sortable(),

                ...TimestampColumns::make(),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Mata Kuliah')
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
                    ->optionsLimit(100),

                Filter::make('published_at')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal Publikasi')
                            ->placeholder('Pilih Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('published_at', $date),
                        );
                    }),

                TrashedFilter::make()
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth(Width::FourExtraLarge)
                    ->schema(function (Material $record) {
                        return $record->getMedia('materials')->map(function ($item, $index) {
                            return PdfViewerEntry::make('pdf_'.$index)
                                ->hiddenLabel()
                                ->fileUrl($item->getUrl())
                                ->columnSpanFull();
                        })->toArray();
                    })
                    ->visible(fn (Material $record) => $record->hasMedia('materials')),

                EditAction::make()
                    ->modalWidth(Width::FourExtraLarge)
                    ->fillForm(function (Material $record): array {
                        $data = $record->toArray();

                        $data['pdf'] = $record->getMedia('materials')
                            ->map(fn ($media) => $media->getPathRelativeToRoot())
                            ->toArray();

                        return $data;
                    })
                    ->using(function (Material $record, array $data): Material {
                        $pdf = $data['pdf'] ?? [];
                        unset($data['pdf']);

                        $record->update($data);

                        $existingMedia = $record->getMedia('materials');
                        $mediaMap = [];

                        foreach ($existingMedia as $media) {
                            if (in_array($media->getPathRelativeToRoot(), $pdf)) {
                                $mediaMap[$media->getPathRelativeToRoot()] = $media;
                            } else {
                                $media->delete();
                            }
                        }

                        foreach ($pdf as $path) {
                            if (! isset($mediaMap[$path])) {
                                $newMedia = $record->addMediaFromDisk($path, config('filesystems.default'))
                                    ->preservingOriginal()
                                    ->withCustomProperties([
                                        'feature' => 'materials',
                                        'date' => now()->toDateString(),
                                        'doc_type' => 'pdf',
                                    ])
                                    ->toMediaCollection('materials');
                                $mediaMap[$path] = $newMedia;
                            }
                        }

                        foreach ($pdf as $index => $path) {
                            if (isset($mediaMap[$path])) {
                                $mediaMap[$path]->order_column = $index + 1;
                                $mediaMap[$path]->save();
                            }
                        }

                        return $record;
                    }),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Materi'),
                ]),
            ])
            ->emptyStateIcon(Heroicon::OutlinedBookOpen)
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
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

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
