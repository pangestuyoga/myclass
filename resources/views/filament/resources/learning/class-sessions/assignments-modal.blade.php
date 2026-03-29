<div class="space-y-4 py-2">
    @forelse($assignments as $assignment)
        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 transition-colors shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-primary-100 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-gray-900 dark:text-white leading-tight uppercase font-mono text-[11px] mb-0.5 tracking-tight text-primary-600/60 dark:text-primary-400/50 block">
                        {{ $assignment->type->label() ?? 'TUGAS' }}
                    </span>
                    <span class="font-bold text-gray-900 dark:text-white leading-tight">{{ $assignment->title }}</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase tracking-tight">
                            Deadline: {{ $assignment->due_date?->translatedFormat('d F Y, H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ \App\Filament\Resources\Learning\Assignments\AssignmentResource::getUrl('index') . '?tableFilters[course_id][value]=' . $assignment->course_id . '&tableSearch=' . urlencode($assignment->title) }}" 
                   class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-white/10 text-gray-500 transition-colors"
                   title="Lihat Detail">
                    <x-heroicon-o-chevron-right class="w-5 h-5" />
                </a>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-200 dark:border-white/10">
            <x-heroicon-o-pencil-square class="w-12 h-12 text-gray-300 dark:text-gray-600" />
            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada tugas untuk sesi ini.</p>
        </div>
    @endforelse
</div>
