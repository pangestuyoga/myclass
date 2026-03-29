<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Actions\Cheerful\DeleteAction;
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

    public function deleteSessionAction(): Action
    {
        return DeleteAction::make('deleteSession')
            ->record(fn (array $arguments) => ClassSession::find($arguments['session']));
    }

    public function getTodaySessions(): Collection
    {
        $dayOfWeek = now()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)

        $schedules = CourseSchedule::query()
            ->where('day_of_week', $dayOfWeek)
            ->whereHas('course', function ($query) {
                $query->where('semester', app(GeneralSettings::class)->current_semester);
            })
            ->with([
                'course',
                'course.classSessions' => fn ($q) => $q->whereDate('date', now())->withCount('attendances'),
            ])
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
                'attendances_count' => $session?->attendances_count ?? 0,
            ];
        });
    }

    public function getCourses(): Collection
    {
        $query = Course::query()
            ->where('semester', app(GeneralSettings::class)->current_semester)
            ->with(['classSessions' => fn ($q) => $q->orderBy('session_number', 'asc')]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%")
                    ->orWhere('lecturer', 'like', "%{$this->search}%");
            });
        }

        return $query->get()
            ->sortBy('name');
    }
}
