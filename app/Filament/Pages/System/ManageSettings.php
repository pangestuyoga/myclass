<?php

namespace App\Filament\Pages\System;

use App\Filament\Support\SystemNotification;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends SettingsPage
{
    use HasPageShield;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $slug = 'system/settings';

    public static function getPagePermission(): string
    {
        return 'View:ManageSettings';
    }

    protected static ?int $navigationSort = 10;

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('current_semester')
                            ->label('Semester Sekarang')
                            ->placeholder('Pilih Semester')
                            ->options([
                                1 => 'Semester 1',
                                2 => 'Semester 2',
                                3 => 'Semester 3',
                                4 => 'Semester 4',
                                5 => 'Semester 5',
                                6 => 'Semester 6',
                                7 => 'Semester 7',
                                8 => 'Semester 8',
                            ])
                            ->native(false)
                            ->required()
                            ->helperText('Tentukan semester yang sedang aktif saat ini. Semester ini akan digunakan untuk menyaring data yang relevan.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function getSavedNotification(): ?Notification
    {
        return SystemNotification::update();
    }
}
