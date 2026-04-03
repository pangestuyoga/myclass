<div class="space-y-4">
    @foreach ($record->getMedia('materials') as $item)
        <div class="rounded-xl border border-gray-100 dark:border-white/5 overflow-hidden shadow-sm">
            <div class="h-[600px] w-full bg-white dark:bg-gray-900 overflow-hidden">
                <iframe src="{{ $item->getUrl() }}" class="w-full h-full border-0" frameborder="0"></iframe>
            </div>
        </div>
    @endforeach
</div>
