<?php

namespace App\Filament\Resources\Master\Courses\Tables;

use App\Enums\RoleEnum;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\RowIndexColumn;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Master\Courses\Actions\EditCourseAction;
use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CoursesTable
{
    public static function configure(Table $table): Table
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
}
