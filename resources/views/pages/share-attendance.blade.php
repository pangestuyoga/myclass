<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased font-sans">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Presensi {{ $course->name }} - {{ config('app.name', 'MyClass') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] {
                display: none !important;
            }

            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                background-color: #fafafa;
                color: #18181b;
            }

            .dark body {
                background-color: #09090b;
                color: #fafafa;
            }

            .card {
                background-color: #ffffff;
                border: 1px solid #e4e4e7;
                border-radius: 0.5rem;
            }

            .dark .card {
                background-color: #09090b;
                border-color: #27272a;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                border-radius: 9999px;
                border: 1px solid #e4e4e7;
                padding-left: 0.625rem;
                padding-right: 0.625rem;
                padding-top: 0.125rem;
                padding-bottom: 0.125rem;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .dark .badge {
                border-color: #27272a;
            }

            .select-trigger {
                display: flex;
                height: 2.5rem;
                width: 100%;
                align-items: center;
                justify-content: space-between;
                border-radius: 0.375rem;
                border: 1px solid #e4e4e7;
                background-color: transparent;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
            }

            .dark .select-trigger {
                border-color: #27272a;
            }

            ::-webkit-scrollbar {
                width: 4px;
            }

            ::-webkit-scrollbar-thumb {
                background: #d4d4d8;
                border-radius: 4px;
            }

            .dark ::-webkit-scrollbar-thumb {
                background: #3f3f46;
            }
        </style>
    </head>

    <body class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <div class="space-y-8 tracking-tight">
                <!-- Header -->
                <div
                    class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="badge bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 capitalize tracking-tighter">{{ $course->code }}</span>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">SEMESTER
                                GENAP</span>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tighter">{{ $course->name }}</h1>
                        <p class="text-zinc-500 flex items-center gap-2 tracking-tight text-sm font-medium">
                            <x-heroicon-o-user class="w-4 h-4 opacity-50" />
                            {{ $course->lecturer }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5 min-w-[240px]">
                        <label for="date-selector"
                            class="text-[10px] font-bold text-zinc-500 ml-1 uppercase tracking-wider">Pilih Tanggal
                            Sesi</label>
                        <div x-data="{ date: '{{ $date }}' }" class="relative">
                            <select id="date-selector" x-model="date" @change="window.location.href = '?date=' + date"
                                class="select-trigger cursor-pointer appearance-none pr-10 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
                                @foreach ($availableDates as $availableDate)
                                    <option value="{{ $availableDate->toDateString() }}"
                                        {{ $date == $availableDate->toDateString() ? 'selected' : '' }}>
                                        Pertemuan: {{ $availableDate->translatedFormat('d F Y') }}
                                    </option>
                                @endforeach
                                @if ($availableDates->isEmpty())
                                    <option value="{{ now()->toDateString() }}">Tidak ada riwayat</option>
                                @endif
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400">
                                <x-heroicon-m-chevron-up-down class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <!-- Attendance Content -->
                    <div class="lg:col-span-8 space-y-6">
                        <div class="flex items-center justify-between mb-2 px-1">
                            <h2 class="text-lg font-bold tracking-tight uppercase tracking-tighter">Daftar Kehadiran
                            </h2>
                            <span
                                class="text-[11px] font-black text-zinc-400 uppercase tracking-widest">{{ $attendances->count() }}
                                Terdata</span>
                        </div>

                        <div class="card overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="border-b border-zinc-200 dark:border-zinc-800">
                                        <tr>
                                            <th
                                                class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight">
                                                Mahasiswa</th>
                                            <th
                                                class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight">
                                                Nomor Induk</th>
                                            <th
                                                class="px-6 py-4 font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight text-right">
                                                Waktu Presensi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                        @forelse($attendances as $attendance)
                                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/10 transition-colors">
                                                <td class="px-6 py-5">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-7 h-7 rounded-sm bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-[9px] font-bold text-zinc-500 dark:text-zinc-400 shrink-0 uppercase">
                                                            {{ collect(explode(' ', $attendance->student->full_name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('') }}
                                                        </div>
                                                        <div class="flex flex-col gap-0.5">
                                                            <span
                                                                class="font-semibold text-zinc-950 dark:text-zinc-50 tracking-tight">{{ $attendance->student->full_name }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5">
                                                    <span class="text-zinc-500 font-mono text-xs tracking-tight">
                                                        {{ $attendance->student->student_number }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-5 text-right">
                                                    <div
                                                        class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 font-bold text-[10px] tracking-widest uppercase">
                                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                        {{ $attendance->attended_at ? $attendance->attended_at->format('H:i') : '-' }}
                                                        WIB
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-20 text-center">
                                                    <p
                                                        class="text-xs font-medium text-zinc-400 uppercase tracking-widest opacity-60">
                                                        Belum ada data kehadiran untuk sesi ini</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Panels -->
                    <div class="lg:col-span-4 space-y-8">
                        <!-- Class Status -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest pl-1">Informasi Sesi
                            </h3>
                            <div class="card p-6 space-y-5 shadow-sm">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Tanggal
                                        Sesi</span>
                                    <span
                                        class="text-sm font-bold tracking-tight">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
                                </div>
                                <div class="h-px bg-zinc-100 dark:bg-zinc-800"></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Partisipan</span>
                                        <span class="text-sm font-bold">{{ $attendances->count() }} Orang</span>
                                    </div>
                                    <div class="flex flex-col gap-1 text-right">
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Jam
                                            Kelas</span>
                                        <span class="text-sm font-bold whitespace-nowrap">08:00 - 10:30</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignments -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest pl-1">Tugas Terkait
                            </h3>
                            <div class="space-y-3">
                                @forelse($assignments as $assignment)
                                    <div
                                        class="card p-5 hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition-colors shadow-sm cursor-default">
                                        <div class="flex flex-col gap-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <h4
                                                    class="text-xs font-bold leading-tight tracking-tight uppercase line-clamp-2">
                                                    {{ $assignment->title }}</h4>
                                                <span
                                                    class="shrink-0 text-[8px] font-black px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 uppercase tracking-widest text-zinc-400">TGS</span>
                                            </div>
                                            <div
                                                class="flex items-center gap-4 text-[10px] text-zinc-400 font-bold uppercase tracking-widest">
                                                <div class="flex items-center gap-1.5">
                                                    <x-heroicon-m-calendar class="w-3 h-3 opacity-40 text-rose-500" />
                                                    <span>{{ $assignment->deadline ? $assignment->deadline->translatedFormat('d M') : 'N/A' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <x-heroicon-m-document-check
                                                        class="w-3 h-3 opacity-40 text-blue-500" />
                                                    <span>{{ $assignment->assignment_submissions_count }} Kirim</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="text-[10px] text-zinc-400 font-bold italic pl-1 uppercase tracking-widest opacity-50">
                                        Tidak ada tugas terdaftar.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
