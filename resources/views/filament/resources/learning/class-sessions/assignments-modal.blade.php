<div class="space-y-6 py-2">
    @forelse($assignments as $assignment)
        <div class="space-y-3">
            <div
                class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="font-bold text-gray-900 dark:text-white leading-tight uppercase font-mono text-[11px] mb-0.5 tracking-tight text-primary-600/60 dark:text-primary-400/50 block">
                            {{ $assignment->type_label }}
                        </span>
                        <span class="font-bold text-gray-900 dark:text-white leading-tight">{{ $assignment->title }}</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-tight">
                                Deadline: {{ $assignment->due_date_formatted }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($assignment->submissions->count() > 0)
                <div
                    class="fi-ta-content overflow-x-auto overflow-y-auto max-h-60 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
                    <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2.5 font-bold text-gray-900 dark:text-white">Mahasiswa</th>
                                <th class="px-4 py-2.5 font-bold text-gray-900 dark:text-white text-right">Waktu Unggah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-2.5 text-gray-950 dark:text-white font-medium">
                                        <div class="flex flex-col">
                                            <span class="text-sm">{{ $submission->student_name }}</span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-normal uppercase tabular-nums">
                                                NIM: {{ $submission->student_number }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <span
                                            class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-400/10 px-2 py-0.5 text-xs font-bold text-success-700 dark:text-success-400 ring-1 ring-inset ring-success-600/20 tabular-nums">
                                            {{ $submission->submitted_at_formatted }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-2">
                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">
                        Total Pengumpulan: <span class="text-primary-600 dark:text-primary-400 font-bold font-mono">{{ $assignment->submissions->count() }}
                            Mahasiswa</span>
                    </p>
                </div>
            @else
                <div class="flex flex-col items-center justify-center p-6 border border-dashed rounded-xl border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                    <x-heroicon-o-document-minus class="w-8 h-8 text-gray-400 dark:text-gray-600 mb-2" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Belum ada pengumpulan untuk tugas ini.</p>
                </div>
            @endif
        </div>
    @empty
        <x-filament::empty-state icon="heroicon-o-clipboard-document" heading="Tidak ada data yang ditemukan"
            description="Belum ada tugas untuk sesi ini." iconColor="gray">
        </x-filament::empty-state>
    @endforelse
</div>

