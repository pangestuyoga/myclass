<x-filament-panels::page>
    <div class="space-y-5">

        <x-filament::section>
            <x-slot name="heading">{{ $this->record->title }}</x-slot>
            <x-slot name="description">{{ $this->record->course?->name ?? '-' }}</x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                @foreach ($this->statusCards as $card)
                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                        <div class="{{ $card['icon_classes'] }}">
                            <x-filament::icon :icon="$card['icon']" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                            <p
                                class="font-semibold text-sm {{ $card['is_danger'] ?? false ? 'text-danger-600 dark:text-danger-400' : 'text-gray-900 dark:text-white' }}">
                                {{ $card['value'] }}
                                @if ($card['badge'] ?? null)
                                    <span class="text-xs font-normal">{{ $card['badge'] }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($this->record->description)
                <div
                    class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-4">
                    {!! $this->record->description !!}
                </div>
            @endif

            @if ($attachmentUrl = $this->record->getFirstMediaUrl('assignments'))
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Lampiran Tugas</p>
                    <div
                        class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <iframe src="{{ $attachmentUrl }}" width="100%" height="500"
                            class="block border-0"></iframe>
                    </div>
                </div>
            @endif
        </x-filament::section>

        @if ($this->isOverdue)
            <x-filament::section :icon="$this->overdueIcon" icon-color="danger">
                <x-slot name="heading">{{ $this->overdueHeading }}</x-slot>
                <x-slot name="description">{{ $this->overdueDescription }}</x-slot>

                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-50 dark:bg-danger-900/20 text-danger-600">
                        <x-filament::icon icon="heroicon-o-lock-closed" class="h-5 w-5" />
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-filament::badge :color="$this->submissionStatus->badge_color" size="lg">
                            {{ $this->submissionStatus->badge_label }}
                        </x-filament::badge>
                    </div>
                </div>

                @if ($url = $this->getSubmissionFileUrl())
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">File yang Dikumpulkan</p>
                        <div
                            class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <iframe src="{{ $url }}" width="100%" height="500"
                                class="block border-0"></iframe>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @elseif (!$this->canSubmit)
            <x-filament::section>
                <div
                    class="flex flex-col items-center justify-center py-8 text-center text-gray-500 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-o-user-group" class="w-12 h-12 mb-3" />
                    <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $this->groupHintTitle }}</p>
                    <p class="text-sm mt-1 max-w-sm">
                        {!! $this->groupHintDescription !!}
                    </p>
                </div>

                @if ($url = $this->getSubmissionFileUrl())
                    <div class="mt-6">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 italic">File yang telah
                            dikumpulkan oleh kelompok Anda:</p>
                        <div
                            class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <iframe src="{{ $url }}" width="100%" height="400"
                                class="block border-0"></iframe>
                        </div>
                    </div>
                @endif
            </x-filament::section>
        @else
            <x-filament::section :icon="$this->submissionIcon" icon-color="primary">
                <x-slot name="heading">{{ $this->submissionHeading }}</x-slot>
                <x-slot name="description">{{ $this->submissionDescription }}</x-slot>

                @if ($this->isResubmit && ($url = $this->getSubmissionFileUrl()))
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">File Terkumpul Saat Ini</p>
                        <div
                            class="rounded-lg overflow-hidden border border-success-200 dark:border-success-800 bg-gray-50 dark:bg-gray-900">
                            <iframe src="{{ $url }}" width="100%" height="400"
                                class="block border-0"></iframe>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    {{ $this->form }}
                </div>
            </x-filament::section>

            <div class="flex justify-start">
                <x-filament::button wire:click="submit" wire:loading.attr="disabled" color="primary">
                    <span wire:loading.remove wire:target="submit">
                        {{ $this->isResubmit ? 'Perbarui' : 'Kumpulkan' }}
                    </span>
                    <span wire:loading wire:target="submit">Mengupload...</span>
                </x-filament::button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
