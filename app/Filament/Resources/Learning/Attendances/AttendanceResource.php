<?php

namespace App\Filament\Resources\Learning\Attendances;

use App\Filament\Resources\Learning\Attendances\Pages\AttendancePage;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $navigationLabel = 'Presensi';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'Presensi';

    protected static ?string $pluralModelLabel = 'Presensi';

    public static function getPages(): array
    {
        return [
            'index' => AttendancePage::route('/'),
        ];
    }
}
