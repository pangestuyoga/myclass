<?php

namespace App\Filament\Resources\Learning\Materials\Tables;

use App\Enums\RoleEnum;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\RowIndexColumn;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Learning\Materials\Actions\EditMaterialAction;
use App\Filament\Resources\Learning\Materials\Actions\ViewMaterialAction;
use App\Models\Course;
use App\Models\Material;
use App\Settings\GeneralSettings;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MaterialTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ...RowIndexColumn::make(),

                TextColumn::make('course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Material $record) => $record->classSession?->session_number ? "Sesi Ke-{$record->classSession?->session_number}" : ''),

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
                    ->visible(fn () => Auth::user()?->hasRole(RoleEnum::Developer)),
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
}
