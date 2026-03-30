<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-4">
            @forelse ($this->sessions as $session)
                <div class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition flex flex-col">

                    <div class="flex flex-1 items-center gap-4 p-4 sm:p-5 min-w-0">
                        <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-300 font-bold border border-primary-200 dark:border-primary-500/20 shrink-0">
                            <span class="text-[10px] uppercase leading-none opacity-60 mb-0.5 mt-1">Sesi</span>
                            <span class="text-sm leading-none mb-1 text-primary-600 dark:text-primary-400 font-bold">Ke-{{ $session->session_number }}</span>
                        </div>

                        <div class="flex flex-1 flex-wrap items-center justify-between gap-x-4 gap-y-2 min-w-0">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-primary-500 shrink-0" />
                                    {{ $session->date_formatted }}
                                </div>
                                <div class="flex items-center gap-1.5 font-medium">
                                    <x-heroicon-o-clock class="w-4 h-4 text-primary-500 shrink-0" />
                                    {{ $session->time_range }}
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 font-bold text-success-600 dark:text-success-400 hover:bg-success-50 dark:hover:bg-success-900/10 px-2 py-0.5 rounded-lg transition-colors cursor-pointer"
                                        wire:click="mountAction('viewAttendance', { session: {{ $session->id }} })">
                                        <x-heroicon-o-user-group class="w-4 h-4 shrink-0" />
                                        {{ $session->attendances_count }}<span class="text-[10px] opacity-60 ml-0.5">/{{ $session->total_students }}</span> Presensi
                                    </div>
                                    <div class="flex items-center gap-2 px-2">
                                        <div class="w-20 h-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-success-500 rounded-full transition-all duration-500" style="width: {{ $session->attendance_percentage }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-500">{{ $session->attendance_percentage }}%</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/10 px-2 py-0.5 rounded-lg transition-colors cursor-pointer"
                                    wire:click="mountAction('viewMaterials', { session: {{ $session->id }} })">
                                    <x-heroicon-o-document-text class="w-4 h-4 shrink-0" />
                                    {{ $session->materials_count }} Materi
                                </div>

                                <div class="flex items-center gap-1.5 font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/10 px-2 py-0.5 rounded-lg transition-colors cursor-pointer"
                                    wire:click="mountAction('viewAssignments', { session: {{ $session->id }} })">
                                    <x-heroicon-o-pencil-square class="w-4 h-4 shrink-0" />
                                    {{ $session->assignments_count }} Tugas
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="flex flex-wrap items-center gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                        {{ ($this->viewAttendanceAction)(['session' => $session->id]) }}
                        {{ ($this->viewMaterialsAction)(['session' => $session->id]) }}
                        {{ ($this->viewAssignmentsAction)(['session' => $session->id]) }}
                        {{ ($this->editSessionAction)(['session' => $session->id]) }}
                        {{ ($this->deleteSessionAction)(['session' => $session->id]) }}
                    </div>
                </div>
            @empty
                <x-filament::empty-state icon="heroicon-o-presentation-chart-bar"
                    heading="Tidak ada data yang ditemukan"
                    description="Belum ada sesi perkuliahan yang dibuat untuk mata kuliah ini."
                    iconColor="gray">
                </x-filament::empty-state>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
