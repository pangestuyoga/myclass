<?php

namespace App\Filament\Pages\Information\CourseSchedule;

use App\Filament\Pages\Information\CourseSchedule\Actions\CreateScheduleAction;
use App\Filament\Pages\Information\CourseSchedule\Actions\DeleteScheduleAction;
use App\Filament\Pages\Information\CourseSchedule\Actions\EditScheduleAction;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
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
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use UnitEnum;

class Index extends Page implements HasActions, HasForms
{
    use HasPageShield, InteractsWithActions, InteractsWithForms;

    protected static ?string $model = CourseSchedule::class;

    protected static string|UnitEnum|null $navigationGroup = 'Informasi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $title = 'Jadwal Kuliah';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'day_of_week';

    protected static ?string $slug = 'information/course-schedules';

    protected string $view = 'filament.pages.information.course-schedule.index';

    public ?string $search = '';

    public static function getPagePermission(): string
    {
        return 'View:CourseSchedule';
    }

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
        return [
            CreateScheduleAction::make()
                ->visible(fn () => auth()->user()->can('Create:CourseSchedule')),
        ];
    }

    public function editScheduleAction(): Action
    {
        return EditScheduleAction::make()
            ->visible(fn () => auth()->user()->can('Update:CourseSchedule'));
    }

    public function deleteScheduleAction(): Action
    {
        return DeleteScheduleAction::make()
            ->visible(fn () => auth()->user()->can('Delete:CourseSchedule'));
    }

    public function getSchedules(): Collection
    {
        $query = CourseSchedule::with(['course']);

        $query->whereHas('course', fn ($q) => $q->where('semester', app(GeneralSettings::class)->current_semester));

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('course', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('lecturer', 'like', "%{$this->search}%"));
            });
        }

        return $query->get()
            ->sortBy([
                ['day_of_week', 'asc'],
                ['start_time', 'asc'],
            ])
            ->groupBy('day_of_week')
            ->map(function ($daySchedules) {
                return $daySchedules->map(fn ($schedule) => (object) [
                    'id' => $schedule->id,
                    'course_code' => $schedule->course->code ?? 'MATKUL',
                    'course_name' => $schedule->course->name ?? 'Mata Kuliah Tidak Diketahui',
                    'lecturer' => $schedule->course->lecturer ?? 'Dosen Belum Ditentukan',
                    'semester' => $schedule->course->semester,
                    'credit' => $schedule->course->credit ?? '-',
                    'room' => $schedule->room,
                    'mode_label' => $schedule->mode?->getLabel(),
                    'mode_color' => $schedule->mode?->getColor(),
                    'time_range' => $schedule->start_time->format('H:i').' – '.$schedule->end_time->format('H:i'),
                ]);
            });
    }

    #[Computed]
    public function schedulesGrouped(): Collection
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $this->getSchedules()->mapWithKeys(function ($schedules, $day) use ($days) {
            return [$days[$day] ?? 'Lainnya' => $schedules];
        });
    }

    public function scheduleFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('course_id')
                        ->label('Mata Kuliah')
                        ->options(function ($record) {
                            return Course::query()
                                ->where('semester', app(GeneralSettings::class)->current_semester)
                                ->where(function ($query) use ($record) {
                                    $query->whereDoesntHave('courseSchedules');

                                    if ($record && $record->course_id) {
                                        $query->orWhere('id', $record->course_id);
                                    }
                                })
                                ->pluck('name', 'id')
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
