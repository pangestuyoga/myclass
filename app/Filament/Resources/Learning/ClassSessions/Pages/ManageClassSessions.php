<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Filament\Support\SystemNotification;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Student;
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
        $this->form?->fill();
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
        $totalActiveStudents = Student::query()
            ->whereHas('user', fn ($q) => $q->active())
            ->count();

        return ClassSession::query()
            ->whereDate('date', now())
            ->with(['course'])
            ->withCount(['attendances', 'materials', 'assignments'])
            ->get()
            ->map(function ($session) use ($totalActiveStudents) {
                $percentage = $totalActiveStudents > 0 ? round(($session->attendances_count / $totalActiveStudents) * 100) : 0;

                return (object) [
                    'id' => $session->id,
                    'is_pending' => false,
                    'session_number' => $session->session_number,
                    'course_name' => $session->course?->name,
                    'course_code' => $session->course?->code,
                    'lecturer' => $session->course?->lecturer ?? '-',
                    'time_range' => $session->start_time?->format('H:i').' - '.$session->end_time?->format('H:i'),
                    'attendances_count' => $session->attendances_count,
                    'total_students' => $totalActiveStudents,
                    'attendance_percentage' => $percentage,
                    'materials_count' => $session->materials_count,
                    'assignments_count' => $session->assignments_count,
                    'is_sent_to_lecturer' => $session->is_sent_to_lecturer,
                    'url' => ClassSessionResource::getUrl('course', ['course' => $session->course]),

                    // Pre-calculated classes
                    'status_label' => $session->is_sent_to_lecturer ? 'Terkirim' : $session->time_range,
                    'status_icon' => $session->is_sent_to_lecturer ? 'heroicon-s-paper-airplane' : 'heroicon-s-clock',
                    // Pre-calculated classes
                    'card_classes' => 'group flex w-full rounded-xl border border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm overflow-hidden relative transition-all hover:shadow-md',
                    'session_badge_classes' => 'flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg font-bold border bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700',
                    'title_classes' => 'font-bold transition-colors text-gray-900 dark:text-white group-hover:text-primary-600',
                    'status_badge_classes' => Arr::toCssClasses([
                        'flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold shadow-sm text-white',
                        'bg-primary-500 animate-pulse' => ! $session->is_sent_to_lecturer,
                        'bg-danger-500' => $session->is_sent_to_lecturer,
                    ]),
                    'attendance_section_classes' => 'flex flex-col items-center justify-center border-l border-primary-200 dark:border-primary-800 p-2 bg-white/30 dark:bg-black/10 transition-colors group-hover:bg-primary-500/5',
                ];
            });
    }

    #[Computed]
    public function courses(): Collection
    {
        $query = Course::query()
            ->where('semester', app(GeneralSettings::class)->current_semester)
            ->with(['classSessions', 'studyGroups.students.user']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('lecturer', 'like', '%'.$this->search.'%');
            });
        }

        $totalActiveStudents = Student::query()
            ->whereHas('user', fn ($q) => $q->active())
            ->count();

        return $query->get()
            ->sortBy('name')
            ->map(function ($course) use ($totalActiveStudents) {
                return (object) [
                    'id' => $course->id,
                    'name' => $course->name,
                    'code' => $course->code,
                    'lecturer' => $course->lecturer ?? 'Dosen Belum Ditentukan',
                    'sessions_count' => $course->classSessions?->count(),
                    'total_students' => $totalActiveStudents,
                    'url' => ClassSessionResource::getUrl('course', ['course' => $course]),
                ];
            });
    }

    #[Computed]
    public function sessionsHeading(): string
    {
        return SystemNotification::getByKey('labels.today_sessions.title');
    }

    #[Computed]
    public function sessionsDescription(): string
    {
        return SystemNotification::getByKey('labels.today_sessions.description', ['date' => $this->todayDate]);
    }

    #[Computed]
    public function coursesHeading(): string
    {
        return SystemNotification::getByKey('labels.semester_courses.title');
    }

    #[Computed]
    public function coursesDescription(): string
    {
        return SystemNotification::getByKey('labels.semester_courses.description');
    }

    #[Computed]
    public function sessionsIcon(): string
    {
        return SystemNotification::getByKey('icons.today_sessions');
    }

    #[Computed]
    public function coursesIcon(): string
    {
        return SystemNotification::getByKey('icons.semester_courses');
    }

    #[Computed]
    public function sessionsEmptyHeading(): string
    {
        return SystemNotification::getByKey('labels.empty_today_sessions.title');
    }

    #[Computed]
    public function sessionsEmptyDescription(): string
    {
        return SystemNotification::getByKey('labels.empty_today_sessions.description');
    }

    #[Computed]
    public function sessionsEmptyIcon(): string
    {
        return SystemNotification::getByKey('icons.empty_today_sessions');
    }

    #[Computed]
    public function coursesEmptyHeading(): string
    {
        return SystemNotification::getByKey('labels.empty_semester_courses.title');
    }

    #[Computed]
    public function coursesEmptyDescription(): string
    {
        return SystemNotification::getByKey('labels.empty_semester_courses.description');
    }

    #[Computed]
    public function coursesEmptyIcon(): string
    {
        return SystemNotification::getByKey('icons.empty_semester_courses');
    }
}
