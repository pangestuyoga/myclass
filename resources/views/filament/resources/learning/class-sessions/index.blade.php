<x-filament-panels::page>
    <!-- Search Section -->
    <x-filament::section>
        <div class="flex items-center justify-between gap-4">
            <div class="w-full max-w-xl">
                {{ $this->form }}
            </div>
        </div>
    </x-filament::section>

    <!-- Today's Sessions Section -->
    @if ($this->todaySessions->isNotEmpty())
        <x-filament::section icon="heroicon-o-bolt" icon-color="primary">
            <x-slot name="heading">
                Sesi Hari Ini
            </x-slot>
            <x-slot name="description">
                Sesi yang dijadwalkan pada {{ $this->today_date }}
            </x-slot>

            <div class="space-y-4">
                @foreach ($this->todaySessions as $session)
                    <a href="{{ $session->url }}" class="{{ $session->card_classes }}">
                        <div class="flex flex-1 items-start gap-4 p-5">
                            <div class="{{ $session->session_badge_classes }}">
                                <span class="text-[9px] uppercase leading-none opacity-60 mb-0.5 font-bold">Sesi</span>
                                <span class="text-sm font-bold italic">Ke-{{ $session->session_number }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <h3 class="{{ $session->title_classes }}">
                                            {{ $session->course_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-medium">
                                            {{ $session->course_code }} • {{ $session->lecturer }}
                                        </p>
                                    </div>
                                    <div class="{{ $session->status_badge_classes }}">
                                        <x-heroicon-s-clock class="w-3.5 h-3.5" />
                                        {{ $session->time_range }}
                                    </div>
                                    @if ($session->is_pending)
                                        <div
                                            class="mt-2 flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-gray-500 font-medium italic">
                                            <x-heroicon-m-calendar-days class="w-3.5 h-3.5" />
                                            Sesuai Jadwal Hari Ini
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $session->attendance_section_classes }}">
                                @if ($session->is_pending)
                                    <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 p-2 opacity-50">
                                        <x-heroicon-m-clock class="w-5 h-5" />
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center min-w-[60px] p-2 hover:bg-white/50 dark:hover:bg-white/5 rounded-lg transition-colors cursor-pointer" 
                                     onclick="event.preventDefault(); event.stopPropagation();"
                                     wire:click="mountAction('viewAttendance', { session: {{ $session->id }} })">
                                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400 leading-none">
                                        {{ $session->attendances_count }}
                                    </span>
                                    <span class="text-[9px] uppercase font-bold text-gray-500 mt-1 leading-none tracking-wider">
                                        Hadir
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <!-- All Courses Section -->
    <x-filament::section icon="heroicon-o-academic-cap" icon-color="gray">
        <x-slot name="heading">
            Daftar Mata Kuliah Semester Ini
        </x-slot>
        <x-slot name="description">
            Pilih mata kuliah untuk melihat dan mengelola semua riwayat sesi.
        </x-slot>

        @if ($this->courses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->courses as $course)
                    <a href="{{ $course->url }}"
                        class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden transition hover:shadow-md h-fit block group">

                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                    {{ $course->code }}
                                </span>

                                <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <span class="text-xs font-medium">
                                        {{ $course->sessions_count }} Sesi
                                    </span>
                                    <x-heroicon-m-chevron-right
                                        class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
                                </div>
                            </div>

                            <div class="space-y-1">
                                <h3
                                    class="text-base font-bold text-gray-950 dark:text-white leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                                    {{ $course->name }}
                                </h3>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <x-heroicon-m-user class="w-4 h-4 mr-1.5 opacity-70" />
                                    <span class="truncate">{{ $course->lecturer }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-filament::empty-state icon="heroicon-o-presentation-chart-bar" heading="Tidak ada data yang ditemukan"
                description="Setelah Anda membuat data pertama, maka akan muncul disini." iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
