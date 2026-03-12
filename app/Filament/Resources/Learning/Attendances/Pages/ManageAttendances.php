<?php

namespace App\Filament\Resources\Learning\Attendances\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\Attendances\AttendanceResource;
use App\Filament\Support\SystemNotification;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageAttendances extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = AttendanceResource::class;

    protected static ?string $title = 'Presensi Kuliah';

    protected static ?string $navigationLabel = 'Presensi';

    protected string $view = 'filament.resources.learning.attendances.pages.list-attendances';

    public function getSchedules()
    {
        /** @var User $user */
        $user = auth()->user();
        $student = $user->student;

        if (! $student) {
            return collect();
        }

        $today = now()->dayOfWeekIso;
        $semester = app(GeneralSettings::class)->current_semester;

        return CourseSchedule::query()
            ->where('day_of_week', $today)
            ->whereHas('course', function ($q) use ($semester) {
                $q->where('semester', $semester);
            })
            ->with(['course.lecturer', 'attendances' => function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->whereDate('date', now()->toDateString());
            }])
            ->orderBy('start_time')
            ->get();
    }

    public function getScheduleCards()
    {
        return $this->getSchedules()
            ->map(function ($schedule) {
                $attendance = $schedule->attendances->first();
                $isAttended = $attendance !== null;

                $now = now();
                $startTime = now()->setTimeFrom($schedule->start_time);

                $canAttend = $now->greaterThanOrEqualTo($startTime);

                $statusLabel = 'Belum Presensi';
                $statusColor = 'warning';
                $statusIcon = 'heroicon-o-clock';

                if ($isAttended) {
                    $statusLabel = 'Hadir';
                    $statusColor = 'success';
                    $statusIcon = 'heroicon-o-check-circle';
                } elseif (! $canAttend) {
                    $statusLabel = 'Belum Dimulai';
                    $statusColor = 'gray';
                    $statusIcon = 'heroicon-o-lock-closed';
                }

                return (object) [
                    'id' => $schedule->id,
                    'course_name' => $schedule->course->name,
                    'lecturer_name' => $schedule->course->lecturer?->full_name ?? 'Belum Ditentukan',
                    'time_range' => $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i'),
                    'is_attended' => $isAttended,
                    'can_attend' => $canAttend && ! $isAttended,
                    'status_label' => $statusLabel,
                    'status_color' => $statusColor,
                    'status_icon' => $statusIcon,
                    'attended_at' => $isAttended ? $attendance->attended_at->format('H:i') : null,
                ];
            });
    }

    protected function getViewData(): array
    {
        return [
            'scheduleCards' => $this->getScheduleCards(),
        ];
    }

    public function attend(int $scheduleId): void
    {
        /** @var User $user */
        $user = auth()->user();
        $student = $user->student;
        if (! $student) {
            return;
        }

        $schedule = CourseSchedule::find($scheduleId);
        if (! $schedule) {
            return;
        }

        // Check if already attended
        $existing = Attendance::where('student_id', $student->id)
            ->where('course_schedule_id', $scheduleId)
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($existing) {
            SystemNotification::warning(
                'Presensi Sudah Tercatat ⚠️',
                'Anda telah melakukan presensi untuk jadwal perkuliahan ini pada hari ini.'
            )->send();

            return;
        }

        // Check time
        $startTime = now()->setTimeFrom($schedule->start_time);
        if (now()->lessThan($startTime)) {
            SystemNotification::warning(
                'Presensi Belum Dibuka ⏳',
                'Waktu presensi untuk jadwal perkuliahan ini belum dimulai. Silakan lakukan presensi setelah waktu dimulai.'
            )->send();

            return;
        }

        Attendance::create([
            'student_id' => $student->id,
            'course_schedule_id' => $scheduleId,
            'date' => now()->toDateString(),
            'attended_at' => now(),
        ]);

        SystemNotification::success(
            'Presensi Berhasil Dicatat ✅',
            'Kehadiran Anda telah berhasil direkam ke dalam sistem.'
        )->send();
    }

    public function table(Table $table): Table
    {
        /** @var User $user */
        $user = auth()->user();

        return $table
            ->query(AttendanceResource::getEloquentQuery())
            ->modifyQueryUsing(fn(Builder $query) => $query->where('student_id', $user->student?->id))
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Mahasiswa')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => $user->hasRole([RoleEnum::Developer, RoleEnum::Kosma])),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('l, d F Y')
                    ->sortable()
                    ->description(fn(Attendance $record): string => $record->attended_at->format('H:i') . ' WIB')
                    ->color('gray'),

                TextColumn::make('courseSchedule.course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn(Attendance $record): string => $record->courseSchedule->course->lecturer->full_name),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Mata Kuliah')
                    ->options(function () {
                        return Course::query()
                            ->where('semester', app(GeneralSettings::class)->current_semester)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn(Builder $query) => $query->whereHas('courseSchedule', fn($q) => $q->where('course_id', $data['value']))
                        );
                    })
                    ->searchable(),
            ])
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->emptyStateDescription('Setelah Anda membuat data pertama, maka akan muncul disini.')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }
}
