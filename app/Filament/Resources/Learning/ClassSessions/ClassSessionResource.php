<?php

namespace App\Filament\Resources\Learning\ClassSessions;

use App\Filament\Resources\Learning\ClassSessions\Pages\ListCourseSessions;
use App\Filament\Resources\Learning\ClassSessions\Pages\ManageClassSessions;
use App\Models\ClassSession;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Sesi Kelas';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'learning/class-sessions';

    public static function getPages(): array
    {
        return [
            'index' => ManageClassSessions::route('/'),
            'course' => ListCourseSessions::route('/course/{course}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', function (Builder $query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            });
    }
}
