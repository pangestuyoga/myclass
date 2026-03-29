<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Settings\GeneralSettings;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ManageClassSessions extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public static string $resource = ClassSessionResource::class;

    protected static ?string $title = 'Sesi Kelas';

    protected string $view = 'filament.resources.learning.class-sessions.index';

    public ?string $search = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    #[Computed]
    public function todayDate(): string
    {
        return now()->translatedFormat('l, d F Y');
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

    #[Computed]
    public function todaySessions(): Collection
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
            $isPending = ! $session;
            $sessionNumber = $session?->session_number ?? (ClassSession::where('course_id', $schedule->course_id)->max('session_number') ?? 0) + 1;

            return (object) [
                'id' => $session?->id,
                'is_pending' => $isPending,
                'session_number' => $sessionNumber,
                'course_name' => $schedule->course->name,
                'course_code' => $schedule->course->code,
                'lecturer' => $schedule->course->lecturer ?? '-',
                'time_range' => $schedule->start_time->format('H:i').' - '.$schedule->end_time->format('H:i'),
                'attendances_count' => $session?->attendances_count ?? 0,
                'url' => $isPending ? null : ClassSessionResource::getUrl('course', ['courseId' => $schedule->course->id]),

                // Pre-calculated classes
                'card_classes' => Arr::toCssClasses([
                    'group flex w-full rounded-xl border overflow-hidden relative transition-all hover:shadow-md',
                    'border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm' => ! $isPending,
                    'border-gray-300 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/10 border-dashed' => $isPending,
                ]),
                'session_badge_classes' => Arr::toCssClasses([
                    'flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg font-bold border',
                    'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700' => ! $isPending,
                    'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700' => $isPending,
                ]),
                'title_classes' => Arr::toCssClasses([
                    'font-bold transition-colors',
                    'text-gray-900 dark:text-white group-hover:text-primary-600' => ! $isPending,
                    'text-gray-500 dark:text-gray-400' => $isPending,
                ]),
                'status_badge_classes' => Arr::toCssClasses([
                    'flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold shadow-sm',
                    'bg-primary-500 text-white animate-pulse' => ! $isPending,
                    'bg-gray-500 text-white opacity-60' => $isPending,
                ]),
                'attendance_section_classes' => Arr::toCssClasses([
                    'flex flex-col items-center justify-center border-l p-2 bg-white/30 dark:bg-black/10 transition-colors',
                    'border-primary-200 dark:border-primary-800 group-hover:bg-primary-500/5' => ! $isPending,
                    'border-gray-200 dark:border-gray-800' => $isPending,
                ]),
            ];
        });
    }

    #[Computed]
    public function courses(): Collection
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
            ->sortBy('name')
            ->map(fn ($course) => (object) [
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code,
                'lecturer' => $course->lecturer ?? 'Dosen Belum Ditentukan',
                'sessions_count' => $course->classSessions->count(),
                'url' => ClassSessionResource::getUrl('course', ['courseId' => $course->id]),
            ]);
    }
}
