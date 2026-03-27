<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: History -->
            <div class="lg:col-span-2 space-y-6">
                <x-filament::section>
                    <x-slot name="heading">Riwayat Pertemuan</x-slot>
                    <x-slot name="description">Rekapitulasi presensi tiap pertemuan.</x-slot>

                    <div class="space-y-4">
                        @forelse ($meetingHistory as $meeting)
                            <div
                                class="fi-card flex items-center justify-between p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm transition duration-200 hover:shadow-md hover:border-primary-500">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex flex-col items-center justify-center border border-gray-100 dark:border-gray-700">
                                        <span
                                            class="text-[10px] font-bold uppercase text-gray-400">{{ $meeting->date['month'] }}</span>
                                        <span
                                            class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ $meeting->date['day'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-gray-950 dark:text-white">
                                            {{ $meeting->formatted_date }}</h4>
                                        <p
                                            class="text-[11px] font-medium text-gray-500 uppercase tracking-tight mt-0.5">
                                            HADIR: <span
                                                class="text-primary-600 dark:text-primary-400">{{ $meeting->attended_count }}
                                                / {{ $meeting->total_students }}</span>
                                        </p>
                                    </div>
                                </div>

                                <x-filament::button
                                    x-on:click="window.navigator.clipboard.writeText('{{ $meeting->share_url }}'); new FilamentNotification().title('Tautan Berhasil Disalin ✨').body('Tautan presensi telah berhasil disalin ke papan klip.').success().send()"
                                    color="gray" size="xs" icon="heroicon-m-share"
                                    class="rounded-lg shadow-sm font-bold uppercase tracking-tight text-[10px]">
                                    Salin Link
                                </x-filament::button>
                            </div>
                        @empty
                            <x-filament::empty-state icon="heroicon-o-calendar" heading="Belum Ada Riwayat Pertemuan"
                                description="Riwayat pertemuan untuk mata kuliah ini belum tersedia. Data presensi akan muncul setelah sesi perkuliahan dilakukan."
                                iconColor="gray">
                            </x-filament::empty-state>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>

            <!-- Right: Ongoing Stats -->
            <div class="space-y-6">
                @if ($activeMeetingStats)
                    <div
                        class="fi-card flex flex-col justify-between rounded-xl border transition duration-200 relative bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500">
                        <div class="p-6 space-y-4">
                            <div class="space-y-1">
                                <h3
                                    class="text-lg font-bold leading-tight flex items-center gap-2 text-primary-900 dark:text-primary-100">
                                    <x-heroicon-o-presentation-chart-line class="w-5 h-5 shrink-0 text-primary-500" />
                                    Monitoring Sesi Ini
                                </h3>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-primary-200 dark:border-primary-800">
                                <div
                                    class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-primary-700 dark:text-primary-300">
                                    <span>WAKTU PERKULIAHAN</span>
                                    <span
                                        class="font-mono bg-primary-100 dark:bg-primary-800 px-2 py-0.5 rounded text-primary-600 dark:text-primary-400">{{ $activeMeetingStats->time_range }}</span>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-end">
                                        <div
                                            class="text-3xl font-black text-primary-950 dark:text-white tracking-tight">
                                            {{ $activeMeetingStats->attended_count }} <span
                                                class="text-sm font-normal text-primary-600/70 italic">/
                                                {{ $activeMeetingStats->total_students }}</span>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-100 dark:bg-primary-800 px-2 py-1 rounded-lg">
                                            {{ $activeMeetingStats->percentage }}%
                                        </span>
                                    </div>
                                    <div
                                        class="w-full h-3 bg-white/50 dark:bg-gray-800/50 rounded-full overflow-hidden border border-primary-200 dark:border-primary-700 p-0.5">
                                        <div class="h-full bg-primary-500 rounded-full transition-all duration-1000 shadow-[0_0_12px_rgba(var(--primary-500),0.4)]"
                                            style="width: {{ $activeMeetingStats->percentage }}%">
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-primary-200 dark:border-primary-800">
                                    <h5
                                        class="text-[10px] font-bold text-primary-700 dark:text-primary-300 uppercase tracking-widest mb-3 flex items-center justify-between">
                                        <span>Belum Melakukan Absen</span>
                                        <span
                                            class="bg-warning-500 text-white px-2 py-0.5 rounded text-[10px] shadow-sm font-bold">{{ $activeMeetingStats->total_students - $activeMeetingStats->attended_count }}</span>
                                    </h5>
                                    <div class="max-h-[350px] overflow-y-auto space-y-2 thin-scrollbar pr-1">
                                        @foreach ($activeMeetingStats->absent_students as $absent)
                                            <div
                                                class="p-3 rounded-xl bg-white/60 dark:bg-gray-900/60 border border-primary-200/50 dark:border-primary-800/50 text-xs shadow-sm group hover:border-warning-500 transition">
                                                <div class="font-bold text-gray-950 dark:text-white truncate">
                                                    {{ $absent->full_name }}</div>
                                                <div class="text-[10px] text-gray-500 font-mono mt-0.5">
                                                    {{ $absent->nim }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <x-filament::empty-state icon="heroicon-o-clock" heading="Tidak Ada Sesi Perkuliahan Aktif"
                        description="Saat ini belum ada sesi perkuliahan yang berlangsung. Silakan lihat daftar riwayat pertemuan di sebelah kiri."
                        iconColor="gray">
                    </x-filament::empty-state>
                @endif
            </div>
        </div>
    </div>

    <style>
        .thin-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .thin-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .thin-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .dark .thin-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
</x-filament-panels::page>
