<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Tugas Saya</x-slot>
        <x-slot name="description">Klik tugas untuk melihat detail dan mengumpulkan file.</x-slot>

        @if ($this->assignmentCards->isEmpty())
            <x-filament::empty-state icon="heroicon-o-clipboard-document-list" heading="Tidak ada data yang ditemukan"
                description="Setelah Anda membuat data pertama, maka akan muncul disini." iconColor="gray">
            </x-filament::empty-state>
        @else
            <div class="space-y-6">
                @foreach ($this->assignmentCards as $card)
                    <div @class([
                        'group flex w-full rounded-xl border transition-all overflow-hidden relative',
                        'border-primary-300 dark:border-primary-700 bg-primary-50/30 dark:bg-primary-900/10 shadow-sm' =>
                            $card->is_pinned,
                        'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' =>
                            !$card->is_pinned && $card->can_submit_actual,
                        'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 opacity-80' => !$card->can_submit_actual,
                    ])>
                        <a href="{{ \App\Filament\Resources\Learning\Assignments\AssignmentResource::getUrl('submit', ['record' => $card->id]) }}"
                            wire:navigate
                            class="flex flex-1 items-start gap-4 p-5 text-left min-w-0 hover:bg-primary-500/5 transition-colors cursor-pointer">
                            <div @class([
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-lg',
                                'bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400' =>
                                    $card->is_submitted,
                                'bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400' =>
                                    !$card->is_submitted && $card->is_overdue,
                                'bg-warning-50 dark:bg-warning-900/20 text-warning-600 dark:text-warning-400' =>
                                    !$card->is_submitted && !$card->is_overdue,
                            ])>
                                <x-filament::icon :icon="$card->status_icon" class="h-6 w-6" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $card->title }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $card->course_name }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if ($card->is_pinned)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 px-2.5 py-0.5 text-xs font-semibold">
                                                📌 Dipinned
                                            </span>
                                        @endif
                                        @if ($card->is_new)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400 px-2.5 py-0.5 text-xs font-semibold">
                                                ✨ Baru
                                            </span>
                                        @endif

                                        @if ($card->is_group)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2.5 py-0.5 text-xs font-semibold">
                                                👥 Kelompok
                                            </span>
                                        @endif

                                        <span @class([
                                            'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' =>
                                                $card->status_color === 'amber',
                                            'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' =>
                                                $card->status_color === 'success',
                                            'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400' =>
                                                $card->status_color === 'danger',
                                            'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' =>
                                                $card->status_color === 'warning',
                                            'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' =>
                                                $card->status_color === 'gray',
                                        ])>
                                            @if ($card->status_icon)
                                                <x-filament::icon :icon="$card->status_icon" class="h-3 w-3" />
                                            @endif
                                            {{ $card->status_label }}
                                        </span>

                                        @if ($card->is_urgent)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-danger-500 text-white px-2.5 py-0.5 text-xs font-semibold animate-pulse">
                                                🔥 Segera!
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <x-filament::icon icon="heroicon-o-clock" class="h-3.5 w-3.5" />
                                        Batas: {{ $card->due_date_formatted }}
                                    </span>
                                    @if ($card->submitted_at_formatted)
                                        <span class="flex items-center gap-1 text-success-600 dark:text-success-400">
                                            <x-filament::icon icon="heroicon-o-arrow-up-tray" class="h-3.5 w-3.5" />
                                            Dikumpulkan:
                                            {{ $card->submitted_at_formatted }}
                                        </span>
                                    @endif

                                </div>


                            </div>

                            @if ($card->can_submit_actual)
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                    class="h-4 w-4 text-gray-400 group-hover:text-primary-500 mt-1 transition-colors shrink-0" />
                            @endif
                        </a>

                        <div
                            class="flex flex-col items-center justify-center border-l border-gray-100 dark:border-gray-700 p-1 gap-3 p-3">
                            {{ ($this->pinAction)(['record' => $card->id]) }}

                            @canAny(['Update:Assignment', 'Delete:Assignment'])
                                {{ ($this->editAssignmentAction)(['record' => $card->id]) }}

                                {{ ($this->deleteAssignmentAction)(['record' => $card->id]) }}
                            @endcanAny
                        </div>

                        @if ($card->is_group && !$card->is_submitted)
                            <div class="absolute right-12 top-2 pointer-events-none">
                                <x-filament::icon
                                    icon="{{ $card->is_leader ? 'heroicon-m-sparkles' : 'heroicon-m-user-group' }}"
                                    @class([
                                        'h-5 w-5 opacity-20',
                                        'text-primary-500' => $card->is_leader,
                                        'text-gray-400' => !$card->is_leader,
                                    ])
                                    title="{{ $card->is_leader ? 'Anda adalah Ketua Kelompok' : 'Anda adalah Anggota Kelompok' }}" />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
