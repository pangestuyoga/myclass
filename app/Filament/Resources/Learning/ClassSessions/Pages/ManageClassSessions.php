<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\Cheerful\DeleteAction;
use App\Filament\Actions\Cheerful\EditAction;
use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Settings\GeneralSettings;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;

class ManageClassSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public static string $resource = ClassSessionResource::class;

    protected static ?string $title = 'Sesi Kelas';

    protected string $view = 'filament.pages.learning.class-session.index';

    public ?string $search = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('search')
                    ->label('Cari')
                    ->placeholder('Cari Mata Kuliah atau Dosen...')
                    ->autocomplete(false)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($state) => $this->search = $state),
            ])
            ->columns([
                'sm' => 1,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function editSessionAction(): Action
    {
        return EditAction::make('editSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']))
            ->modalWidth(Width::TwoExtraLarge);
    }

    public function deleteSessionAction(): Action
    {
        return DeleteAction::make('deleteSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']));
    }

    public function generateTodaySessionAction(): Action
    {
        return Action::make('generateTodaySession')
            ->label('Generate Sesi')
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Generate Sesi Hari Ini?')
            ->modalDescription('Sistem akan membuat sesi baru secara otomatis berdasarkan jadwal aktif.')
            ->modalSubmitActionLabel('Generate Sekarang')
            ->action(function (array $arguments) {
                $courseId = $arguments['course'];
                $schedule = CourseSchedule::where('course_id', $courseId)->first();

                if (! $schedule) {
                    \Filament\Notifications\Notification::make()
                        ->title('Jadwal Tidak Ditemukan')
                        ->danger()
                        ->send();

                    return;
                }

                $lastSession = ClassSession::where('course_id', $courseId)->max('session_number');

                ClassSession::create([
                    'course_id' => $courseId,
                    'session_number' => ($lastSession ?? 0) + 1,
                    'date' => now()->toDateString(),
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ]);

                \Filament\Notifications\Notification::make()
                    ->title('Sesi Berhasil Digenerate')
                    ->success()
                    ->send();
            });
    }

    public function getTodaySessions(): Collection
    {
        $dayOfWeek = now()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)

        $schedules = CourseSchedule::query()
            ->where('day_of_week', $dayOfWeek)
            ->whereHas('course', function ($query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            })
            ->with(['course.lecturer', 'course.classSessions' => fn ($q) => $q->whereDate('date', now())])
            ->orderBy('start_time', 'asc')
            ->get();

        return $schedules->map(function ($schedule) {
            $session = $schedule->course->classSessions->first();

            return (object) [
                'id' => $session?->id,
                'course' => $schedule->course,
                'session_number' => $session?->session_number ?? (ClassSession::where('course_id', $schedule->course_id)->max('session_number') ?? 0) + 1,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'title' => $session?->title,
                'is_pending' => ! $session,
            ];
        });
    }

    public function getCourses(): Collection
    {
        $query = Course::query()
            ->where('semester', app(GeneralSettings::class)->current_semester)
            ->with(['classSessions' => fn ($q) => $q->orderBy('session_number', 'asc'), 'lecturer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
                    ->orWhereHas('lecturer', fn ($sq) => $sq->where('full_name', 'like', "%{$this->search}%"));
            });
        }

        return $query->get()
            ->sortBy('name');
    }
}
