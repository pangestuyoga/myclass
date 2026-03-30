<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\DeleteSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\EditSessionAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\GenerateSessionsAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewAssignmentDetailAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewAssignmentsAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewAttendanceAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewMaterialDetailAction;
use App\Filament\Resources\Learning\ClassSessions\Actions\ViewMaterialsAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\Course;
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
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->count();

        return $this->course->classSessions()
            ->withCount(['attendances', 'materials', 'assignments'])
            ->orderBy('session_number', 'asc')
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

    public function form(): array
    {
        return [
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
        ];
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

            CreateAction::make()
                ->label('Tambah')
                ->modalHeading('Tambah Sesi')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->schema($this->form())
                ->mutateDataUsing(function (array $data): array {
                    $data['course_id'] = $this->courseId;

                    return $data;
                })
                ->extraModalFooterActions(fn (CreateAction $action): array => [
                    $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                        ->label('Simpan dan Tambah Lagi'),
                ])
                ->modalWidth(Width::TwoExtraLarge),
        ];
    }

    protected function getActions(): array
    {
        return [
            $this->viewAttendanceAction(),
            $this->viewMaterialsAction(),
            $this->viewAssignmentsAction(),
            $this->editSessionAction(),
            $this->deleteSessionAction(),
            $this->viewMaterialDetailAction(),
            $this->viewAssignmentDetailAction(),
        ];
    }

    public function viewAttendanceAction(): Action
    {
        return ViewAttendanceAction::make();
    }

    public function viewMaterialsAction(): Action
    {
        return ViewMaterialsAction::make();
    }

    public function viewAssignmentsAction(): Action
    {
        return ViewAssignmentsAction::make();
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
        return ViewMaterialDetailAction::make();
    }

    public function viewAssignmentDetailAction(): Action
    {
        return ViewAssignmentDetailAction::make();
    }
}
