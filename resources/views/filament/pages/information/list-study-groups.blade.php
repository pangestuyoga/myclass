<x-filament-panels::page>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-6">
            <div class="w-full max-w-xl">
                {{ $this->form }}
            </div>
        </div>

        @if ($groups->isNotEmpty())
            <div class="columns-1 md:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6 pb-8">
                @foreach ($groups as $record)
                    @php $isMyGroup = $this->isMyGroup($record); @endphp
                    <div class="break-inside-avoid mb-6">
                        <div @class([
                            'fi-card flex flex-col justify-between rounded-xl border transition duration-200 group relative',
                            'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md' => !$isMyGroup,
                            'bg-primary-50/30 dark:bg-primary-900/10 border-primary-500 shadow-md ring-1 ring-primary-500' => $isMyGroup,
                        ])>

                            @if ($isMyGroup)
                                <div class="absolute -top-3 -right-3 z-10">
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
                                            'bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-300 ring-primary-600/20' => !$isMyGroup,
                                            'bg-white dark:bg-primary-800/40 text-primary-700 dark:text-primary-200 ring-primary-500/30' => $isMyGroup,
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
                                        'text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400' => !$isMyGroup,
                                        'text-primary-900 dark:text-primary-100' => $isMyGroup,
                                    ])>
                                        <x-heroicon-o-users @class([
                                            'w-5 h-5 shrink-0',
                                            'opacity-40' => !$isMyGroup,
                                            'text-primary-500' => $isMyGroup,
                                        ]) />
                                        {{ $record->name }}
                                    </h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <div @class([
                                            'w-8 h-8 rounded-full flex items-center justify-center mr-3 shrink-0 border',
                                            'bg-primary-100 dark:bg-primary-900/40 border-primary-200 dark:border-primary-800' => !$isMyGroup,
                                            'bg-white dark:bg-primary-800 border-primary-300 dark:border-primary-600' => $isMyGroup,
                                        ])>
                                            @if ($record->leader)
                                                <img src="{{ $record->leader->user->facehash_avatar_url }}"
                                                    alt="User avatar" class="w-full h-full rounded-full object-cover" />
                                            @else
                                                <x-heroicon-m-user @class([
                                                    'w-4 h-4',
                                                    'text-primary-600 dark:text-primary-300' => !$isMyGroup,
                                                    'text-primary-700 dark:text-primary-200' => $isMyGroup,
                                                ]) />
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span
                                                class="text-[10px] font-bold uppercase text-gray-400 tracking-widest leading-none mb-1">Ketua
                                                Kelompok</span>
                                            <span @class([
                                                'truncate font-medium',
                                                'text-gray-900 dark:text-gray-200' => !$isMyGroup,
                                                'text-primary-900 dark:text-primary-50' => $isMyGroup,
                                            ])>
                                                {{ $record->leader->full_name ?? 'Belum Ditentukan' }}
                                                @if ($studentId && $record->leader_id === $studentId)
                                                    <span
                                                        class="text-[10px] text-primary-600 font-bold ml-1">(Anda)</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div @class([
                                        'pt-4 border-t',
                                        'border-gray-100 dark:border-gray-800' => !$isMyGroup,
                                        'border-primary-200 dark:border-primary-800' => $isMyGroup,
                                    ])>
                                        <div class="flex items-center justify-between mb-3">
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Anggota
                                                Kelompok</span>
                                            <span
                                                class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 ring-1 ring-inset ring-violet-600/20">
                                                {{ $record->students->count() }} orang
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse ($record->students as $student)
                                                <span @class([
                                                    'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset',
                                                    'bg-gray-50 dark:bg-gray-800/50 text-gray-700 dark:text-gray-300 ring-gray-200 dark:ring-gray-700' =>
                                                        $student->id !== $studentId,
                                                    'bg-primary-600 text-white dark:text-gray-900 ring-primary-700 font-bold shadow-sm' =>
                                                        $student->id === $studentId,
                                                ])>
                                                    {{ $student->full_name }}
                                                    @if ($student->id === $studentId)
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

                            <div @class([
                                'flex items-center justify-end gap-2 p-4 pt-0 rounded-b-xl',
                                'bg-primary-100/30 dark:bg-primary-900/10 pt-4' => $isMyGroup,
                            ])>
                                <x-filament::button size="xs" color="warning" tooltip="Ubah" variant="primary"
                                    class="rounded-lg shadow-sm"
                                    wire:click="mountAction('editStudyGroup', { record: {{ $record->id }} })">
                                    <x-heroicon-m-pencil-square class="w-4 h-4" />
                                </x-filament::button>

                                <x-filament::button size="xs" color="danger" tooltip="Hapus"
                                    class="rounded-lg shadow-sm"
                                    wire:click="mountAction('deleteStudyGroup', { record: {{ $record->id }} })">
                                    <x-heroicon-m-trash class="w-4 h-4" />
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($groups->isEmpty())
            <x-filament::empty-state icon="heroicon-o-users" heading="Tidak ada data yang ditemukan"
                description="Setelah Anda membuat data pertama, maka akan muncul disini." iconColor="gray">
            </x-filament::empty-state>
        @endif
    </x-filament::section>
</x-filament-panels::page>
