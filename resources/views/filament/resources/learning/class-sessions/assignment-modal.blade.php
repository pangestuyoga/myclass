<div class="space-y-4 py-2">
    <a href="{{ $assignment->url }}"
        class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 shadow-sm hover:bg-white dark:hover:bg-white/10 transition-colors group">
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
        <x-heroicon-o-chevron-right
            class="w-5 h-5 text-gray-400 group-hover:text-primary-500 transition-colors shrink-0" />
    </a>

    <div
        class="fi-ta-content overflow-x-auto overflow-y-auto max-h-150 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
        <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/5">
            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-2.5 font-bold text-gray-900 dark:text-white">Mahasiswa</th>
                    <th class="px-4 py-2.5 font-bold text-gray-900 dark:text-white text-center">Status</th>
                    <th class="px-4 py-2.5 font-bold text-gray-900 dark:text-white text-right">Waktu Unggah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                @foreach ($assignment->submissions as $submission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-4 py-2.5 text-gray-950 dark:text-white font-medium">
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $submission->student_name }}</span>
                                <span
                                    class="text-[10px] text-gray-500 dark:text-gray-400 font-normal uppercase tabular-nums">
                                    NIM: {{ $submission->student_number }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if ($submission->is_submitted)
                                <span
                                    class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-400/10 px-2 py-0.5 text-xs font-bold text-success-700 dark:text-success-400 ring-1 ring-inset ring-success-600/20">
                                    Sudah Unggah
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-400/10 px-2 py-0.5 text-xs font-bold text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/20">
                                    Belum Unggah
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right tabular-nums">
                            @if ($submission->is_submitted)
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ $submission->submitted_at_formatted }}
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-600 italic">
                                    -
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between px-2">
        <p class="text-xs text-gray-500 font-medium">
            Total Mahasiswa: <span
                class="text-primary-600 dark:text-primary-400 font-bold font-mono">{{ $assignment->total_students }}</span>
            <span class="mx-2 text-gray-300">|</span>
            Sudah Mengumpulkan: <span
                class="text-success-600 dark:text-success-400 font-bold font-mono">{{ $assignment->submission_count }}</span>
        </p>
    </div>
</div>
