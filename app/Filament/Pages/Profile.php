<?php

namespace App\Filament\Pages;

use App\Enums\Sex;
use App\Filament\Support\SystemNotification;
use Carbon\Carbon;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends EditProfile
{
    public function getMaxWidth(): Width
    {
        return Width::FourExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->description('Kelola informasi detail akun Anda agar selalu terkini.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Alamat Surel')
                                    ->placeholder('contoh@email.com')
                                    ->autocomplete(false)
                                    ->required()
                                    ->maxLength(254)
                                    ->unique(ignoreRecord: true)
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->live(),

                                TextInput::make('username')
                                    ->label('Nama Pengguna')
                                    ->placeholder('nama_pengguna')
                                    ->autocomplete(false)
                                    ->required()
                                    ->minLength(5)
                                    ->maxLength(20)
                                    ->regex('/^[a-zA-Z0-9_.-]+$/')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-o-at-symbol'),
                            ]),
                    ]),

                Section::make('Informasi Mahasiswa')
                    ->description('Pastikan data akademik Anda sesuai dengan record sistem.')
                    ->icon('heroicon-o-academic-cap')
                    ->statePath('student')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->placeholder('John Doe')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
                            ->autofocus()
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('student_number')
                                    ->label('NIM')
                                    ->placeholder('1234567890')
                                    ->autocomplete(false)
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('phone_number')
                                    ->label('Nomor Telepon')
                                    ->placeholder('0812 3456 78910')
                                    ->autocomplete(false)
                                    ->required()
                                    ->maxLength(20)
                                    ->minLength(10)
                                    ->rules(['regex:/^[0-9 ]+$/'])
                                    ->mask('9999 9999 99999'),

                                Radio::make('sex')
                                    ->label('Jenis Kelamin')
                                    ->options(Sex::class)
                                    ->required()
                                    ->inline(),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Jl. Contoh No. 123, Kota, Provinsi')
                            ->autocomplete(false)
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),

                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->placeholder('Pilih Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->maxDate(Carbon::now()->subYears(15)),

                        TextInput::make('place_of_birth')
                            ->label('Tempat Lahir')
                            ->placeholder('Purwakarta')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(50)
                            ->minLength(3),
                    ])
                    ->columns(2)
                    ->visible(fn () => auth()->user()?->student()->exists()),

                Section::make('Keamanan Akun')
                    ->description('Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label(__('filament-panels::auth/pages/register.form.password.label'))
                                    ->placeholder('********')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->rule(Password::default())
                                    ->showAllValidationMessages()
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->same('passwordConfirmation')
                                    ->validationAttribute(__('filament-panels::auth/pages/register.form.password.validation_attribute'))
                                    ->prefixIcon('heroicon-o-lock-closed'),

                                TextInput::make('passwordConfirmation')
                                    ->label(__('filament-panels::auth/pages/register.form.password_confirmation.label'))
                                    ->placeholder('********')
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->dehydrated(false)
                                    ->prefixIcon('heroicon-o-lock-closed'),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getUser()->student;

        if ($student) {
            $data['student'] = $student->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $studentData = $data['student'] ?? null;
        unset($data['student']);

        $record->update($data);

        if ($studentData && $record->student) {
            $record->student->update($studentData);
        } elseif ($studentData) {
            $record->student()->create($studentData);
        }

        return $record;
    }

    protected function afterSave(): void
    {
        SystemNotification::success(
            'Profil Berhasil Diperbarui ✅',
            'Data profil Anda telah berhasil disimpan dan diperbarui di dalam sistem.'
        )->send();

        if (filled($this->form->getState()['password'] ?? null)) {
            filament()->auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            SystemNotification::info(
                'Keamanan Akun Diperbarui 🔐',
                'Anda telah mengubah kata sandi. Untuk alasan keamanan, silakan masuk kembali dengan kata sandi baru Anda.'
            )->send();

            $this->redirect(filament()->getLoginUrl());
        } else {
            $this->redirect($this->getUrl());
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }
}
