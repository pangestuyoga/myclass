<x-filament-panels::page>
    <x-filament::section>
        <div class="w-full max-w-xl">
            {{ $this->form }}
        </div>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-bolt" icon-color="primary">
        <x-slot name="heading">Sesi Hari Ini</x-slot>
        <x-slot name="description">Sesi yang dijadwalkan pada {{ $this->today_date }}</x-slot>

        <div class="space-y-4">
            @forelse ($this->todaySessions as $session)
                <a href="{{ $session->url }}" class="{{ $session->card_classes }} flex-col sm:flex-row">

                    <div class="flex flex-1 items-start gap-4 p-4 sm:p-5 min-w-0">
                        <div class="{{ $session->session_badge_classes }} shrink-0">
                            <span class="text-[9px] uppercase leading-none opacity-60 mb-0.5 font-bold">Sesi</span>
                            <span class="text-sm font-bold italic">Ke-{{ $session->session_number }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="{{ $session->title_classes }} truncate">
                                        {{ $session->course_name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-medium truncate">
                                        {{ $session->course_code }} • {{ $session->lecturer }}
                                    </p>
                                </div>

                                <div class="{{ $session->status_badge_classes }} shrink-0 self-start">
                                    <x-heroicon-s-clock class="w-3.5 h-3.5" />
                                    {{ $session->time_range }}
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 mt-3">
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-900/10 cursor-pointer transition-colors"
                                    wire:click="mountAction('viewMaterials', { session: {{ $session->id }} })"
                                    onclick="event.stopPropagation()">
                                    <x-heroicon-o-document-text class="w-4 h-4 text-amber-500 shrink-0" />
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                                        {{ $session->materials_count }}<span class="ml-1 font-medium text-gray-400">Materi</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/10 cursor-pointer transition-colors"
                                    wire:click="mountAction('viewAssignments', { session: {{ $session->id }} })"
                                    onclick="event.stopPropagation()">
                                    <x-heroicon-o-pencil-square class="w-4 h-4 text-primary-500 shrink-0" />
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                                        {{ $session->assignments_count }}<span class="ml-1 font-medium text-gray-400">Tugas</span>
                                    </span>
                                </div>

                                @if ($session->is_pending)
                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-gray-500 font-medium italic">
                                        <x-heroicon-m-calendar-days class="w-3.5 h-3.5 shrink-0" />
                                        Sesuai Jadwal Hari Ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="{{ $session->attendance_section_classes }} border-t border-gray-100 dark:border-gray-700 sm:border-t-0 sm:border-l">
                        @if ($session->is_pending)
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 p-3 opacity-50">
                                <x-heroicon-m-clock class="w-5 h-5" />
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center min-w-[80px] p-3 hover:bg-white/50 dark:hover:bg-white/5 rounded-lg transition-colors cursor-pointer"
                                onclick="event.preventDefault(); event.stopPropagation();"
                                wire:click="mountAction('viewAttendance', { session: {{ $session->id }} })">
                                <div class="flex flex-col items-center">
                                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400 leading-none">
                                        {{ $session->attendances_count }}<span class="text-[10px] text-gray-400 font-normal ml-0.5">/{{ $session->total_students }}</span>
                                    </span>
                                    <div class="mt-1.5 w-12 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary-500 rounded-full transition-all duration-500" style="width: {{ $session->attendance_percentage }}%"></div>
                                    </div>
                                    <span class="text-[9px] uppercase font-bold text-gray-500 mt-1 leading-none tracking-wider">
                                        {{ $session->attendance_percentage }}% Hadir
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <x-filament::empty-state icon="heroicon-o-calendar-days"
                    heading="Tidak ada data yang ditemukan"
                    description="Tidak ada sesi perkuliahan yang dijadwalkan untuk hari ini."
                    iconColor="gray">
                </x-filament::empty-state>
            @endforelse
        </div>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-academic-cap" icon-color="gray">
        <x-slot name="heading">Daftar Mata Kuliah Semester Ini</x-slot>
        <x-slot name="description">Pilih mata kuliah untuk melihat dan mengelola semua riwayat sesi.</x-slot>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse ($this->courses as $course)
                <a href="{{ $course->url }}"
                    class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden transition hover:shadow-md h-fit block group">

                    <div class="p-4 sm:p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                {{ $course->code }}
                            </span>
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500">
                                <span class="text-xs font-medium">{{ $course->sessions_count }} Sesi</span>
                                <x-heroicon-m-chevron-right class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <h3 class="text-base font-bold text-gray-950 dark:text-white leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                                {{ $course->name }}
                            </h3>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <x-heroicon-m-user class="w-4 h-4 mr-1.5 opacity-70 shrink-0" />
                                <span class="truncate">{{ $course->lecturer }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center">
                    <x-filament::empty-state icon="heroicon-o-academic-cap"
                        heading="Mata Kuliah Belum Terdaftar"
                        description="Belum ada mata kuliah yang terdaftar untuk semester aktif ini."
                        iconColor="gray">
                    </x-filament::empty-state>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
