<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Sesi Kuliah</x-slot>
        <x-slot name="description">Silakan melakukan presensi sesuai dengan waktu kelas.</x-slot>

        @if ($scheduleCards->isNotEmpty())
            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6 pb-8">
                @foreach ($scheduleCards as $card)
                    <div class="break-inside-avoid mb-6">
                        <div @class([
                            'fi-card flex flex-col justify-between rounded-xl border transition duration-200 group relative',
                            'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md' => !$card->is_attended,
                            'bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500' =>
                                $card->is_attended,
                        ])>

                            <div class="p-6 space-y-4 flex-1">
                                <div class="space-y-1">
                                    <h3 @class([
                                        'text-lg font-bold leading-tight flex items-center gap-2 transition-colors',
                                        'text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400' => !$card->is_attended,
                                        'text-primary-900 dark:text-primary-100' => $card->is_attended,
                                    ])>
                                        <x-heroicon-o-book-open @class([
                                            'w-5 h-5 shrink-0',
                                            'opacity-40' => !$card->is_attended,
                                            'text-primary-500' => $card->is_attended,
                                        ]) />
                                        {{ $card->course_name }}
                                    </h3>
                                    <div @class([
                                        'flex items-center gap-1.5',
                                        'text-gray-500 dark:text-gray-400' => !$card->is_attended,
                                        'text-primary-600 dark:text-primary-400' => $card->is_attended,
                                    ])>
                                        <x-heroicon-m-user @class(['w-3.5 h-3.5 shrink-0', 'opacity-50' => !$card->is_attended]) />
                                        <span class="text-[11px] font-medium leading-none">
                                            {{ $card->lecturer_name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 pt-4">
                                        <div @class([
                                            'w-8 h-8 rounded-full flex items-center justify-center mr-3 shrink-0 border',
                                            'bg-primary-100 dark:bg-primary-900/40 border-primary-200 dark:border-primary-800' => !$card->is_attended,
                                            'bg-white dark:bg-primary-800 border-primary-300 dark:border-primary-600' =>
                                                $card->is_attended,
                                        ])>
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
                                            <span @class([
                                                'truncate font-medium',
                                                'text-gray-900 dark:text-gray-200' => !$card->is_attended,
                                                'text-primary-900 dark:text-primary-50' => $card->is_attended,
                                            ])>
                                                {{ $card->time_range }}
                                            </span>
                                        </div>
                                    </div>

                                    <div @class([
                                        'pt-4 border-t',
                                        'border-gray-100 dark:border-gray-800' => !$card->is_attended,
                                        'border-primary-200 dark:border-primary-800' => $card->is_attended,
                                    ])>
                                        <div class="flex items-center justify-between mb-3">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Status</span>
                                            <span @class([
                                                'text-[10px] font-bold px-1.5 py-0.5 rounded ring-1 ring-inset',
                                                'bg-success-50 dark:bg-success-500/10 text-success-600 dark:text-success-400 ring-success-600/20' =>
                                                    $card->is_attended,
                                                'bg-warning-50 dark:bg-warning-500/10 text-warning-600 dark:text-warning-400 ring-warning-600/20' =>
                                                    !$card->is_attended && $card->can_attend,
                                                'bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 ring-gray-600/20' =>
                                                    !$card->is_attended && !$card->can_attend,
                                            ])>
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

                            <div @class([
                                'flex items-center justify-end gap-2 p-4 pt-0 rounded-b-xl',
                                'bg-primary-100/30 dark:bg-primary-900/10 pt-4' =>
                                    $card->is_attended || $card->can_attend,
                            ])>
                                @if ($card->can_attend)
                                    <x-filament::button wire:click="attend({{ $card->id }})" color="primary"
                                        size="xs"
                                        class="rounded-lg shadow-sm font-bold uppercase tracking-tight text-[10px]">
                                        Absen Sekarang
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
            <x-filament::empty-state icon="heroicon-o-finger-print" heading="Tidak ada data yang ditemukan"
                description="Setelah jadwal Anda muncul, Anda dapat melakukan presensi di sini." iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Riwayat Presensi</x-slot>
        <x-slot name="description">Berikut adalah riwayat kehadiran Anda pada sesi perkuliahan.</x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
