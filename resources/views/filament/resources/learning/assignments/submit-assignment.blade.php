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

            @if ($this->record->hasMedia('assignments'))
                @php
                    $attachmentUrl = $this->record->getFirstMediaUrl('assignments');
                    $googleViewerUrl =
                        'https://docs.google.com/viewer?url=' . urlencode($attachmentUrl) . '&embedded=true';
                @endphp
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Lampiran Tugas</p>
                    <div
                        class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <iframe src="{{ $this->record->getFirstMediaUrl('assignments') }}" width="100%" height="500"
                            class="block border-0"></iframe>
                    </div>
                </div>
            @endif
        </x-filament::section>

        @if ($this->isOverdue)
            <x-filament::section
                icon="{{ \App\Filament\Support\SystemNotification::getNotifStyle() === \App\Enums\NotifStyle::Cheerful ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-lock-closed' }}"
                icon-color="danger">
                <x-slot
                    name="heading">{{ \App\Filament\Support\SystemNotification::getMessage('Yah, Batas Waktu Udah Habis! 😭⏳', 'Batas Waktu Telah Berakhir') }}</x-slot>
                <x-slot
                    name="description">{{ \App\Filament\Support\SystemNotification::getMessage('Maaf ya, kamu udah nggak bisa kumpulin tugas ini lagi karena waktunya udah lewat. Tetap semangat buat tugas selanjutnya! 💪', 'Proses pengumpulan tugas ini telah ditutup karena melewati batas waktu.') }}</x-slot>

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
                    <p class="font-semibold text-gray-700 dark:text-gray-300">Tugas Kelompok</p>
                    <p class="text-sm mt-1 max-w-sm">
                        Hanya <strong>Ketua Kelompok</strong> yang dapat mengumpulkan atau memperbarui tugas ini.
                        Silakan hubungi ketua kelompok Anda.
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
            <x-filament::section
                icon="{{ \App\Filament\Support\SystemNotification::getNotifStyle() === \App\Enums\NotifStyle::Cheerful ? 'heroicon-o-paper-airplane' : 'heroicon-o-document-arrow-up' }}"
                icon-color="primary">
                <x-slot
                    name="heading">{{ $this->isResubmit ? \App\Filament\Support\SystemNotification::getMessage('Kuy Perbarui Tugasmu! 🔄✨', 'Perbarui Pengumpulan') : \App\Filament\Support\SystemNotification::getMessage('Kumpulkan Tugas Sekarang! 🚀📚', 'Kumpulkan Tugas') }}</x-slot>

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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ $this->isResubmit ? 'Ganti File PDF (opsional)' : 'File Tugas' }}
                            @if (!$this->isResubmit)
                                <span class="text-danger-500">*</span>
                            @endif
                        </label>
                        <input wire:model="file" type="file" id="assignment-file" accept=".pdf"
                            class="block w-full text-sm text-gray-700 dark:text-gray-300
                    file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                    file:text-sm file:font-medium file:cursor-pointer
                    file:bg-primary-50 file:text-primary-700
                    dark:file:bg-primary-900/20 dark:file:text-primary-400
                    hover:file:bg-primary-100 dark:hover:file:bg-primary-900/30
                    border border-gray-300 dark:border-gray-600 rounded-lg
                    bg-white dark:bg-gray-800 p-2 transition-colors" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Hanya file PDF. Maksimal 5MB.
                        </p>
                        @error('file')
                            <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">
                                {{ $message }}</p>
                        @enderror
                    </div>

                    <div id="pdf-submission-preview" class="hidden" wire:ignore>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Preview</p>
                        <div
                            class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <iframe id="pdf-submission-frame" width="100%" height="400"
                                class="block border-0"></iframe>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-filament::button wire:click="submit" wire:loading.attr="disabled" color="primary">
                        <span wire:loading.remove wire:target="submit">
                            {{ $this->isResubmit ? 'Perbarui Pengumpulan' : 'Kumpulkan Sekarang' }}
                        </span>
                        <span wire:loading wire:target="submit">Mengupload...</span>
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to reset preview
            function resetPreview() {
                var preview = document.getElementById('pdf-submission-preview');
                var frame = document.getElementById('pdf-submission-frame');
                if (preview) preview.classList.add('hidden');
                if (frame) frame.src = '';
            }

            // Delegation for change event
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'assignment-file') {
                    var file = e.target.files[0];
                    var preview = document.getElementById('pdf-submission-preview');
                    var frame = document.getElementById('pdf-submission-frame');

                    if (file && preview && frame) {
                        frame.src = URL.createObjectURL(file);
                        preview.classList.remove('hidden');
                    } else {
                        resetPreview();
                    }
                }
            });

            // Listen for the Livewire event after submission
            window.addEventListener('submission-completed', function() {
                var input = document.getElementById('assignment-file');
                if (input) input.value = '';
                resetPreview();
            });
        });
    </script>
@endpush
