<?php

namespace App\Filament\Resources\Master\Students\Tables;

use App\Enums\IsActive;
use App\Enums\RoleEnum;
use App\Enums\Sex;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\ForceDeleteAction;
use App\Filament\Actions\Cheerful\RestoreAction;
use App\Filament\Actions\DefaultBulkActions;
use App\Filament\Columns\RowIndexColumn;
use App\Filament\Columns\TimestampColumns;
use App\Filament\Resources\Master\Students\Actions\ChangePasswordAction;
use App\Filament\Resources\Master\Students\Actions\EditStudentAction;
use App\Filament\Support\SystemNotification;
use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ...RowIndexColumn::make(),

                TextColumn::make('user.email')
                    ->label('Akun')
                    ->formatStateUsing(fn ($state, Student $record) => "
                        <div class='flex flex-col gap-1'>
                            <div><span class='font-bold text-gray-500 text-xs uppercase'>Alamat Surel:</span> <span class='font-medium'>{$state}</span></div>
                            <div><span class='font-bold text-gray-500 text-xs uppercase'>Nama Pengguna:</span> <span class='font-medium'>{$record->user?->username}</span></div>
                        </div>
                    ")
                    ->html()
                    ->searchable(['email', 'username'])
                    ->sortable()
                    ->visible(fn () => auth()->user()->hasRole(RoleEnum::Developer)),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('full_name', 'like', "%{$search}%")
                            ->orWhere('student_number', 'like', "%{$search}%");
                    })
                    ->sortable()
                    ->description(fn (Student $student) => $student->student_number),

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
                    ->prefix(fn (Student $student) => $student->place_of_birth ? $student->place_of_birth.', ' : ''),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),

                ToggleColumn::make('user.is_active')
                    ->label('Status')
                    ->sortable()
                    ->getStateUsing(fn (Student $record): bool => $record->user?->is_active === IsActive::Active)
                    ->updateStateUsing(function (Student $record, bool $state): void {
                        $user = $record->user;
                        if (! $user) {
                            return;
                        }

                        $user->is_active = $state ? IsActive::Active : IsActive::Inactive;
                        $user->save();

                        if ($user->is_active === IsActive::Inactive) {
                            DB::table('sessions')
                                ->where('user_id', $user->id)
                                ->delete();
                        }

                        SystemNotification::success(
                            $state ? 'Pengguna Diaktifkan! ✅' : 'Pengguna Dinonaktifkan ⛔',
                            $state ? 'Status akun pengguna berhasil diaktifkan kembali. Siap beraksi! 🚀' : 'Status akun pengguna telah berhasil dinonaktifkan. Istirahat dulu ya... 😴'
                        )->send();
                    })
                    ->visible(fn () => auth()->user()->hasRole([RoleEnum::Kosma, RoleEnum::Developer])),

                ...TimestampColumns::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(IsActive::class)
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($q) => $q->whereHas('user', fn ($uq) => $uq->where('is_active', $data['value']))))
                    ->native(false),

                SelectFilter::make('sex')
                    ->label('Jenis Kelamin')
                    ->options(Sex::class)
                    ->native(false),

                TrashedFilter::make()
                    ->native(false)
                    ->visible(fn () => auth()->user()->hasRole(RoleEnum::Developer)),
            ])
            ->recordActions([
                ChangePasswordAction::make('changePassword'),

                EditStudentAction::make(),

                DeleteAction::make(),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...DefaultBulkActions::make('Mahasiswa'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-user-plus')
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }
}
