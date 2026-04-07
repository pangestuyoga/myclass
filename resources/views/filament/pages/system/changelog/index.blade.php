<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-xl bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400">
                <x-heroicon-o-rocket-launch class="w-8 h-8" />
            </div>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ $this->techStack['name'] }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Versi Aplikasi:
                    <span class="font-semibold text-primary-600 dark:text-primary-400">
                        {{ $this->techStack['version'] }}
                    </span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-gray-100 dark:border-white/5">
            @foreach ($this->techStack['stack'] as $tech => $version)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 transition duration-200 hover:border-primary-500/30 hover:shadow-sm">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500 mb-1">
                        {{ $tech }}
                    </p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate">
                        {{ $version }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section>
        @if (count($this->changelogs))
            <div 
                class="space-y-4" 
                x-data="{ expanded: @js($this->changelogs->first()?->id) }"
                x-on:refresh-expansion.window="expanded = $event.detail.id"
            >
                @foreach ($this->changelogs as $index => $log)
                    <div wire:key="changelog-{{ $log->id }}" @class([
                        'fi-card rounded-xl border transition overflow-hidden shadow-sm hover:shadow-md',
                        'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900' => $log->is_read,
                        'border-primary-500/50 dark:border-primary-400/30 bg-primary-50/30 dark:bg-primary-400/5 shadow-primary-500/5' => !$log->is_read,
                    ])>
                        {{-- Header / Clickable Toggle --}}
                        <button 
                            type="button" 
                            x-on:click="expanded = (expanded === {{ $log->id }} ? null : {{ $log->id }})"
                            class="w-full text-left p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition duration-200 hover:bg-gray-50/50 dark:hover:bg-white/5"
                        >
                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-bold leading-tight text-gray-950 dark:text-white">
                                        {{ $log->title }}
                                    </h3>
                                    @if (!$log->is_read)
                                        <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-bold text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 animate-pulse">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-filament::badge :color="$log->type->getColor()" :icon="$log->type->getIcon()" size="sm">
                                        {{ $log->type->getLabel() }}
                                    </x-filament::badge>
                                    
                                    <x-filament::badge color="gray" icon="heroicon-m-tag" size="sm">
                                        {{ $log->formatted_version }}
                                    </x-filament::badge>
                                    
                                    <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                        <x-heroicon-m-calendar class="w-4 h-4 mr-1.5 opacity-70" />
                                        {{ $log->formatted_release_date }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <x-heroicon-m-chevron-down 
                                    class="w-5 h-5 text-gray-400 transition duration-300" 
                                    x-bind:class="{ 'rotate-180': expanded === {{ $log->id }} }"
                                />
                            </div>
                        </button>

                        {{-- Body Content --}}
                        <div 
                            x-show="expanded === {{ $log->id }}" 
                            x-collapse
                            x-cloak
                        >
                            <div class="px-6 pb-6 pt-2 space-y-6 border-t border-gray-100 dark:border-white/5">
                                @if ($log->description)
                                    <div class="text-sm text-gray-600 dark:text-gray-300 prose prose-sm dark:prose-invert fi-prose max-w-none">
                                        {!! $log->description !!}
                                    </div>
                                @endif

                                <div class="pt-2">
                                    <span class="text-[10px] font-bold uppercase text-gray-500 dark:text-gray-400 tracking-widest">
                                        Perubahan Detail
                                    </span>
                                    <ul class="mt-3 space-y-2.5">
                                        @foreach ($log->changes as $change)
                                            <li class="flex items-start gap-3">
                                                @php
                                                    $colorClass = match ($log->type->getColor()) {
                                                        'success' => 'text-success-500 dark:text-success-400',
                                                        'info' => 'text-info-500 dark:text-info-400',
                                                        'danger' => 'text-danger-500 dark:text-danger-400',
                                                        'warning' => 'text-warning-500 dark:text-warning-400',
                                                        default => 'text-primary-500 dark:text-primary-400',
                                                    };
                                                @endphp
                                                <x-heroicon-m-check-circle class="w-5 h-5 mt-0.5 shrink-0 {{ $colorClass }}" />
                                                <span class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                                                    {{ $change }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="flex items-center justify-between gap-4 pt-4 mt-6 border-t border-gray-100 dark:border-white/5">
                                    <div class="flex items-center gap-2">
                                        @if (!$log->is_read)
                                            {{ ($this->markAsReadAction)(['record' => $log->id]) }}
                                        @else
                                            <div class="flex items-center gap-1.5 text-xs font-semibold text-success-600 dark:text-success-400 bg-success-50 dark:bg-success-500/10 px-3 py-1.5 rounded-lg border border-success-200 dark:border-success-500/20">
                                                <x-heroicon-m-check-badge class="w-4 h-4" />
                                                Sudah Dibaca
                                            </div>
                                        @endif
                                    </div>

                                    @canAny(['Update:Changelog', 'Delete:Changelog'])
                                        <div class="flex items-center gap-2">
                                            {{ ($this->viewReadersAction)(['record' => $log->id]) }}
                                            {{ ($this->editChangelogAction)(['record' => $log->id]) }}
                                            {{ ($this->deleteChangelogAction)(['record' => $log->id]) }}
                                        </div>
                                    @endcanAny
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-filament::empty-state
                icon="heroicon-o-document-text"
                heading="{{ $this->emptyHeading }}"
                description="{{ $this->emptyDescription }}"
                class="py-12">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
