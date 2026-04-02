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
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 transition hover:border-primary-500/30">
                    <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">
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
            <div class="space-y-4">
                @foreach ($this->changelogs as $log)
                    <div class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition">
                        <div class="p-5 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-{{ $log->type->getColorClass() }}-50 dark:bg-{{ $log->type->getColorClass() }}-500/10 px-2 py-1 text-xs font-semibold text-{{ $log->type->getColorClass() }}-700 dark:text-{{ $log->type->getColorClass() }}-300 ring-1 ring-inset ring-{{ $log->type->getColorClass() }}-600/20">
                                        {{ $log->type->getLabel() }}
                                    </span>

                                    <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800/50 px-2 py-1 text-xs font-bold text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-700">
                                        {{ $log->formatted_version }}
                                    </span>
                                </div>

                                <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 shrink-0">
                                    <x-heroicon-m-calendar class="w-4 h-4 mr-1 opacity-70" />
                                    {{ $log->formatted_release_date }}
                                </span>
                            </div>

                            <h3 class="text-base font-semibold leading-tight text-gray-950 dark:text-white">
                                {{ $log->title }}
                            </h3>

                            @if ($log->description)
                                <div class="text-sm text-gray-500 dark:text-gray-400 prose prose-sm dark:prose-invert max-w-none">
                                    {!! $log->description !!}
                                </div>
                            @endif

                            <div>
                                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none">
                                    Daftar Perubahan
                                </span>
                                <ul class="mt-2 space-y-1.5">
                                    @foreach ($log->changes as $change)
                                        <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <x-heroicon-m-check-circle class="w-4 h-4 mt-0.5 shrink-0 text-{{ $log->type->getColorClass() }}-500" />
                                            <span>{{ $change }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @canAny(['Update:Changelog', 'Delete:Changelog'])
                            <div class="flex items-center justify-end gap-1 px-5 pb-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                                {{ ($this->editChangelogAction)(['record' => $log->id]) }}
                                {{ ($this->deleteChangelogAction)(['record' => $log->id]) }}
                            </div>
                        @endcanAny
                    </div>
                @endforeach
            </div>
        @else
            <x-filament::empty-state
                icon="heroicon-o-information-circle"
                heading="{{ $this->emptyHeading }}"
                description="{{ $this->emptyDescription }}"
                iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
