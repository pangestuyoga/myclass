<?php

namespace App\Filament\Resources\Learning\Assignments;

use App\Filament\Resources\Learning\Assignments\Pages\CreateAssignment;
use App\Filament\Resources\Learning\Assignments\Pages\EditAssignment;
use App\Filament\Resources\Learning\Assignments\Pages\ListAssignments;
use App\Filament\Resources\Learning\Assignments\Pages\SubmissionDetailPage;
use App\Filament\Resources\Learning\Assignments\Pages\SubmitAssignmentPage;
use App\Filament\Resources\Learning\Assignments\Schemas\AssignmentForm;
use App\Models\Assignment;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user?->student) {
            return null;
        }

        $student = $user->student;
        $now = now();

        $count = static::getEloquentQuery()
            ->where('due_date', '>', $now)
            ->where(function (Builder $query) use ($student) {
                $query->whereHas('students', fn ($q) => $q->whereKey($student->id))
                    ->orWhereHas('studyGroups', fn ($q) => $q->where('leader_id', $student->id)->orWhereHas('students', fn ($sq) => $sq->whereKey($student->id)));
            })
            ->whereDoesntHave('assignmentSubmissions', function (Builder $query) use ($student) {
                $query->where('student_id', $student->id)
                    ->orWhereHas('studyGroup', fn ($sq) => $sq->where('leader_id', $student->id)->orWhereHas('students', fn ($ssq) => $ssq->whereKey($student->id)));
            })
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    protected static string|UnitEnum|null $navigationGroup = 'Pembelajaran';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Tugas';

    protected static ?int $navigationSort = 15;

    protected static ?string $slug = 'learning/assignments';

    public static function form(Schema $schema): Schema
    {
        return AssignmentForm::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', function (Builder $query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssignments::route('/'),
            'create' => CreateAssignment::route('/create'),
            'edit' => EditAssignment::route('/{record}/edit'),
            'submission-detail' => SubmissionDetailPage::route('/{record}/submission-detail'),
            'submit' => SubmitAssignmentPage::route('/{record}/submit'),
        ];
    }
}
