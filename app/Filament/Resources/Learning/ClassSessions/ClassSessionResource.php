<?php

namespace App\Filament\Resources\Learning\ClassSessions;

use App\Filament\Resources\Learning\ClassSessions\Pages\ListCourseSessions;
use App\Filament\Resources\Learning\ClassSessions\Pages\ManageClassSessions;
use App\Filament\Resources\Learning\ClassSessions\RelationManagers\AttendanceRelationManager;
use App\Models\ClassSession;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Sesi Kelas';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'session_number';

    protected static ?string $slug = 'learning/class-sessions';

    public static function getRelations(): array
    {
        return [
            AttendanceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClassSessions::route('/'),
            'course' => ListCourseSessions::route('/course/{courseId}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', function (Builder $query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            });
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
