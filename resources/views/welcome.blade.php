<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MyClass') }} - Sistem Informasi Manajemen</title>
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
        class="antialiased min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col font-sans relative">

        <!-- ERP Grid Background -->
        <div class="absolute inset-0 z-0 pointer-events-none bg-grid border-b border-gray-200 dark:border-gray-800"
            style="mask-image: linear-gradient(to bottom, black, transparent); -webkit-mask-image: linear-gradient(to bottom, black, transparent);">
        </div>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center p-6 sm:p-12 h-full">
            <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center">

                <div class="space-y-6">
                    <div
                        class="inline-flex items-center rounded-full bg-primary-50 dark:bg-primary-900/30 px-3 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300 ring-1 ring-inset ring-primary-600/20">
                        SI Space
                    </div>
                    <h1
                        class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">
                        {{ $headings['hero_title'] }} <br /> <span class="text-primary-600 dark:text-primary-400">{{ $headings['hero_subtitle'] }}</span>
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-xl">
                        Kelola jadwal kelas, daftar presensi, pengumpulan tugas, hingga materi belajar dalam satu
                        antarmuka yang cepat dan mudah.
                    </p>
                    <div class="pt-4 flex items-center gap-4">
                        @auth
                            <a href="{{ url('/admin') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-primary-600 text-white dark:bg-primary-500 dark:text-white px-8 py-3.5 text-base font-bold shadow-lg shadow-primary-600/30 dark:shadow-primary-500/20 ring-1 ring-primary-600/50 dark:ring-primary-400/40 hover:bg-primary-500 dark:hover:bg-primary-400 hover:-translate-y-0.5 hover:shadow-primary-600/50 dark:hover:shadow-primary-400/40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:focus-visible:outline-primary-400 transition-all duration-300">
                                Buka Dasbor</a>
                        @else
                            <a href="{{ route('filament.admin.auth.login') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-primary-600 text-white dark:bg-primary-500 dark:text-white px-8 py-3.5 text-base font-bold shadow-lg shadow-primary-600/30 dark:shadow-primary-500/20 ring-1 ring-primary-600/50 dark:ring-primary-400/40 hover:bg-primary-500 dark:hover:bg-primary-400 hover:-translate-y-0.5 hover:shadow-primary-600/50 dark:hover:shadow-primary-400/40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:focus-visible:outline-primary-400 transition-all duration-300">Masuk
                                ke Aplikasi
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3">
                                    </path>
                                </svg></a>
                        @endauth
                    </div>
                </div>

                <!-- Feature Cards Mockup -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-xl hover:border-primary-500/30 dark:hover:border-primary-500/30 transition-all duration-300 hover:-translate-y-1 group cursor-default">
                        <div
                            class="w-12 h-12 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center mb-4 text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-500/20 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-950 dark:text-white mb-2 leading-tight">
                            {{ $headings['feature_session'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $headings['feature_session_desc'] }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-xl hover:border-primary-500/30 dark:hover:border-primary-500/30 transition-all duration-300 hover:-translate-y-1 group cursor-default translate-y-0 sm:translate-y-6">
                        <div
                            class="w-12 h-12 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center mb-4 text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-500/20 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-950 dark:text-white mb-2 leading-tight">
                            {{ $headings['feature_task'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $headings['feature_task_desc'] }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-xl hover:border-primary-500/30 dark:hover:border-primary-500/30 transition-all duration-300 hover:-translate-y-1 group cursor-default">
                        <div
                            class="w-12 h-12 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center mb-4 text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-500/20 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-950 dark:text-white mb-2 leading-tight">
                            {{ $headings['feature_attendance'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $headings['feature_attendance_desc'] }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-xl hover:border-primary-500/30 dark:hover:border-primary-500/30 transition-all duration-300 hover:-translate-y-1 group cursor-default translate-y-0 sm:translate-y-6">
                        <div
                            class="w-12 h-12 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center mb-4 text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-500/20 group-hover:scale-110 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-950 dark:text-white mb-2 leading-tight">
                            {{ $headings['feature_group'] }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $headings['feature_group_desc'] }}</p>
                    </div>
                </div>

            </div>
        </main>
    </body>

</html>
