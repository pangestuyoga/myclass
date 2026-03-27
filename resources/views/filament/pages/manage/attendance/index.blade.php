<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Monitoring Presensi</x-slot>
            <x-slot name="description">Pilih mata kuliah untuk memantau kehadiran mahasiswa per pertemuan.</x-slot>

            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
                @foreach ($courses as $course)
                    <a href="{{ $course->detail_url }}" class="break-inside-avoid mb-6 block group">
                        <div @class([
                            'fi-card flex flex-col justify-between rounded-xl border transition duration-200 group relative',
                            'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-primary-500' => !$course->is_ongoing,
                            'bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500' =>
                                $course->is_ongoing,
                        ])>

                            @if ($course->is_ongoing)
                                <div class="absolute top-0 right-0 -translate-x-1 -translate-y-1/2 z-10">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-primary-600 px-3 py-1 text-[10px] font-bold text-white shadow-lg uppercase tracking-widest">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                                        Hari Ini
                                    </span>
                                </div>
                            @endif

                            <div class="p-6 space-y-4 flex-1">
                                <div class="space-y-1">
                                    <h3 @class([
                                        'text-lg font-bold leading-tight flex items-center gap-2 transition-colors',
                                        'text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400' => !$course->is_ongoing,
                                        'text-primary-900 dark:text-primary-100' => $course->is_ongoing,
                                    ])>
                                        <x-heroicon-o-folder @class([
                                            'w-5 h-5 shrink-0',
                                            'opacity-40' => !$course->is_ongoing,
                                            'text-primary-500' => $course->is_ongoing,
                                        ]) />
                                        {{ $course->name }}
                                    </h3>
                                    <div @class([
                                        'flex items-center gap-1.5',
                                        'text-gray-500 dark:text-gray-400' => !$course->is_ongoing,
                                        'text-primary-600 dark:text-primary-400' => $course->is_ongoing,
                                    ])>
                                        <x-heroicon-m-hashtag @class(['w-3.5 h-3.5 shrink-0', 'opacity-50' => !$course->is_ongoing]) />
                                        <span class="text-[11px] font-medium leading-none uppercase tracking-widest">
                                            {{ $course->code }}
                                        </span>
                                    </div>
                                </div>

                                <div @class([
                                    'pt-4 border-t',
                                    'border-gray-100 dark:border-gray-800' => !$course->is_ongoing,
                                    'border-primary-200 dark:border-primary-800' => $course->is_ongoing,
                                ])>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <div @class([
                                            'w-8 h-8 rounded-full flex items-center justify-center mr-3 shrink-0 border',
                                            'bg-primary-100 dark:bg-primary-900/40 border-primary-200 dark:border-primary-800' => !$course->is_ongoing,
                                            'bg-white dark:bg-primary-800 border-primary-300 dark:border-primary-600' =>
                                                $course->is_ongoing,
                                        ])>
                                            <x-heroicon-m-user @class([
                                                'w-4 h-4',
                                                'text-primary-600 dark:text-primary-300' => !$course->is_ongoing,
                                                'text-primary-700 dark:text-primary-200' => $course->is_ongoing,
                                            ]) />
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span
                                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none mb-1">Dosen
                                                Pengampu</span>
                                            <span @class([
                                                'truncate font-medium',
                                                'text-gray-900 dark:text-gray-200' => !$course->is_ongoing,
                                                'text-primary-900 dark:text-primary-50' => $course->is_ongoing,
                                            ])>
                                                {{ $course->lecturer_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div @class([
                                'flex items-center justify-end gap-2 p-4 pt-0 rounded-b-xl',
                                'bg-primary-100/30 dark:bg-primary-900/10 pt-4' => $course->is_ongoing,
                            ])>
                                <span
                                    class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-tight group-hover:underline">
                                    Buka &rarr;
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
