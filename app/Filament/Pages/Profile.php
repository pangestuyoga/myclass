<?php

namespace App\Filament\Pages;

use App\Enums\NotifStyle;
use App\Enums\Sex;
use App\Filament\Support\SystemNotification;
use App\Models\User;
use Carbon\Carbon;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Throwable;

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
                Section::make(SystemNotification::getByKey('labels.user_account.title'))
                    ->description(SystemNotification::getByKey('labels.user_account.description'))
                    ->icon(SystemNotification::getByKey('icons.user_account'))
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

                Section::make(SystemNotification::getByKey('labels.student_data.title'))
                    ->description(SystemNotification::getByKey('labels.student_data.description_profile'))
                    ->icon(SystemNotification::getByKey('icons.student_data'))
                    ->statePath('student')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->placeholder('John Doe')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(100)
                            ->minLength(3)
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

                Section::make(SystemNotification::getByKey('labels.account_security.title'))
                    ->description(SystemNotification::getByKey('labels.account_security.description'))
                    ->icon(SystemNotification::getByKey('icons.account_security'))
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

                Section::make(SystemNotification::getByKey('labels.appearance_settings.title'))
                    ->description(SystemNotification::getByKey('labels.appearance_settings.description'))
                    ->icon(SystemNotification::getByKey('icons.appearance_settings'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('settings.notif_style')
                                    ->label('Gaya Bahasa')
                                    ->options(NotifStyle::class)
                                    ->native(false)
                                    ->required(),

                                Select::make('settings.primary_color')
                                    ->label('Tema Warna')
                                    ->options([
                                        'blue' => '🔵 Biru (Default)',
                                        'sky' => '💎 Biru Langit',
                                        'cyan' => '🌊 Biru Tosca (Cyan)',
                                        'emerald' => '🟢 Emerald (Hijau)',
                                        'teal' => '🍃 Teal (Hijau Keunguan)',
                                        'lime' => '🍋 Lime (Hijah Muda)',
                                        'amber' => '🟡 Amber (Kuning)',
                                        'orange' => '🟠 Orange',
                                        'rose' => '🔴 Rose (Merah)',
                                        'fuchsia' => '🌸 Fuchsia (Pink Cerah)',
                                        'violet' => '🟣 Violet (Ungu)',
                                        'indigo' => '🌌 Indigo (Ungu Gelap)',
                                    ])
                                    ->placeholder('Pilih warna tema')
                                    ->native(false),

                                Select::make('settings.font')
                                    ->label('Jenis Huruf (Font)')
                                    ->options([
                                        'Inter' => 'Inter (Modern)',
                                        'Roboto' => 'Roboto (Clean)',
                                        'Poppins' => 'Poppins (Rounder)',
                                        'Outfit' => 'Outfit (Premium)',
                                        'Montserrat' => 'Montserrat (Classic)',
                                        'Lexend' => 'Lexend (Readable)',
                                    ])
                                    ->native(false),

                                Select::make('settings.content_width')
                                    ->label('Lebar Konten')
                                    ->options([
                                        'full' => '↔️ Lebar Penuh (Full)',
                                        'centered' => '🏢 Terpusat (Centered)',
                                    ])
                                    ->native(false),

                                Select::make('settings.border_radius')
                                    ->label('Radius Sudut (Border)')
                                    ->options([
                                        'none' => '📐 Tegas (Sharp)',
                                        'md' => '📱 Modern (Default)',
                                        'lg' => '🎉 Rounded (Cheerful)',
                                        'xl' => '🎈 Extra Round',
                                    ])
                                    ->native(false),

                                Toggle::make('settings.top_navigation')
                                    ->label('Navigasi Atas (Top Nav)')
                                    ->helperText('Pindahkan menu navigasi dari samping ke bagian atas layar.')
                                    ->onIcon('heroicon-m-window')
                                    ->offIcon('heroicon-m-arrow-top-right-on-square')
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form?->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_'.filament()->getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getUser();
        $student = $user->student;
        $settings = $user->settings;

        if ($student) {
            $data['student'] = $student->toArray();
        }

        if ($settings) {
            $data['settings'] = $settings->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $studentData = $data['student'] ?? null;
        $settingsData = $data['settings'] ?? null;
        unset($data['student'], $data['settings']);

        $record->update($data);

        /** @var User $record */
        if ($studentData && $record->student()->exists()) {
            $record->student?->update($studentData);
        } elseif ($studentData) {
            $record->student()->create($studentData);
        }

        if ($settingsData && $record->settings()->exists()) {
            $record->settings?->update($settingsData);
        } elseif ($settingsData) {
            $record->settings()->create($settingsData);
        }

        return $record;
    }

    protected function afterSave(): void
    {
        SystemNotification::send('profile_updated')
            ->send();

        if (filled($this->form?->getState()['password'] ?? null)) {
            filament()->auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            SystemNotification::send('account_security_updated', type: 'info')
                ->send();

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
