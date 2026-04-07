<x-filament-panels::page>
    <x-filament::section>
        <div class="space-y-4">
            @forelse ($this->sessions as $session)
                <div
                    class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition flex flex-col">

                    <div class="flex flex-1 items-center gap-4 p-4 sm:p-5 min-w-0">
                        <div
                            class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-300 font-bold border border-primary-200 dark:border-primary-500/20 shrink-0">
                            <span class="text-[10px] uppercase leading-none opacity-60 mb-0.5 mt-1">Sesi</span>
                            <span
                                class="text-sm leading-none mb-1 text-primary-600 dark:text-primary-400 font-bold">Ke-{{ $session->session_number }}</span>
                        </div>

                        <div class="flex flex-1 flex-wrap items-center justify-between gap-x-6 gap-y-2 min-w-0">
                            <div class="flex flex-col gap-1.5 min-w-0">
                                <div
                                    class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
                                        <x-heroicon-o-calendar class="w-4 h-4 text-primary-500 shrink-0" />
                                        {{ $session->date_formatted }}
                                    </div>
                                    <div class="flex items-center gap-1.5 font-medium">
                                        <x-heroicon-o-clock class="w-4 h-4 text-primary-500 shrink-0" />
                                        {{ $session->time_range }}
                                    </div>
                                </div>

                                 <div class="flex items-center gap-2">
                                    <div
                                        class="flex items-center gap-1.5 text-xs font-bold text-success-600 dark:text-success-400">
                                        <x-heroicon-o-user-group class="w-4 h-4 shrink-0" />
                                        {{ $session->attendances_count }}<span
                                            class="text-[10px] opacity-60">/{{ $session->total_students }}</span>
                                    </div>
                                    <div class="w-16 h-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-success-500 rounded-full transition-all duration-500"
                                            style="width: {{ $session->attendance_percentage }}%"></div>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold text-gray-500">{{ $session->attendance_percentage }}%</span>
                                    
                                    @if ($session->is_sent_to_lecturer)
                                        <span class="inline-flex items-center rounded-md bg-danger-50 dark:bg-danger-500/10 px-1.5 py-0.5 text-[10px] font-bold text-danger-700 dark:text-danger-400 ring-1 ring-inset ring-danger-600/20">
                                            Terkirim
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($session->materials_count || $session->assignments_count)
                                <div class="flex items-center gap-2 shrink-0">
                                    @if ($session->materials_count)
                                        {{ ($this->viewMaterialsAction)(['session' => $session->id]) }}
                                    @endif
                                    @if ($session->assignments_count)
                                        {{ ($this->viewAssignmentsAction)(['session' => $session->id]) }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-3 px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                        {{ ($this->viewAttendanceAction)(['session' => $session->id]) }}
                        {{ ($this->shareAttendanceAction)(['session' => $session->id]) }}
                        {{ ($this->markAsSentAction)(['record' => $session->id]) }}
                        {{ ($this->editSessionAction)(['session' => $session->id]) }}
                        {{ ($this->deleteSessionAction)(['session' => $session->id]) }}
                    </div>
                </div>
            @empty
                <x-filament::empty-state icon="{{ $this->emptyStateIcon }}"
                    heading="{{ $this->emptyStateHeading }}"
                    description="{{ $this->emptyStateDescription }}"
                    iconColor="gray">
                </x-filament::empty-state>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
