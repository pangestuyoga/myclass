<?php

namespace App\Filament\Pages\Information\CourseSchedule;

use App\Filament\Pages\Information\CourseSchedule\Actions\CreateScheduleAction;
use App\Filament\Pages\Information\CourseSchedule\Actions\DeleteScheduleAction;
use App\Filament\Pages\Information\CourseSchedule\Actions\EditScheduleAction;
use App\Models\Course;
use App\Models\CourseSchedule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class Index extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected static ?string $model = CourseSchedule::class;

    protected static string|UnitEnum|null $navigationGroup = 'Informasi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $title = 'Jadwal Kuliah';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'day_of_week';

    protected static ?string $slug = 'information/course-schedules';

    protected string $view = 'filament.pages.information.list-course-schedules';

    public ?int $course_semester = null;

    public ?string $search = '';

    public static function getPagePermission(): string
    {
        return 'View:CourseSchedule';
    }

    public function mount(): void
    {
        $this->course_semester = null;

        $this->form->fill([
            'course_semester' => $this->course_semester,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('course_semester')
                    ->label('Semester')
                    ->options(array_combine(range(1, 8), array_map(fn ($i) => "Semester $i", range(1, 8))))
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->course_semester = $state),

                TextInput::make('search')
                    ->label('Cari')
                    ->placeholder('Cari Mata Kuliah atau Dosen...')
                    ->autocomplete(false)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($state) => $this->search = $state),
            ])
            ->columns([
                'sm' => 1,
                'md' => 3,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateScheduleAction::make(),
        ];
    }

    protected function editScheduleAction(): Action
    {
        return EditScheduleAction::make();
    }

    protected function deleteScheduleAction(): Action
    {
        return DeleteScheduleAction::make();
    }

    public function getSchedules(): Collection
    {
        $user = auth()->user();
        $lecturer = $user->lecturer;

        $query = CourseSchedule::with(['course.lecturer']);

        if ($lecturer) {
            $query->whereHas('course', fn ($q) => $q->where('lecturer_id', $lecturer->id));
        }

        if ($this->course_semester) {
            $query->whereHas('course', fn ($q) => $q->where('semester', $this->course_semester));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('course', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('course.lecturer', fn ($sq) => $sq->where('full_name', 'like', "%{$this->search}%"));
            });
        }

        return $query->get()
            ->sortBy([
                ['day_of_week', 'asc'],
                ['start_time', 'asc'],
            ])
            ->groupBy('day_of_week');
    }

    protected function getViewData(): array
    {
        return [
            'schedules' => $this->getSchedules(),
            'days' => [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu',
            ],
        ];
    }

    public function scheduleFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('course_id')
                        ->label('Mata Kuliah')
                        ->options(function () {
                            $user = auth()->user();
                            $lecturer = $user->lecturer;

                            if ($lecturer) {
                                $courses = Course::getOptionsForLecturer($lecturer);
                            } else {
                                $courses = Course::all();
                            }

                            return $courses
                                ->groupBy('semester')
                                ->mapWithKeys(function ($courses, $semester) {
                                    return [
                                        "Semester $semester" => $courses->pluck('name', 'id')->toArray(),
                                    ];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->live()
                        ->required()
                        ->columnSpanFull(),

                    Grid::make(3)
                        ->schema([
                            Select::make('day_of_week')
                                ->label('Hari')
                                ->placeholder('Pilih Hari')
                                ->options([
                                    1 => 'Senin',
                                    2 => 'Selasa',
                                    3 => 'Rabu',
                                    4 => 'Kamis',
                                    5 => 'Jumat',
                                    6 => 'Sabtu',
                                    7 => 'Minggu',
                                ])
                                ->native(false)
                                ->required(),

                            TimePicker::make('start_time')
                                ->label('Jam Mulai')
                                ->placeholder('08:00')
                                ->required()
                                ->native(false)
                                ->seconds(false),

                            TimePicker::make('end_time')
                                ->label('Jam Selesai')
                                ->placeholder('10:00')
                                ->required()
                                ->native(false)
                                ->seconds(false),
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }
}
