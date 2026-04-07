<x-filament-panels::page>
    <x-filament::section
        icon="{{ $this->icon }}"
        icon-color="primary">
        <x-slot name="heading">{{ $this->heading }}</x-slot>
        <x-slot name="description">{{ $this->description }}</x-slot>

        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="w-full">
                {{ $this->form }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($this->assignmentCards->take($this->perPage) as $card)
                <div class="{{ $card->card_classes }} flex-col">

                    <a href="{{ $card->url }}"
                        class="flex flex-1 items-start gap-4 p-5 text-left min-w-0 hover:bg-primary-500/5 transition-colors cursor-pointer">

                        <div class="{{ $card->icon_wrapper_classes }} h-12 w-12 mt-1 shrink-0">
                            <x-filament::icon :icon="$card->submission_status->icon" class="h-6 w-6" />
                        </div>

                        <div class="flex-1 min-w-0">

                            <p class="font-bold text-gray-900 dark:text-white text-lg leading-tight">
                                {{ $card->title }}
                            </p>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1 truncate">
                                {{ $card->course_name }}
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                @if ($card->is_pinned)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 px-2.5 py-1 text-xs font-bold whitespace-nowrap border border-primary-200/50 dark:border-primary-500/20">
                                        📌 Dipinned
                                    </span>
                                @endif
                                @if ($card->is_new)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400 px-2.5 py-1 text-xs font-bold whitespace-nowrap border border-violet-200/50 dark:border-violet-500/20">
                                        ✨ Baru
                                    </span>
                                @endif
                                @if ($card->is_group)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2.5 py-1 text-xs font-bold whitespace-nowrap border border-blue-200/50 dark:border-blue-500/20">
                                        👥 Kelompok
                                    </span>
                                @endif

                                {{-- Status Pengumpulan (Submission Status) --}}
                                <span class="{{ $card->submission_status->classes }} whitespace-nowrap">
                                    <x-filament::icon :icon="$card->submission_status->icon" class="h-3.5 w-3.5" />
                                    {{ $card->submission_status->label }}
                                </span>

                                {{-- Status Pengiriman ke Dosen (Delivery Status) --}}
                                <span class="{{ $card->delivery_status->classes }} whitespace-nowrap">
                                    <x-filament::icon :icon="$card->delivery_status->icon" class="h-3.5 w-3.5" />
                                    {{ $card->delivery_status->label }}
                                </span>

                                @if ($card->is_urgent)
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-danger-500 text-white px-2.5 py-1 text-xs font-bold animate-pulse whitespace-nowrap shadow-sm shadow-danger-300 dark:shadow-none">
                                        🔥 Segera!
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-x-4 gap-y-2 mt-4 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1.5">
                                    <x-filament::icon icon="heroicon-o-clock"
                                        class="h-4 w-4 shrink-0 text-primary-500" />
                                    <span class="font-medium">Batas: {{ $card->due_date_formatted }}</span>
                                </span>
                                @if ($card->submitted_at_formatted)
                                    <span
                                        class="flex items-center gap-1.5 text-success-600 dark:text-success-400 font-bold bg-success-50 dark:bg-success-900/25 px-2 py-0.5 rounded-lg">
                                        <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-4 w-4 shrink-0" />
                                        <span>Dikumpulkan: {{ $card->submitted_at_formatted }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <div
                        class="flex flex-wrap items-center gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/20">
                        {{ ($this->pinAction)(['record' => $card->id]) }}
                        @can('Share:Assignment')
                            {{ ($this->shareAssignmentAction)(['record' => $card->id]) }}
                        @endcan

                        @canAny(['Update:Assignment', 'Delete:Assignment'])
                            {{ ($this->markAsSentAction)(['record' => $card->id]) }}
                            {{ ($this->editAssignmentAction)(['record' => $card->id]) }}
                            {{ ($this->deleteAssignmentAction)(['record' => $card->id]) }}
                        @endcanAny
                    </div>

                </div>
            @empty
                <div class="col-span-full">
                    <x-filament::empty-state icon="heroicon-o-clipboard-document-list"
                        heading="{{ $this->emptyStateHeading }}"
                        description="{{ $this->emptyStateDescription }}"
                        iconColor="gray">
                    </x-filament::empty-state>
                </div>
            @endforelse
        </div>

        @if ($this->assignmentCards->count() > $this->perPage)
            <div x-intersect="$wire.loadMore()" class="h-10 mt-6 flex items-center justify-center text-primary-500">
                <x-filament::loading-indicator class="h-8 w-8" />
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
