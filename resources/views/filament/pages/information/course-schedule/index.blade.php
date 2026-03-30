<x-filament-panels::page>
    <x-filament::section>
        <div
            class="fi-section-content rounded-xl border border-info-200 bg-info-50 dark:bg-info-500/10 dark:border-info-500/30 p-4 mb-6">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-info-600 dark:text-info-400 mt-0.5" />

                <div class="text-sm text-info-800 dark:text-info-200">
                    Geser ke samping untuk melihat hari lainnya.
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="w-full max-w-xl">
                {{ $this->form }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="flex flex-nowrap gap-6 min-w-max pb-4">
                @forelse ($this->schedulesGrouped as $dayName => $daySchedules)
                    <section class="w-80 shrink-0 space-y-4">

                        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-primary-500" />
                            <h2 class="text-lg font-semibold tracking-tight text-gray-950 dark:text-white">
                                {{ $dayName }}
                            </h2>
                        </div>

                        <div class="space-y-4">
                            @foreach ($daySchedules as $schedule)
                                <div
                                    class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition">

                                    <div class="p-5 space-y-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                                    {{ $schedule->course_code }}
                                                </span>

                                                @if ($schedule->mode_label)
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-{{ $schedule->mode_color }}-50 dark:bg-{{ $schedule->mode_color }}-500/10 px-2 py-1 text-xs font-semibold text-{{ $schedule->mode_color }}-700 dark:text-{{ $schedule->mode_color }}-300 ring-1 ring-inset ring-{{ $schedule->mode_color }}-600/20">
                                                        {{ $schedule->mode_label }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($schedule->room)
                                                <span
                                                    class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                    <x-heroicon-m-map-pin class="w-4 h-4 mr-1 opacity-70" />
                                                    {{ $schedule->room }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3
                                            class="text-base font-semibold leading-tight text-gray-950 dark:text-white">
                                            {{ $schedule->course_name }}
                                        </h3>

                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <x-heroicon-m-user class="w-4 h-4 mr-1.5 opacity-70" />
                                            <span class="truncate">{{ $schedule->lecturer }}</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            @if ($schedule->semester)
                                                <span
                                                    class="inline-flex items-center gap-1 rounded-md bg-amber-50 dark:bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20">
                                                    <x-heroicon-m-academic-cap class="w-3 h-3" />
                                                    Semester {{ $schedule->semester }}
                                                </span>
                                            @endif
                                        </div>

                                        <div
                                            class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-sm">
                                            <div
                                                class="flex items-center font-medium text-gray-800 dark:text-gray-200">
                                                <x-heroicon-o-clock class="w-4 h-4 mr-1.5 text-primary-500" />
                                                {{ $schedule->time_range }}
                                            </div>

                                            <span
                                                class="text-[11px] font-semibold tracking-wide text-gray-400 uppercase">
                                                {{ $schedule->credit }} SKS
                                            </span>
                                        </div>

                                        @canAny(['Update:CourseSchedule', 'Delete:CourseSchedule'])
                                            <div
                                                class="flex items-center justify-end gap-1 pt-3 border-t border-gray-100 dark:border-gray-800">
                                                {{ ($this->editScheduleAction)(['schedule' => $schedule->id]) }}

                                                {{ ($this->deleteScheduleAction)(['schedule' => $schedule->id]) }}
                                            </div>
                                        @endcanAny
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="w-full">
                        <x-filament::empty-state icon="heroicon-o-calendar-days" heading="Tidak ada data yang ditemukan"
                            description="Belum ada jadwal perkuliahan yang terdaftar untuk kriteria ini. Hubungi administrator jika jadwal Anda belum muncul."
                            iconColor="gray">
                        </x-filament::empty-state>
                    </div>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
