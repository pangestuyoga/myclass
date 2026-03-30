<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ($this->statCards as $stat)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">

                <div class="flex items-center gap-2 mb-1">

                    <div class="flex h-7 w-7 items-center justify-center rounded-lg {{ $stat['color_classes'] }}">
                        <x-filament::icon :icon="$stat['icon']" class="h-4 w-4 text-current" />
                    </div>

                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $stat['label'] }}
                    </span>
                </div>

                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $stat['value'] }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
        <div class="h-2 rounded-full transition-all {{ $this->progress_color_class }}"
            style="width: {{ $this->percentage }}%"></div>
    </div>

    <x-filament::section 
        icon="{{ \App\Filament\Support\SystemNotification::getNotifStyle() === \App\Enums\NotifStyle::Cheerful ? 'heroicon-o-document-magnifying-glass' : 'heroicon-o-document-text' }}" 
        icon-color="primary"
    >
        <x-slot name="heading">{{ $this->assignmentSummary['title'] }}</x-slot>
        <x-slot name="description">
            {{ $this->assignmentSummary['course'] }}
            &nbsp;·&nbsp;
            Batas: {{ $this->assignmentSummary['due_date'] }}
            &nbsp;·&nbsp;
            Tipe: {{ $this->assignmentSummary['type'] }}
            @if ($this->assignmentSummary['is_overdue'])
                <x-filament::badge color="danger" class="ml-2">Terlewat</x-filament::badge>
            @endif
        </x-slot>

        @if ($this->submissionSummary->isEmpty())
            <div class="flex flex-col items-center py-8 text-center">
                <x-filament::icon icon="heroicon-o-user-group" class="w-10 h-10 text-gray-400 mb-3" />
                <p class="text-gray-500 dark:text-gray-400">Tidak ada
                    {{ $this->record->type === \App\Enums\AssignmentType::Individual ? 'mahasiswa' : 'kelompok' }} yang
                    ditarget.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">No</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                                {{ $this->record->type === \App\Enums\AssignmentType::Individual ? 'Mahasiswa' : 'Kelompok' }}
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                                {{ $this->record->type === \App\Enums\AssignmentType::Individual ? 'NIM' : 'Pengumpul' }}
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Waktu Kumpul
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">File</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach ($this->submissionSummary as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 tabular-nums">
                                    {{ $loop->iteration }}</td>

                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $item->primary_name }}
                                </td>

                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                    {{ $item->secondary_info }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="{{ $item->status_classes }}">
                                        {{ $item->status_label }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $item->submitted_at_formatted }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($item->has_file)
                                        <button type="button"
                                            wire:click="mountAction('previewSubmission', { submissionId: {{ $item->submission_id }} })"
                                            class="inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline">
                                            <x-filament::icon icon="heroicon-o-eye" class="h-3.5 w-3.5" />
                                            Lihat Tugas
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
