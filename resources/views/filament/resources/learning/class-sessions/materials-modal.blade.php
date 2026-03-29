<div class="space-y-4 py-2">
    @forelse($materials as $material)
        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 transition-colors shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-gray-900 dark:text-white leading-tight">{{ $material->title }}</span>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium uppercase mt-0.5">
                        Dibuat: {{ $material->created_at?->translatedFormat('d F Y') ?? '-' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ \App\Filament\Resources\Learning\Materials\MaterialResource::getUrl('index') . '?tableFilters[course_id][value]=' . $material->course_id . '&tableSearch=' . urlencode($material->title) }}" 
                   class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-white/10 text-gray-500 transition-colors"
                   title="Lihat Detail">
                    <x-heroicon-o-chevron-right class="w-5 h-5" />
                </a>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-200 dark:border-white/10">
            <x-heroicon-o-document-minus class="w-12 h-12 text-gray-300 dark:text-gray-600" />
            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada materi untuk sesi ini.</p>
        </div>
    @endforelse
</div>
