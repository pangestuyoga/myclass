<x-filament-panels::page>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ \App\Filament\Resources\Learning\ClassSessions\ClassSessionResource::getUrl('index') }}"
            class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 fi-btn-color-gray fi-color-gray bg-white dark:bg-white/5 text-gray-950 dark:text-white shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 fi-btn-icon-start gap-1 p-2 rounded-lg">
            <x-heroicon-m-arrow-left class="w-5 h-5 text-gray-500" />
        </a>
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $this->getTitle() }}
            </h1>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $this->description }}
            </p>
        </div>
    </div>

    <x-filament::section>
        @if ($this->sessions->isNotEmpty())
            <div class="space-y-4">
                @foreach ($this->sessions as $session)
                    <div
                        class="fi-card p-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-300 font-bold border border-primary-200 dark:border-primary-500/20">
                                <span class="text-[10px] uppercase leading-none opacity-60 mb-0.5 mt-1">Sesi</span>
                                <span class="text-xl leading-none mb-1">#{{ $session->session_number }}</span>
                            </div>
                            <div class="space-y-1">
                                <div
                                    class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
                                        <x-heroicon-o-calendar class="w-4 h-4 text-primary-500" />
                                        {{ $session->date_formatted }}
                                    </div>
                                    <div class="flex items-center gap-1.5 font-medium">
                                        <x-heroicon-o-clock class="w-4 h-4 text-primary-500" />
                                        {{ $session->time_range }}
                                    </div>
                                </div>
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
            <x-filament::empty-state icon="heroicon-o-presentation-chart-bar" heading="Tidak ada data yang ditemukan"
                description="Setelah Anda membuat data pertama, maka akan muncul disini." iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
