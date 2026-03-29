<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="w-full max-w-xl">
                {{ $this->form }}
            </div>
        </div>

        @if ($this->studyGroups->isNotEmpty())
        <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6">
            @foreach ($this->studyGroups as $record)
                <div class="break-inside-avoid mb-6">
                    <div class="{{ $record->card_classes }}">

                        @if ($record->is_my_group)
                            <div class="absolute top-0 right-0 -translate-x-1 -translate-y-1/2 z-10">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-primary-600 px-3 py-1 text-[10px] font-bold text-white dark:text-gray-900 shadow-lg ring-2 ring-white dark:ring-gray-900 uppercase tracking-widest">
                                    <x-heroicon-m-check-badge class="w-3.5 h-3.5" />
                                    Kelompok Saya
                                </span>
                            </div>
                        @endif

                        <div class="p-6 space-y-4 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @forelse ($record->courses as $course)
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2.5 py-1 text-[10px] font-bold ring-1 ring-inset uppercase tracking-tight',
                                        'bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-300 ring-primary-600/20' => !$record->is_my_group,
                                        'bg-white dark:bg-primary-800/40 text-primary-700 dark:text-primary-200 ring-primary-500/30' => $record->is_my_group,
                                    ])>
                                        {{ $course->name }}
                                    </span>
                                @empty
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-500/10 px-2 py-0.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 ring-1 ring-inset ring-gray-600/20 uppercase tracking-tight italic">
                                        Tanpa Mata Kuliah
                                    </span>
                                @endforelse
                            </div>

                            <div class="space-y-1">
                                <h3 @class([
                                    'text-lg font-bold leading-tight flex items-center gap-2 transition-colors',
                                    'text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400' => !$record->is_my_group,
                                    'text-primary-900 dark:text-primary-100' => $record->is_my_group,
                                ])>
                                    <x-heroicon-o-users @class([
                                        'w-5 h-5 shrink-0',
                                        'opacity-40' => !$record->is_my_group,
                                        'text-primary-500' => $record->is_my_group,
                                    ]) />
                                    {{ $record->name }}
                                </h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <div @class([
                                        'w-8 h-8 rounded-full flex items-center justify-center mr-3 shrink-0 border',
                                        'bg-primary-100 dark:bg-primary-900/40 border-primary-200 dark:border-primary-800' => !$record->is_my_group,
                                        'bg-white dark:bg-primary-800 border-primary-300 dark:border-primary-600' => $record->is_my_group,
                                    ])>
                                        @if ($record->leader_avatar)
                                            <img src="{{ $record->leader_avatar }}"
                                                alt="User avatar" class="w-full h-full rounded-full object-cover" />
                                        @else
                                            <x-heroicon-m-user @class([
                                                'w-4 h-4',
                                                'text-primary-600 dark:text-primary-300' => !$record->is_my_group,
                                                'text-primary-700 dark:text-primary-200' => $record->is_my_group,
                                            ]) />
                                        @endif
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span
                                            class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none mb-1">Ketua
                                            Kelompok</span>
                                        <span @class([
                                            'truncate font-medium',
                                            'text-gray-900 dark:text-gray-200' => !$record->is_my_group,
                                            'text-primary-900 dark:text-primary-50' => $record->is_my_group,
                                        ])>
                                            {{ $record->leader_name }}
                                            @if ($record->is_leader)
                                                <span
                                                    class="text-[10px] text-primary-600 font-bold ml-1">(Anda)</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div @class([
                                    'pt-4 border-t',
                                    'border-gray-100 dark:border-gray-800' => !$record->is_my_group,
                                    'border-primary-200 dark:border-primary-800' => $record->is_my_group,
                                ])>
                                    <div class="flex items-center justify-between mb-3">
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Anggota
                                            Kelompok</span>
                                        <span
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 ring-1 ring-inset ring-violet-600/20">
                                            {{ $record->students_count }} orang
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($record->students as $student)
                                            <span @class([
                                                'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset',
                                                'bg-gray-50 dark:bg-gray-800/50 text-gray-700 dark:text-gray-300 ring-gray-200 dark:ring-gray-700' =>
                                                    !$student->is_me,
                                                'bg-primary-600 text-white dark:text-gray-900 ring-primary-700 font-bold shadow-sm' =>
                                                    $student->is_me,
                                            ])>
                                                {{ $student->full_name }}
                                                @if ($student->is_me)
                                                    (Anda)
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">Belum ada anggota</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        @canAny(['Update:StudyGroup', 'Delete:StudyGroup'])
                            <div @class([
                                'rounded-b-xl',
                                'bg-primary-100/30 dark:bg-primary-900/10' => $record->is_my_group,
                            ])>
                                <div @class([
                                    'mx-6 border-t flex items-center justify-end gap-2 py-4',
                                    'border-gray-100 dark:border-gray-800' => !$record->is_my_group,
                                    'border-primary-200 dark:border-primary-800' => $record->is_my_group,
                                ])>
                                    {{ ($this->editStudyGroupAction)(['studyGroup' => $record->id]) }}

                                    {{ ($this->deleteStudyGroupAction)(['studyGroup' => $record->id]) }}
                                </div>
                            </div>
                        @endcanAny
                    </div>
                </div>
            @endforeach
        </div>
    @endif

        @if ($this->studyGroups->isEmpty())
            <x-filament::empty-state icon="heroicon-o-users" heading="Tidak ada data yang ditemukan"
                description="Setelah Anda membuat data pertama, maka akan muncul disini." iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
