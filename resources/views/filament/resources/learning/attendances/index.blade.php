<x-filament-panels::page>
    <x-filament::section icon="{{ $this->sectionIcon }}" icon-color="primary">
        <x-slot name="heading">{{ $this->heading }}</x-slot>
        <x-slot name="description">{{ $this->description }}</x-slot>

        @if (count($this->todayScheduleCards))
            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
                @foreach ($this->todayScheduleCards as $card)
                    <div class="break-inside-avoid mb-6">
                        <div class="{{ $card->card_classes }}">

                            <div class="p-6 space-y-4 flex-1">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="{{ $card->title_classes }}">
                                            <x-heroicon-o-book-open @class([
                                                'w-5 h-5 shrink-0',
                                                'opacity-40' => !$card->is_attended,
                                                'text-primary-500' => $card->is_attended,
                                            ]) />
                                            {{ $card->course_name }}
                                        </h3>
                                        <span
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 ring-1 ring-inset ring-gray-600/10 shrink-0">
                                            Sesi {{ $card->session_number }}
                                        </span>
                                    </div>
                                    <div class="{{ $card->lecturer_wrapper_classes }}">
                                        <x-heroicon-m-user @class(['w-3.5 h-3.5 shrink-0', 'opacity-50' => !$card->is_attended]) />
                                        <span class="text-[11px] font-medium leading-none">
                                            {{ $card->lecturer_name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 pt-4">
                                        <div class="{{ $card->icon_wrapper_classes }}">
                                            <x-heroicon-m-clock @class([
                                                'w-4 h-4',
                                                'text-primary-600 dark:text-primary-300' => !$card->is_attended,
                                                'text-primary-700 dark:text-primary-200' => $card->is_attended,
                                            ]) />
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span
                                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none mb-1">Waktu
                                                Kuliah</span>
                                            <span class="{{ $card->time_label_classes }}">
                                                {{ $card->time_range }}
                                            </span>
                                        </div>
                                    </div>

                                    <div @class([
                                        'pt-4 border-t',
                                        'border-gray-100 dark:border-gray-700' => !$card->is_attended,
                                        'border-primary-200 dark:border-primary-800' => $card->is_attended,
                                    ])>
                                        <div class="flex items-center justify-between mb-3">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Status</span>
                                            <span class="{{ $card->status_badge_classes }}">
                                                {{ $card->status_label }}
                                            </span>
                                        </div>
                                        @if ($card->is_attended)
                                            <span
                                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none">
                                                Tercatat: <span
                                                    class="text-primary-600 dark:text-primary-200">{{ $card->attended_at }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="{{ $card->footer_classes }}">
                                @if ($card->can_attend)
                                    <x-filament::button wire:click="attend({{ $card->id }})" color="primary"
                                        size="xs" icon="heroicon-m-finger-print"
                                        class="rounded-lg shadow-sm font-bold uppercase tracking-tight text-[10px] w-full">
                                        Presensi Sekarang
                                    </x-filament::button>
                                @elseif ($card->is_attended)
                                    <div
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-primary-800 border border-primary-300 dark:border-primary-600 shadow-sm">
                                        <x-heroicon-m-hand-thumb-up
                                            class="w-3.5 h-3.5 text-primary-600 dark:text-primary-200" />
                                        <span
                                            class="text-[10px] font-bold text-primary-700 dark:text-primary-200 uppercase tracking-tight">
                                            Selesai
                                        </span>
                                    </div>
                                @elseif ($card->is_sent_to_lecturer)
                                    <div class="px-2 py-1 flex items-center gap-1.5">
                                        <x-heroicon-m-paper-airplane class="w-3 h-3 text-danger-500" />
                                        <span
                                            class="text-[10px] font-bold text-danger-500 uppercase tracking-widest leading-none italic">Sesi
                                            Terkirim</span>
                                    </div>
                                @else
                                    <div class="px-2 py-1">
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none italic">Belum
                                            Dibuka</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-filament::empty-state icon="heroicon-o-finger-print" heading="{{ $this->emptyHeading }}"
                description="{{ $this->emptyDescription }}" iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>

    @if (count($this->missedScheduleCards))
        <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="danger"
            class="mb-6 !bg-danger-50/5 border-danger-100 dark:border-danger-900/50">
            <x-slot name="heading">{{ $this->missedHeading }}</x-slot>
            <x-slot name="description">{{ $this->missedDescription }}</x-slot>

            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6 mt-4">
                @foreach ($this->missedScheduleCards as $card)
                    <div class="break-inside-avoid mb-6">
                        <div class="{{ $card->card_classes }}">

                            <div class="p-6 space-y-4 flex-1">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="{{ $card->title_classes }}">
                                            <x-heroicon-o-book-open @class([
                                                'w-5 h-5 shrink-0',
                                                'opacity-40' => !$card->is_attended,
                                                'text-primary-500' => $card->is_attended,
                                            ]) />
                                            {{ $card->course_name }}
                                        </h3>
                                        <span
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 ring-1 ring-inset ring-gray-600/10 shrink-0">
                                            Sesi {{ $card->session_number }}
                                        </span>
                                    </div>
                                    <div class="{{ $card->lecturer_wrapper_classes }}">
                                        <x-heroicon-m-user @class(['w-3.5 h-3.5 shrink-0', 'opacity-50' => !$card->is_attended]) />
                                        <span class="text-[11px] font-medium leading-none">
                                            {{ $card->lecturer_name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 pt-4">
                                        <div class="{{ $card->icon_wrapper_classes }}">
                                            <x-heroicon-m-clock @class([
                                                'w-4 h-4',
                                                'text-primary-600 dark:text-primary-300' => !$card->is_attended,
                                                'text-primary-700 dark:text-primary-200' => $card->is_attended,
                                            ]) />
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span
                                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none mb-1">
                                                {{ $card->date }}
                                            </span>
                                            <span class="{{ $card->time_label_classes }}">
                                                {{ $card->time_range }}
                                            </span>
                                        </div>
                                    </div>

                                    <div @class([
                                        'pt-4 border-t',
                                        'border-gray-100 dark:border-gray-700' => !$card->is_attended,
                                        'border-primary-200 dark:border-primary-800' => $card->is_attended,
                                    ])>
                                        <div class="flex items-center justify-between">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Status</span>
                                            <div class="flex items-center gap-2">
                                                <span class="{{ $card->status_badge_classes }}">
                                                    {{ $card->status_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="{{ $card->footer_classes }}">
                                @if ($card->can_attend)
                                    <x-filament::button wire:click="attend({{ $card->id }})" color="primary"
                                        size="xs" icon="heroicon-m-finger-print"
                                        class="rounded-lg shadow-sm font-bold uppercase tracking-tight text-[10px] w-full">
                                        Presensi Sekarang
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    <x-filament::section icon="{{ $this->historyIcon }}" icon-color="gray">
        <x-slot name="heading">{{ $this->historyHeading }}</x-slot>
        <x-slot name="description">{{ $this->historyDescription }}</x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
