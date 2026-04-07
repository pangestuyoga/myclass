<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\DeleteSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\EditSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\GenerateSessionsAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\MarkAsSentAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ShareAttendanceAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewAssignmentsAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewAttendanceAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewMaterialsAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Filament\Resources\Learning\ClassSessions\Schemas\SessionForm;
use App\Filament\Support\SystemNotification;
use App\Models\Course;
use App\Models\Student;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ListCourseSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected static string $resource = ClassSessionResource::class;

    protected string $view = 'filament.resources.learning.class-sessions.show';

    public Course $course;

    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    #[Computed]
    public function sessions(): Collection
    {
        $totalActiveStudents = Student::query()
            ->whereHas('user', fn ($q) => $q->active())
            ->count();

        return $this->course?->classSessions()
            ->withCount(['attendances', 'materials', 'assignments'])
            ->orderByDesc('session_number')
            ->get()
            ->map(fn ($session) => (object) [
                'id' => $session->id,
                'session_number' => $session->session_number,
                'date_formatted' => $session->date?->translatedFormat('l, d F Y'),
                'time_range' => $session->start_time?->format('H:i').' - '.$session->end_time?->format('H:i'),
                'attendances_count' => $session->attendances_count,
                'total_students' => $totalActiveStudents,
                'attendance_percentage' => $totalActiveStudents > 0 ? round(($session->attendances_count / $totalActiveStudents) * 100) : 0,
                'materials_count' => $session->materials_count,
                'assignments_count' => $session->assignments_count,
                'is_sent_to_lecturer' => $session->is_sent_to_lecturer,
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return SessionForm::configure($schema);
    }

    #[Computed]
    public function description(): string
    {
        return 'Data sesi pembelajaran untuk mata kuliah '.$this->course?->name;
    }

    public function getTitle(): string
    {
        return 'Sesi Kelas - '.$this->course?->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ManageClassSessions::getUrl()),

            GenerateSessionsAction::make(),
        ];
    }

    public function viewAttendanceAction(): ViewAttendanceAction
    {
        return ViewAttendanceAction::make();
    }

    public function viewMaterialsAction(): ViewMaterialsAction
    {
        return ViewMaterialsAction::make();
    }

    public function viewAssignmentsAction(): ViewAssignmentsAction
    {
        return ViewAssignmentsAction::make();
    }

    public function shareAttendanceAction(): ShareAttendanceAction
    {
        return ShareAttendanceAction::make();
    }

    public function editSessionAction(): EditSessionAction
    {
        return EditSessionAction::make();
    }

    public function markAsSentAction(): MarkAsSentAction
    {
        return MarkAsSentAction::make();
    }

    public function deleteSessionAction(): DeleteSessionAction
    {
        return DeleteSessionAction::make();
    }

    #[Computed]
    public function emptyStateHeading(): string
    {
        return SystemNotification::getByKey('labels.empty_course_sessions.title');
    }

    #[Computed]
    public function emptyStateDescription(): string
    {
        return SystemNotification::getByKey('labels.empty_course_sessions.description');
    }

    #[Computed]
    public function emptyStateIcon(): string
    {
        return SystemNotification::getByKey('icons.empty_course_sessions');
    }
}
