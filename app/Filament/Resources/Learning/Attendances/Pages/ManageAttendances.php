<?php

namespace App\Filament\Resources\Learning\Attendances\Pages;

use App\Enums\RoleEnum;
use App\Filament\Resources\Learning\Attendances\AttendanceResource;
use App\Filament\Support\SystemNotification;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use App\Settings\GeneralSettings;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;

class ManageAttendances extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = AttendanceResource::class;

    protected static ?string $title = 'Presensi Kuliah';

    protected static ?string $navigationLabel = 'Presensi';

    protected string $view = 'filament.resources.learning.attendances.index';

    public function getSchedules()
    {
        /** @var User $user */
        $user = auth()->user();
        $student = $user->student;

        if (! $student) {
            return collect();
        }

        return ClassSession::query()
            ->whereDate('date', now())
            ->with(['course', 'attendances' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->orderBy('start_time')
            ->get();
    }

    #[Computed]
    public function scheduleCards()
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
                    'lecturer_name' => $schedule->course->lecturer ?? 'Belum Ditentukan',
                    'time_range' => $schedule->start_time->format('H:i').' - '.$schedule->end_time->format('H:i'),
                    'is_attended' => $isAttended,
                    'can_attend' => $canAttend && ! $isAttended,
                    'status_label' => $statusLabel,
                    'status_color' => $statusColor,
                    'status_icon' => $statusIcon,
                    'attended_at' => $isAttended ? $attendance->attended_at->format('H:i') : null,
                    // Pre-calculated classes
                    'card_classes' => Arr::toCssClasses([
                        'fi-card flex flex-col justify-between rounded-xl border transition duration-200 group relative',
                        'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md' => ! $isAttended,
                        'bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500' => $isAttended,
                    ]),
                    'title_classes' => Arr::toCssClasses([
                        'text-lg font-bold leading-tight flex items-center gap-2 transition-colors',
                        'text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400' => ! $isAttended,
                        'text-primary-900 dark:text-primary-100' => $isAttended,
                    ]),
                    'lecturer_wrapper_classes' => Arr::toCssClasses([
                        'flex items-center gap-1.5',
                        'text-gray-500 dark:text-gray-400' => ! $isAttended,
                        'text-primary-600 dark:text-primary-400' => $isAttended,
                    ]),
                    'icon_wrapper_classes' => Arr::toCssClasses([
                        'w-8 h-8 rounded-full flex items-center justify-center mr-3 shrink-0 border',
                        'bg-primary-100 dark:bg-primary-900/40 border-primary-200 dark:border-primary-800' => ! $isAttended,
                        'bg-white dark:bg-primary-800 border-primary-300 dark:border-primary-600' => $isAttended,
                    ]),
                    'time_label_classes' => Arr::toCssClasses([
                        'truncate font-medium',
                        'text-gray-100 dark:text-gray-200' => ! $isAttended,
                        'text-primary-900 dark:text-primary-50' => $isAttended,
                    ]),
                    'status_badge_classes' => Arr::toCssClasses([
                        'text-[10px] font-bold px-1.5 py-0.5 rounded ring-1 ring-inset',
                        'bg-success-50 dark:bg-success-500/10 text-success-600 dark:text-success-400 ring-success-600/20' => $isAttended,
                        'bg-warning-50 dark:bg-warning-500/10 text-warning-600 dark:text-warning-400 ring-warning-600/20' => ! $isAttended && $canAttend && ! $isAttended,
                        'bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 ring-gray-600/20' => ! $isAttended && ! $canAttend,
                    ]),
                    'footer_classes' => Arr::toCssClasses([
                        'flex items-center justify-end gap-2 p-4 pt-0 rounded-b-xl',
                        'bg-primary-100/30 dark:bg-primary-900/10 pt-4' => $isAttended || ($canAttend && ! $isAttended),
                    ]),
                ];
            });
    }

    public function attend(int $sessionId): void
    {
        /** @var User $user */
        $user = auth()->user();
        $student = $user->student;
        if (! $student) {
            return;
        }

        $session = ClassSession::find($sessionId);
        if (! $session) {
            return;
        }

        // Check if already attended
        $existing = Attendance::where('student_id', $student->id)
            ->where('class_session_id', $sessionId)
            ->first();

        if ($existing) {
            SystemNotification::warning(
                'Presensi Sudah Tercatat ⚠️',
                'Anda telah melakukan presensi untuk sesi perkuliahan ini.'
            )->send();

            return;
        }

        // Check time
        $startTime = $session->start_time;
        if (now()->lessThan($startTime)) {
            SystemNotification::warning(
                'Presensi Belum Dibuka ⏳',
                'Waktu presensi untuk sesi perkuliahan ini belum dimulai.'
            )->send();

            return;
        }

        // Find schedule for compatibility
        $schedule = CourseSchedule::where('course_id', $session->course_id)
            ->where('day_of_week', $session->date->dayOfWeekIso)
            ->first();

        Attendance::create([
            'student_id' => $student->id,
            'class_session_id' => $sessionId,
            'course_schedule_id' => $schedule?->id,
            'date' => $session->date->toDateString(),
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
            ->modifyQueryUsing(fn (Builder $query) => $query->where('student_id', $user->student?->id))
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Mahasiswa')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => $user->hasRole([RoleEnum::Developer, RoleEnum::Kosma])),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('l, d F Y')
                    ->sortable()
                    ->description(fn (Attendance $record): string => $record->attended_at->format('H:i').' WIB')
                    ->color('gray'),

                TextColumn::make('courseSchedule.course.name')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Attendance $record): string => $record->courseSchedule->course->lecturer ?? 'Belum Ditentukan'),
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
                            fn (Builder $query) => $query->whereHas('courseSchedule', fn ($q) => $q->where('course_id', $data['value']))
                        );
                    })
                    ->searchable(),
            ])
            ->emptyStateIcon('heroicon-o-finger-print')
            ->emptyStateDescription('Belum ada riwayat presensi yang tercatat untuk Anda saat ini.')
            ->deferFilters(false)
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }
}
