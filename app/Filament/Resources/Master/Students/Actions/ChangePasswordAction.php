<?php

namespace App\Filament\Resources\Master\Students\Actions;

use App\Filament\Support\SystemNotification;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Hash;

class ChangePasswordAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Ganti Kata Sandi')
            ->color('info')
            ->icon('heroicon-o-key')
            ->modalWidth(Width::Large)
            ->schema([
                TextInput::make('password')
                    ->label('Kata Sandi Baru')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->revealable(filament()->arePasswordsRevealable())
                    ->default('Minimal8@'),
            ])
            ->action(function (Student $record, array $data): void {
                $record->user?->update([
                    'password' => Hash::make($data['password']),
                ]);

                SystemNotification::success(
                    'Kata sandi berhasil diubah! 🔑',
                    "Kata sandi untuk mahasiswa {$record->full_name} telah diperbarui."
                )->send();
            });
    }
}
