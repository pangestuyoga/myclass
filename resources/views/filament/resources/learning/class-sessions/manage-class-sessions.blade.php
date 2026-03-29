<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="w-full max-w-xl">
                {{ $this->form }}
            </div>
        </div>

        @if($this->getCourses()->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($this->getCourses() as $course)
                    <div 
                        x-data="{ open: false }" 
                        class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden transition hover:shadow-md h-fit"
                    >
                        <!-- Course Header Card -->
                        <div 
                            @click="open = !open" 
                            class="p-5 cursor-pointer space-y-4 group"
                        >
                            <div class="flex items-center justify-between">
                                 <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-500/10 px-2 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                                    {{ $course->code }}
                                </span>
                                
                                <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500">
                                    <span class="text-xs font-medium">
                                        {{ $course->classSessions->count() }} Sesi
                                    </span>
                                    <x-heroicon-m-chevron-down 
                                        class="w-4 h-4 transition-transform duration-300"
                                        x-bind:class="open ? 'rotate-180' : ''"
                                    />
                                </div>
                            </div>

                            <div class="space-y-1">
                                <h3 class="text-base font-bold text-gray-950 dark:text-white leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                                    {{ $course->name }}
                                </h3>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                     <x-heroicon-m-user class="w-4 h-4 mr-1.5 opacity-70" />
                                     <span class="truncate">{{ $course->lecturer->full_name ?? 'Dosen Belum Ditentukan' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sessions List (Collapsible) -->
                        <div 
                            x-show="open" 
                            x-collapse 
                            x-cloak 
                            class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20"
                        >
                            @if($course->classSessions->isNotEmpty())
                                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-80 overflow-y-auto">
                                    @foreach($course->classSessions as $session)
                                        <div class="p-4 flex items-center justify-between hover:bg-gray-100/50 dark:hover:bg-gray-800/50 transition">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                                        #{{ $session->session_number }}
                                                    </span>
                                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $session->date->format('l, d/m/Y') }}
                                                    </span>
                                                </div>
                                                <p class="text-[12px] text-gray-600 dark:text-gray-400 line-clamp-1">
                                                    {{ $session->title ?? 'Materi belum diisi' }}
                                                </p>
                                                <div class="flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                                                    <x-heroicon-o-clock class="w-3.5 h-3.5 text-primary-500" />
                                                    {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                 {{ ($this->editSessionAction)(['session' => $session->id]) }}
                                                 {{ ($this->deleteSessionAction)(['session' => $session->id]) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-6 text-center">
                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                        Belum ada laporan sesi untuk mata kuliah ini.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-filament::empty-state
                icon="heroicon-o-presentation-chart-bar"
                heading="Tidak ada mata kuliah"
                description="Mata kuliah untuk semester ini belum tersedia atau tidak sesuai dengan pencarian."
            />
        @endif
    </x-filament::section>
</x-filament-panels::page>
