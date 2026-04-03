<div class="space-y-3">
    @foreach ($students as $item)
        @if ($loop->first)
            <div
                class="fi-ta-content overflow-x-auto overflow-y-auto max-h-150 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <table class="w-full text-sm text-left divider-y dark:divider-white/5 sticky-header-table">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 font-bold text-gray-900 dark:text-white">Mahasiswa</th>
                            <th class="px-4 py-3 font-bold text-gray-900 dark:text-white text-center">Status</th>
                            <th class="px-4 py-3 font-bold text-gray-900 dark:text-white text-right">Waktu Presensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
        @endif
        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
            <td class="px-4 py-3 text-gray-950 dark:text-white font-medium">
                <div class="flex flex-col">
                    <span>{{ $item->student->full_name }}</span>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-normal uppercase tabular-nums">NIM:
                        {{ $item->student->student_number }}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                @if ($item->has_attended)
                    <span
                        class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-400/10 px-2 py-1 text-xs font-bold text-success-700 dark:text-success-400 ring-1 ring-inset ring-success-600/20">
                        Hadir
                    </span>
                @else
                    <span
                        class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-400/10 px-2 py-1 text-xs font-bold text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/20">
                        Belum Hadir
                    </span>
                @endif
            </td>
            <td class="px-4 py-3 text-right tabular-nums">
                @if ($item->has_attended)
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ $item->attended_at }}
                    </span>
                @else
                    <span class="text-gray-400 dark:text-gray-600 italic">
                        -
                    </span>
                @endif
            </td>
        </tr>
        @if ($loop->last)
            </tbody>
            </table>
</div>
<div class="mt-4 flex items-center justify-between px-2">
    <p class="text-xs text-gray-500 font-medium">
        Total Mahasiswa: <span
            class="text-primary-600 dark:text-primary-400 font-bold font-mono">{{ $loop->count }}</span>
        <span class="mx-2 text-gray-300">|</span>
        Sudah Hadir: <span
            class="text-success-600 dark:text-success-400 font-bold font-mono">{{ $attendedCount }}</span>
    </p>
</div>
@endif
@endforeach
</div>
