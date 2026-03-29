<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Actions\Cheerful\CreateAction;
use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
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

    public function formSchema(): array
    {
        return [
            Grid::make(['default' => 3])
                ->schema([
                    TextInput::make('session_number')
                        ->label('Pertemuan Ke-')
                        ->numeric()
                        ->default(fn () => ($this->course->classSessions()->max('session_number') ?? 0) + 1)
                        ->required(),
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->default(now())
                        ->native(false)
                        ->required(),
                    Grid::make(['default' => 2])
                        ->schema([
                            TimePicker::make('start_time')
                                ->label('Mulai')
                                ->native(false)
                                ->seconds(false)
                                ->required(),
                            TimePicker::make('end_time')
                                ->label('Selesai')
                                ->native(false)
                                ->seconds(false)
                                ->required(),
                        ])->columnSpan(1),
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

            $this->generateSessionsAction(),
            CreateAction::make()
                ->label('Tambah Sesi')
                ->modalHeading('Tambah Sesi')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->form($this->formSchema())
                ->mutateFormDataUsing(function (array $data): array {
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

    public function generateSessionsAction(): Action
    {
        return Action::make('generateSessions')
            ->label('Generate Sesi')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Generate Sesi Pembelajaran')
            ->modalDescription('Sistem akan men-generate atau memperbarui sesi 1 sampai 16 secara otomatis berdasarkan jadwal mata kuliah ini.')
            ->form([
                DatePicker::make('start_date')
                    ->label('Tanggal Pertemuan Ke-1')
                    ->default(now())
                    ->required()
                    ->native(false),
            ])
            ->action(function (array $data) {
                $course = $this->course;
                $schedule = $course->courseSchedules()->first();

                if (! $schedule) {
                    SystemNotification::danger('Batal!', 'Jadwal belum ditentukan untuk mata kuliah ini.')->send();

                    return;
                }

                $startDate = \Carbon\Carbon::parse($data['start_date']);

                for ($i = 1; $i <= 16; $i++) {
                    ClassSession::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'session_number' => $i,
                        ],
                        [
                            'date' => $startDate->copy()->addWeeks($i - 1)->toDateString(),
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                        ]
                    );
                }

                SystemNotification::success('Selesai!', '16 Sesi berhasil digenerate/diperbarui.')->send();
            });
    }

    public function viewAttendanceAction(): Action
    {
        return Action::make('viewAttendance')
            ->label('Lihat Presensi')
            ->modalHeading(fn (array $arguments) => 'Daftar Presensi - Sesi Ke-'.ClassSession::find($arguments['session'])->session_number)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::ExtraLarge)
            ->modalContent(fn (array $arguments) => view('filament.resources.learning.class-sessions.attendance-modal', [
                'attendances' => ClassSession::find($arguments['session'])->attendances()->with('student')->latest('attended_at')->get(),
            ]));
    }

    public function editSessionAction(): Action
    {
        return EditAction::make('editSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']))
            ->form($this->formSchema())
            ->modalWidth(Width::TwoExtraLarge);
    }

    public function deleteSessionAction(): Action
    {
        return DeleteAction::make('deleteSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']));
    }
}
