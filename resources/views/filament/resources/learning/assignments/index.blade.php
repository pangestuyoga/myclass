<x-filament-panels::page>
    <x-filament::section 
        icon="{{ \App\Filament\Support\SystemNotification::getNotifStyle() === \App\Enums\NotifStyle::Cheerful ? 'heroicon-o-pencil-square' : 'heroicon-o-briefcase' }}" 
        icon-color="primary"
    >
        <x-slot name="heading">{{ \App\Filament\Support\SystemNotification::getMessage('Daftar Tugas Bikin Lemes! 🤣📒', 'Tugas Saya') }}</x-slot>
        <x-slot name="description">{{ \App\Filament\Support\SystemNotification::getMessage('Yuk, cek dan kumpulin tugasmu biar tenang hidupnya! Klik aja di tugasnya ya. 🚀👨‍💻', 'Klik pada tugas untuk melihat detail dan mengumpulkan file.') }}</x-slot>

        <div class="space-y-4">
            @forelse ($this->assignmentCards as $card)

                <div class="{{ $card->card_classes }} flex-col sm:flex-row">


                    <a href="{{ \App\Filament\Resources\Learning\Assignments\AssignmentResource::getUrl('submit', ['record' => $card->id]) }}"
                        class="flex flex-1 items-start gap-3 p-4 sm:p-5 text-left min-w-0 hover:bg-primary-500/5 transition-colors cursor-pointer">


                        <div class="{{ $card->icon_wrapper_classes }} mt-0.5 shrink-0">
                            <x-filament::icon :icon="$card->status_icon" class="h-5 w-5 sm:h-6 sm:w-6" />
                        </div>

                        <div class="flex-1 min-w-0">

                            <p class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base leading-snug">
                                {{ $card->title }}
                            </p>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                {{ $card->course_name }}
                            </p>


                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                @if ($card->is_pinned)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 px-2 py-0.5 text-xs font-semibold whitespace-nowrap">
                                        📌 Dipinned
                                    </span>
                                @endif
                                @if ($card->is_new)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400 px-2 py-0.5 text-xs font-semibold whitespace-nowrap">
                                        ✨ Baru
                                    </span>
                                @endif
                                @if ($card->is_group)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-0.5 text-xs font-semibold whitespace-nowrap">
                                        👥 Kelompok
                                    </span>
                                @endif

                                <span class="{{ $card->status_badge_classes }} whitespace-nowrap">
                                    @if ($card->status_icon)
                                        <x-filament::icon :icon="$card->status_icon" class="h-3 w-3" />
                                    @endif
                                    {{ $card->status_label }}
                                </span>

                                @if ($card->is_urgent)
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-danger-500 text-white px-2 py-0.5 text-xs font-semibold animate-pulse whitespace-nowrap">
                                        🔥 Segera!
                                    </span>
                                @endif
                            </div>


                            <div class="flex flex-wrap gap-x-3 gap-y-1 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <x-filament::icon icon="heroicon-o-clock" class="h-3.5 w-3.5 shrink-0" />
                                    <span>Batas: {{ $card->due_date_formatted }}</span>
                                </span>
                                @if ($card->submitted_at_formatted)
                                    <span class="flex items-center gap-1 text-success-600 dark:text-success-400">
                                        <x-filament::icon icon="heroicon-o-arrow-up-tray"
                                            class="h-3.5 w-3.5 shrink-0" />
                                        <span>Dikumpulkan: {{ $card->submitted_at_formatted }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($card->can_submit_actual)
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                class="h-4 w-4 text-gray-400 group-hover:text-primary-500 mt-1 transition-colors shrink-0 hidden sm:block" />
                        @endif
                    </a>


                    <div
                        class="flex items-center justify-start gap-2 px-4 py-2 border-t border-gray-100 dark:border-gray-700 sm:flex-col sm:items-start sm:justify-center sm:border-t-0 sm:border-l sm:px-2 sm:py-3 sm:shrink-0">
                        {{ ($this->pinAction)(['record' => $card->id]) }}

                        @canAny(['Update:Assignment', 'Delete:Assignment'])
                            {{ ($this->editAssignmentAction)(['record' => $card->id]) }}
                            {{ ($this->deleteAssignmentAction)(['record' => $card->id]) }}
                        @endcanAny
                    </div>


                    @if ($card->is_group && !$card->is_submitted)
                        <div class="absolute right-14 top-2 pointer-events-none hidden sm:block">
                            <x-filament::icon
                                icon="{{ $card->is_leader ? 'heroicon-m-sparkles' : 'heroicon-m-user-group' }}"
                                class="{{ $card->indicator_icon_classes }}"
                                title="{{ $card->is_leader ? 'Anda adalah Ketua Kelompok' : 'Anda adalah Anggota Kelompok' }}" />
                        </div>
                    @endif
                </div>
            @empty
                <x-filament::empty-state icon="heroicon-o-clipboard-document-list"
                    heading="Tidak ada data yang ditemukan"
                    description="Belum ada tugas baru yang perlu dikerjakan untuk saat ini. Tetap semangat!"
                    iconColor="gray">
                </x-filament::empty-state>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
