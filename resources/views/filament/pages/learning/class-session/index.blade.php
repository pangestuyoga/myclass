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
    @if ($this->getTodaySessions()->isNotEmpty())
        <x-filament::section icon="heroicon-o-bolt" icon-color="primary">
            <x-slot name="heading">
                Sesi Hari Ini
            </x-slot>
            <x-slot name="description">
                Sesi yang dijadwalkan pada {{ now()->format('l, d F Y') }}
            </x-slot>

            <div class="space-y-4">
                @foreach ($this->getTodaySessions() as $session)
                    <div @class([
                        'group flex w-full rounded-xl border overflow-hidden relative transition-all hover:shadow-md',
                        'border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm' => !$session->is_pending,
                        'border-gray-300 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800/10 border-dashed' => $session->is_pending,
                    ])>
                        <div class="flex flex-1 items-start gap-4 p-5">
                            <div @class([
                                'flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg font-bold border',
                                'bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300 border-primary-200 dark:border-primary-700' => !$session->is_pending,
                                'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700' => $session->is_pending,
                            ])>
                                <span class="text-[9px] uppercase leading-none opacity-60 mb-0.5 font-bold">Sesi</span>
                                <span class="text-xl leading-none italic">#{{ $session->session_number }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <h3 @class([
                                            'font-bold transition-colors',
                                            'text-gray-900 dark:text-white group-hover:text-primary-600' => !$session->is_pending,
                                            'text-gray-500 dark:text-gray-400' => $session->is_pending,
                                        ])>
                                            {{ $session->course->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 font-medium">
                                            {{ $session->course->code }} • {{ $session->course->lecturer->full_name ?? '-' }}
                                        </p>
                                    </div>
                                    <div @class([
                                        'flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold shadow-sm',
                                        'bg-primary-500 text-white animate-pulse' => !$session->is_pending,
                                        'bg-gray-500 text-white opacity-60' => $session->is_pending,
                                    ])>
                                        <x-heroicon-s-clock class="w-3.5 h-3.5" />
                                        {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                    </div>
                                    @if ($session->is_pending)
                                        <div class="mt-2 flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-gray-500 font-medium italic">
                                            <x-heroicon-m-sparkles class="w-3.5 h-3.5" />
                                            Sesi belum digenerate hari ini
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div @class([
                                'flex flex-col items-center justify-center border-l p-2 bg-white/30 dark:bg-black/10 transition-colors',
                                'border-primary-200 dark:border-primary-800 group-hover:bg-primary-500/5' => !$session->is_pending,
                                'border-gray-200 dark:border-gray-800' => $session->is_pending,
                            ])>
                                @if ($session->is_pending)
                                    {{ ($this->generateTodaySessionAction)(['course' => $session->course->id]) }}
                                @else
                                    {{ ($this->editSessionAction)(['session' => $session->id]) }}
                                @endif
                            </div>
                        </div>
                    </div>
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

        @if ($this->getCourses()->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->getCourses() as $course)
                    <a href="{{ \App\Filament\Resources\Learning\ClassSessions\ClassSessionResource::getUrl('course', ['courseId' => $course->id]) }}"
                        class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden transition hover:shadow-md h-fit block group">

                        <div class="p-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                    {{ $course->code }}
                                </span>

                                <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <span class="text-xs font-medium">
                                        {{ $course->classSessions->count() }} Sesi
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
                                    <span
                                        class="truncate">{{ $course->lecturer->full_name ?? 'Dosen Belum Ditentukan' }}</span>
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
