<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased font-sans">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Presensi {{ $course->name }} - {{ config('app.name', 'MyClass') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root {
                --font-family: '{{ $userTheme->font }}', sans-serif;
                --c-border-radius: {{ $userTheme->border_radius }};

                /* Global Tailwind V4 Radius Overrides */
                --radius-md: {{ $userTheme->border_radius }} !important;
                --radius-lg: {{ $userTheme->border_radius }} !important;
                --radius-xl: {{ $userTheme->border_radius }} !important;
                --radius-2xl: {{ $userTheme->border_radius }} !important;

                /* Dynamic Primary Color Overrides */
                @foreach($primaryColors as $shade => $rgb)
                --color-primary-{{ $shade }}: rgb({{ $rgb }}) !important;
                @endforeach
            }

            body {
                font-family: var(--font-family) !important;
            }

            .bg-grid {
                background-size: 32px 32px;
                background-image: linear-gradient(to right, rgba(230, 230, 230, 0.4) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(230, 230, 230, 0.4) 1px, transparent 1px);
            }

            @media (prefers-color-scheme: dark) {
                .bg-grid {
                    background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                        linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
                }
            }

            [x-cloak] {
                display: none !important;
            }

            /* Apply border radius override to everything with standard rounded classes */
            .rounded-md, .rounded-lg, .rounded-xl, .rounded-2xl {
                border-radius: var(--c-border-radius) !important;
            }
        </style>
        @if($userTheme->font !== 'Inter')
            <link rel='preconnect' href='https://fonts.googleapis.com'>
            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
            <link href='https://fonts.googleapis.com/css2?family={{ $userTheme->font }}:wght@400;500;600;700&display=swap' rel='stylesheet'>
        @endif
    </head>

    <body
        class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col relative font-sans">

        <!-- ERP Grid Background -->
        <div class="fixed inset-0 z-0 pointer-events-none bg-grid"
            style="mask-image: linear-gradient(to bottom, black, transparent); -webkit-mask-image: linear-gradient(to bottom, black, transparent);">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full mb-12">
            <!-- Header -->
            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 mb-8 border-b border-gray-200 dark:border-gray-800">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span
                            class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-1 text-[11px] font-bold text-zinc-600 dark:text-zinc-300 ring-1 ring-inset ring-zinc-500/20 uppercase">
                            {{ $course->code }}
                        </span>
                        <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            Semester Genap
                        </span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-950 dark:text-white tracking-tight">{{ $course->name }}
                    </h1>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-1">
                        <x-heroicon-m-user class="w-4 h-4 opacity-70 shrink-0" />
                        {{ $course->lecturer }}
                    </p>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                <!-- Attendance -->
                <div class="lg:col-span-8 flex flex-col gap-4">
                    <div class="flex items-center justify-between px-1">
                        <h2 class="text-base font-bold text-gray-950 dark:text-white tracking-tight uppercase">
                            {{ $headings['list'] }}
                        </h2>
                        <span class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            {{ $attendances->count() }} Terdata
                        </span>
                    </div>

                    <div
                        class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm flex flex-col overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead
                                    class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white">Mahasiswa</th>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white">NIM
                                        </th>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white text-right">
                                            Waktu Presensi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($attendances as $attendance)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span
                                                            class="font-bold text-gray-950 dark:text-white text-sm">{{ $attendance->student->full_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="text-gray-500 dark:text-gray-400 font-medium text-sm">
                                                    {{ $attendance->student->student_number }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <div
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] uppercase tracking-widest">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ $attendance->attended_at ? $attendance->attended_at->format('H:i') : '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-16 text-center">
                                                <div
                                                    class="mx-auto flex max-w-xs flex-col items-center justify-center text-center">
                                                    <div
                                                        class="mb-4 rounded-full bg-gray-100 dark:bg-gray-800 p-3 ring-1 ring-gray-200 dark:ring-gray-700">
                                                        <x-heroicon-o-user-group
                                                            class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                                                    </div>
                                                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
                                                        Tidak ada data kehadiran</h4>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada
                                                        data kehadiran untuk sesi ini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 flex flex-col gap-8">

                    <!-- Info Sesi -->
                    <div class="flex flex-col gap-3">
                        <h3
                            class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest pl-1">
                            {{ $headings['info'] }} {{ $sessionInfo ? 'Ke-' . $sessionInfo->session_number : '' }}</h3>

                        <div
                            class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-5 flex flex-col gap-5">
                            <div class="flex flex-col gap-1">
                                <span
                                    class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Tanggal
                                    Sesi</span>
                                <span
                                    class="text-sm font-bold text-gray-950 dark:text-white">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
                            </div>

                            <div class="border-t border-gray-100 dark:border-gray-800"></div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1 w-full relative">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Partisipan</span>
                                    <span
                                        class="text-sm font-bold text-gray-950 dark:text-white">{{ $presentCount }}<span class="text-[10px] text-gray-400 font-normal ml-0.5">/{{ $totalStudents }}</span> Orang</span>
                                    
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="h-1 flex-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary-500 rounded-full transition-all duration-500" style="width: {{ $attendancePercentage }}%"></div>
                                        </div>
                                        <span class="text-[9px] font-bold text-gray-500">{{ $attendancePercentage }}%</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 text-right">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Waktu
                                        Kelas</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white">
                                        {{ $formattedTime }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </body>

</html>
