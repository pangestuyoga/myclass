<div class="space-y-4 py-2">
    @foreach($record->getMedia('materials') as $item)
        <div class="rounded-xl border border-gray-100 dark:border-white/5 overflow-hidden shadow-sm">
            <div class="p-3 bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight">Lampiran PDF</span>
            </div>
            <div class="h-[600px] w-full bg-white dark:bg-gray-900 overflow-hidden">
                <iframe src="{{ $item->getUrl() }}" class="w-full h-full border-0" frameborder="0"></iframe>
            </div>
        </div>
    @endforeach
</div>
