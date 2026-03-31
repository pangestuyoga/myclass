<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\DeleteSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\EditSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\GenerateSessionsAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ShareAssignmentAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ShareAttendanceAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Material;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ListCourseSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected static string $resource = ClassSessionResource::class;

    protected string $view = 'filament.resources.learning.class-sessions.show';

    public $courseId;

    public function mount($courseId): void
    {
        $this->courseId = $courseId;
    }

    #[Computed]
    public function course(): Course
    {
        return Course::findOrFail($this->courseId);
    }

    #[Computed]
    public function sessions(): Collection
    {
        $totalActiveStudents = Student::query()
            ->whereHas('user', fn ($q) => $q->active())
            ->count();

        return $this->course->classSessions()
            ->withCount(['attendances', 'materials', 'assignments'])
            ->orderByDesc('session_number')
            ->get()
            ->map(fn ($session) => (object) [
                'id' => $session->id,
                'session_number' => $session->session_number,
                'date_formatted' => $session->date->translatedFormat('l, d F Y'),
                'time_range' => $session->start_time->format('H:i').' - '.$session->end_time->format('H:i'),
                'attendances_count' => $session->attendances_count,
                'total_students' => $totalActiveStudents,
                'attendance_percentage' => $totalActiveStudents > 0 ? round(($session->attendances_count / $totalActiveStudents) * 100) : 0,
                'materials_count' => $session->materials_count,
                'assignments_count' => $session->assignments_count,
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(['default' => 2])
                    ->schema([
                        TextInput::make('session_number')
                            ->label('Pertemuan Ke-')
                            ->placeholder('1')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(16)
                            ->autocomplete(false),

                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->placeholder('Pilih Tanggal')
                            ->required()
                            ->native(false)
                            ->displayFormat('l, d F Y')
                            ->default(now()->toDateString()),

                        TimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->placeholder('08:00')
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('Waktu Selesai')
                            ->placeholder('10:00')
                            ->native(false)
                            ->displayFormat('H:i')
                            ->seconds(false)
                            ->required(),
                    ]),
            ]);
    }

    #[Computed]
    public function description(): string
    {
        return 'Data sesi pembelajaran untuk mata kuliah '.$this->course->name;
    }

    public function getTitle(): string
    {
        return 'Sesi Kelas - '.$this->course->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ManageClassSessions::getUrl()),

            GenerateSessionsAction::make(),
        ];
    }

    protected function getActions(): array
    {
        return [
            $this->shareAttendanceAction(),
            $this->shareAssignmentAction(),
            $this->viewAttendanceAction(),
            $this->viewMaterialsAction(),
            $this->viewAssignmentsAction(),
            $this->editSessionAction(),
            $this->deleteSessionAction(),
            $this->viewMaterialDetailAction(),
        ];
    }

    public function shareAttendanceAction(): Action
    {
        return ShareAttendanceAction::make();
    }

    public function shareAssignmentAction(): Action
    {
        return ShareAssignmentAction::make();
    }

    public function viewAttendanceAction(): Action
    {
        return Action::make('viewAttendance')
            ->label('Presensi')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->link()
            ->modalHeading('Daftar Presensi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.attendance-modal', [
                'attendances' => ClassSession::find($arguments['session'] ?? null, ['*'])?->attendances()->with('student')->latest('attended_at')->get() ?? collect(),
            ]));
    }

    public function viewMaterialsAction(): Action
    {
        return Action::make('viewMaterials')
            ->label('Materi')
            ->color('purple')
            ->icon('heroicon-o-book-open')
            ->link()
            ->modalHeading('Materi Sesi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.materials-modal', [
                'materials' => ClassSession::find($arguments['session'] ?? null, ['*'])?->materials()
                    ->latest()
                    ->get()
                    ->map(fn ($m) => (object) [
                        'id' => $m->id,
                        'title' => $m->title,
                        'created_at_formatted' => $m->created_at?->translatedFormat('d F Y') ?? '-',
                    ]) ?? collect(),
            ]));
    }

    public function viewAssignmentsAction(): Action
    {
        return Action::make('viewAssignments')
            ->label('Tugas')
            ->color('sky')
            ->icon('heroicon-o-clipboard-document-list')
            ->link()
            ->modalHeading('Tugas Sesi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::TwoExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.assignments-modal', [
                'assignments' => ClassSession::find($arguments['session'] ?? null, ['*'])?->assignments()
                    ->latest()
                    ->get()
                    ->map(fn ($a) => (object) [
                        'id' => $a->id,
                        'title' => $a->title,
                        'type_label' => $a->type?->getLabel() ?? 'Tugas',
                        'due_date_formatted' => $a->due_date?->translatedFormat('d F Y, H:i') ?? '-',
                    ]) ?? collect(),
            ]));
    }

    public function editSessionAction(): Action
    {
        return EditSessionAction::make();
    }

    public function deleteSessionAction(): Action
    {
        return DeleteSessionAction::make();
    }

    public function viewMaterialDetailAction(): Action
    {
        return Action::make('viewMaterialDetail')
            ->record(fn (array $arguments) => Material::find($arguments['record'] ?? null, ['*']))
            ->modalHeading('Detail Materi')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(fn (?Material $record) => $record ? view('filament.resources.learning.class-sessions.material-detail-modal', [
                'record' => $record,
            ]) : null);
    }
}
