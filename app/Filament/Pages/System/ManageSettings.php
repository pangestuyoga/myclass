<?php

namespace App\Filament\Pages\System;

use App\Enums\RoleEnum;
use App\Filament\Support\SystemNotification;
use App\Models\Student;
use App\Models\User;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSettings extends SettingsPage
{
    use HasPageShield;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $slug = 'system/settings';

    public static function getPagePermission(): string
    {
        return 'View:ManageSettings';
    }

    protected static ?int $navigationSort = 5;

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

                        Select::make('kosma_id')
                            ->label('Kosma')
                            ->placeholder('Pilih Mahasiswa sebagai Kosma')
                            ->options(Student::query()->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Pilih mahasiswa yang bertanggung jawab sebagai Kosma (Ketua Kelas).'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function getSavedNotification(): ?Notification
    {
        return SystemNotification::update();
    }

    protected function afterSave(): void
    {
        $settings = app(GeneralSettings::class);
        $kosmaId = $settings->kosma_id;

        User::role('Kosma')->get()->each(fn (User $user) => $user->removeRole('Kosma'));

        if ($kosmaId) {
            $student = Student::find($kosmaId);

            if ($student?->user) {
                $student->user->assignRole(RoleEnum::Kosma);
            }
        }
    }
}
