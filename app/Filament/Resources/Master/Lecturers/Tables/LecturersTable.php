<?php

namespace App\Filament\Resources\Master\Lecturers\Tables;

use App\Enums\Sex;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\TimestampColumns;
use App\Models\Lecturer;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LecturersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('Akun')
                    ->formatStateUsing(fn ($state, Lecturer $record) => "
                        <div class='flex flex-col gap-1'>
                            <div><span class='font-bold text-gray-500 text-xs uppercase'>Alamat Surel:</span> <span class='font-medium'>{$state}</span></div>
                            <div><span class='font-bold text-gray-500 text-xs uppercase'>Nama Pengguna:</span> <span class='font-medium'>{$record->user?->username}</span></div>
                        </div>
                    ")
                    ->html()
                    ->searchable(['email', 'username'])
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('lecturer_identification_number', 'like', "%{$search}%");
                    })
                    ->sortable()
                    ->description(fn (Lecturer $record) => $record->lecturer_identification_number),

                TextColumn::make('phone_number')
                    ->label('No. Telepon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sex')
                    ->label('Jenis Kelamin')
                    ->badge()
                    ->sortable()
                    ->searchable(query: function ($query, string $search) {
                        $search = strtolower($search);

                        if (str_contains('laki-laki', $search)) {
                            $query->orWhere('sex', 'male');
                        }

                        if (str_contains('perempuan', $search)) {
                            $query->orWhere('sex', 'female');
                        }
                    }),

                TextColumn::make('date_of_birth')
                    ->label('TTL')
                    ->date('d F Y')
                    ->sortable()
                    ->prefix(fn (Lecturer $record) => $record->place_of_birth ? $record->place_of_birth.', ' : ''),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),

                TextColumn::make('courses.name')
                    ->label('Mata Kuliah')
                    ->badge()
                    ->searchable()
                    ->listWithLineBreaks(),

                ...TimestampColumns::make(),
            ])
            ->filters([
                SelectFilter::make('sex')
                    ->label('Jenis Kelamin')
                    ->options(Sex::class)
                    ->native(false),

                SelectFilter::make('courses')
                    ->label('Mata Kuliah')
                    ->relationship('courses', 'name')
                    ->multiple()
                    ->preload(),

                TrashedFilter::make()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::Large),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Dosen'),
                ]),
            ])
            ->emptyStateIcon(Heroicon::OutlinedUserGroup)
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }
}
