<?php

namespace App\Filament\Resources\Learning\ClassSessions\Pages;

use App\Filament\Resources\Learning\ClassSessions\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\Course;
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
        return ClassSession::query()
            ->whereDate('date', now())
            ->with(['course'])
            ->withCount('attendances')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'id' => $session->id,
                    'is_pending' => false,
                    'session_number' => $session->session_number,
                    'course_name' => $session->course->name,
                    'course_code' => $session->course->code,
                    'lecturer' => $session->course->lecturer ?? '-',
                    'time_range' => $session->start_time->format('H:i').' - '.$session->end_time->format('H:i'),
                    'attendances_count' => $session->attendances_count,
                    'url' => ClassSessionResource::getUrl('course', ['courseId' => $session->course_id]),

                    // Pre-calculated classes
                    'card_classes' => 'group flex w-full rounded-xl border border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm overflow-hidden relative transition-all hover:shadow-md',
                    'session_badge_classes' => 'flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg font-bold border bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700',
                    'title_classes' => 'font-bold transition-colors text-gray-900 dark:text-white group-hover:text-primary-600',
                    'status_badge_classes' => 'flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold shadow-sm bg-primary-500 text-white animate-pulse',
                    'attendance_section_classes' => 'flex flex-col items-center justify-center border-l border-primary-200 dark:border-primary-800 p-2 bg-white/30 dark:bg-black/10 transition-colors group-hover:bg-primary-500/5',
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
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('lecturer', 'like', '%'.$this->search.'%');
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
}
