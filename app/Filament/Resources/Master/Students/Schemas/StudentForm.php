<?php

namespace App\Filament\Resources\Master\Students\Schemas;

use App\Enums\Sex;
use App\Filament\Support\SystemNotification;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(SystemNotification::getByKey('labels.account_credentials.title'))
                    ->description(new HtmlString(SystemNotification::getByKey('labels.account_credentials.description')))
                    ->icon(SystemNotification::getByKey('icons.account_credentials'))
                    ->schema([
                        TextInput::make('email')
                            ->label('Alamat Surel')
                            ->placeholder('johndoe@example.com')
                            ->autocomplete(false)
                            ->required()
                            ->maxLength(254)
                            ->email()
                            ->unique('users', 'email', ignoreRecord: true),

                        TextInput::make('username')
                            ->label('Nama Pengguna')
                            ->placeholder('johndoe')
                            ->autocomplete(false)
                            ->required()
                            ->minLength(5)
                            ->maxLength(20)
                            ->regex('/^[a-zA-Z0-9_.-]+$/')
                            ->unique('users', 'username', ignoreRecord: true),
                    ])
                    ->columns(2)
                    ->hiddenOn('edit'),

                Section::make(SystemNotification::getByKey('labels.student_data.title'))
                    ->description(SystemNotification::getByKey('labels.student_data.description'))
                    ->icon(SystemNotification::getByKey('icons.student_data'))
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
                                    ->required()
                                    ->numeric()
                                    ->rules(['digits:10'])
                                    ->unique(ignoreRecord: true),

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
                    ->columns(2),
            ])
            ->columns(1);
    }
}
