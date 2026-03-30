<div class="space-y-3">
    @forelse ($attendances as $attendance)
        @if ($loop->first)
            <div
                class="fi-ta-content overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                <table class="w-full text-sm text-left divider-y dark:divider-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 font-bold text-gray-900 dark:text-white">Mahasiswa</th>
                            <th class="px-4 py-3 font-bold text-gray-900 dark:text-white text-right">Waktu Presensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
        @endif
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-gray-950 dark:text-white font-medium">
                                <div class="flex flex-col">
                                    <span>{{ $attendance->student->full_name }}</span>
                                    <span
                                        class="text-[10px] text-gray-500 dark:text-gray-400 font-normal uppercase tabular-nums">NIM:
                                        {{ $attendance->student->student_number }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span
                                    class="inline-flex items-center rounded-md bg-success-50 dark:bg-success-400/10 px-2 py-1 text-xs font-bold text-success-700 dark:text-success-400 ring-1 ring-inset ring-success-600/20 tabular-nums">
                                    {{ $attendance->attended_at->format('H:i') }} WIB
                                </span>
                            </td>
                        </tr>
        @if ($loop->last)
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex items-center justify-between px-2">
                <p class="text-xs text-gray-500 font-medium">
                    Total Kehadiran: <span
                        class="text-primary-600 dark:text-primary-400 font-bold font-mono">{{ $loop->count }}
                        Mahasiswa</span>
                </p>
            </div>
        @endif
    @empty
        <x-filament::empty-state icon="heroicon-o-user-group" heading="Tidak ada data yang ditemukan"
            description="Belum ada mahasiswa yang melakukan presensi pada sesi ini." iconColor="gray">
        </x-filament::empty-state>
    @endforelse
</div>
