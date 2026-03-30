<?php

namespace App\Filament\Resources\Learning\Attendances;

use App\Filament\Resources\Learning\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'Presensi';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'date';

    protected static ?string $slug = 'learning/attendances';

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendances::route('/'),
        ];
    }
}
