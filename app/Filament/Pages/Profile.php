<?php

namespace App\Filament\Pages;

use App\Filament\Support\SystemNotification;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends EditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->schema([
                        TextInput::make('email')
                            ->label('Alamat Surel')
                            ->placeholder('johndoe@puskesmas.com')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(254)
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->live(),

                        TextInput::make('username')
                            ->label('Nama Pengguna')
                            ->placeholder('johndoe')
                            ->autocomplete(false)
                            ->required()
                            ->minLength(5)
                            ->maxLength(20)
                            ->regex('/^[a-zA-Z0-9_.-]+$/')
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Kata Sandi')
                    ->schema([
                        TextInput::make('password')
                            ->label(__('filament-panels::auth/pages/register.form.password.label'))
                            ->placeholder('********')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required(fn (Get $get) => filled($get('passwoed')) || filled($get('passwordConfirmation')))
                            ->rule(Password::default())
                            ->showAllValidationMessages()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->same('passwordConfirmation')
                            ->validationAttribute(__('filament-panels::auth/pages/register.form.password.validation_attribute')),

                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::auth/pages/register.form.password_confirmation.label'))
                            ->placeholder('********')
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required(fn (Get $get) => filled($get('passwoed')) || filled($get('passwordConfirmation')))
                            ->dehydrated(false),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $shouldLogout = false;
        if (isset($data['new_password'])) {
            $data['password'] = $data['new_password'];
            unset($data['new_password']);
            $shouldLogout = true;
        }

        $record->update($data);

        SystemNotification::success(
            'Profil Berhasil Diperbarui ✅',
            'Data profil Anda telah berhasil disimpan dan diperbarui di dalam sistem.'
        )->send();

        if ($shouldLogout) {
            auth()->logout();
        }

        return $record;
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
