<x-filament-panels::page>
    <div class="space-y-5">

        <x-filament::section>
            <x-slot name="heading">{{ $this->record->title }}</x-slot>
            <x-slot name="description">{{ $this->record->course?->name ?? '-' }}</x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
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
                    @if (str_ends_with(strtolower($attachmentUrl), '.pdf'))
                        <div
                            class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <iframe src="{{ $attachmentUrl }}" width="100%" height="500"
                                class="block border-0"></iframe>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 group hover:border-primary-500 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600">
                                    <x-filament::icon icon="heroicon-o-document-arrow-down" class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->record->getFirstMedia('assignments')?->file_name ?? 'Berkas Lampiran' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Klik tombol di samping untuk mengunduh berkas.</p>
                                </div>
                            </div>
                            <x-filament::button type="button" wire:click="downloadAttachment" color="gray" size="sm" icon="heroicon-o-arrow-down-tray">
                                Unduh
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            @endif
        </x-filament::section>

        {{-- Kondisi Form: Muncul jika BELUM dikirim ke dosen --}}
        @if ($this->record->is_sent_to_lecturer)
            <x-filament::section :icon="$this->overdueIcon" icon-color="danger">
                <x-slot name="heading">Penerimaan Tugas Ditutup</x-slot>
                <x-slot name="description">Tugas ini sudah dikirim ke dosen oleh Kosma dan tidak dapat diubah lagi.</x-slot>

                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-50 dark:bg-danger-900/20 text-danger-600">
                        <x-filament::icon icon="heroicon-o-lock-closed" class="h-5 w-5" />
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        Tugas sudah dikirim ke dosen pada status final.
                    </div>
                </div>

                @if ($url = $this->getSubmissionFileUrl())
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">File yang Terakhir Dikumpulkan</p>
                        @if (str_ends_with(strtolower($url), '.pdf'))
                            <div
                                class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <iframe src="{{ $url }}" width="100%" height="400"
                                    class="block border-0"></iframe>
                            </div>
                        @else
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 group hover:border-primary-500 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600">
                                        <x-filament::icon icon="heroicon-o-document-arrow-down" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->existingSubmission?->getFirstMedia('submission')?->file_name ?? 'Berkas Tugas' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Berkas non-PDF (ZIP)</p>
                                    </div>
                                </div>
                                <x-filament::button type="button" wire:click="downloadSubmission" color="gray" size="sm" icon="heroicon-o-arrow-down-tray">
                                    Unduh
                                </x-filament::button>
                            </div>
                        @endif
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
                        @if (str_ends_with(strtolower($url), '.pdf'))
                            <div
                                class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <iframe src="{{ $url }}" width="100%" height="400"
                                    class="block border-0"></iframe>
                            </div>
                        @else
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600">
                                        <x-filament::icon icon="heroicon-o-document-arrow-down" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->existingSubmission?->getFirstMedia('submission')?->file_name ?? 'Berkas Tugas' }}</p>
                                    </div>
                                </div>
                                <x-filament::button type="button" wire:click="downloadSubmission" color="gray" size="sm" icon="heroicon-o-arrow-down-tray">
                                    Unduh
                                </x-filament::button>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>
        @else
            @if ($this->isOverdue)
                <div class="p-4 rounded-xl border border-warning-200 bg-warning-50 dark:bg-warning-900/10 dark:border-warning-900/20 flex gap-3">
                    <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5 text-warning-600 shrink-0" />
                    <div>
                        <p class="text-sm font-bold text-warning-800 dark:text-warning-400">Tenggat Waktu Sudah Terlewat</p>
                        <p class="text-xs text-warning-700 dark:text-warning-500 mt-1">Anda masih diperbolehkan mengumpulkan tugas karena belum dikirim ke dosen oleh Kosma.</p>
                    </div>
                </div>
            @endif

            <x-filament::section :icon="$this->submissionIcon" icon-color="primary">
                <x-slot name="heading">{{ $this->submissionHeading }}</x-slot>
                <x-slot name="description">{{ $this->submissionDescription }}</x-slot>

                @if ($this->isResubmit && ($url = $this->getSubmissionFileUrl()))
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">File Terkumpul Saat Ini</p>
                        @if (str_ends_with(strtolower($url), '.pdf'))
                            <div
                                class="rounded-lg overflow-hidden border border-success-200 dark:border-success-800 bg-gray-50 dark:bg-gray-900">
                                <iframe src="{{ $url }}" width="100%" height="400"
                                    class="block border-0"></iframe>
                            </div>
                        @else
                            <div class="flex items-center justify-between p-4 bg-success-50/50 dark:bg-success-900/10 rounded-xl border border-success-200 dark:border-success-800">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-success-100 dark:bg-success-900/30 rounded-lg text-success-600">
                                        <x-filament::icon icon="heroicon-o-check-badge" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->existingSubmission?->getFirstMedia('submission')?->file_name ?? 'Berkas Tugas' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Berkas Terkumpul (ZIP)</p>
                                    </div>
                                </div>
                                <x-filament::button type="button" wire:click="downloadSubmission" color="success" size="sm" icon="heroicon-o-arrow-down-tray" variant="outline">
                                    Unduh
                                </x-filament::button>
                            </div>
                        @endif
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
