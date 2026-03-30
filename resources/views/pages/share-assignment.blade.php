<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased font-sans">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tugas {{ $course->name }} - {{ config('app.name', 'MyClass') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
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
        </style>
    </head>

    <body class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col relative font-sans">

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
                        <h2 class="text-base font-bold text-gray-950 dark:text-white tracking-tight uppercase">Daftar
                            Pengumpulan</h2>
                    </div>

                    <div
                        class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm flex flex-col overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm whitespace-nowrap">
                                <thead
                                    class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white">Mahasiswa</th>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white">Nomor Induk
                                        </th>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white">Hasil Tugas
                                        </th>
                                        <th class="px-5 py-4 font-semibold text-gray-950 dark:text-white text-right">
                                            Waktu Pengumpulan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($submissions as $submission)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span
                                                            class="font-bold text-gray-950 dark:text-white text-sm">{{ $submission->student->full_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="text-gray-500 dark:text-gray-400 font-medium text-sm">
                                                    {{ $submission->student->student_number }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col gap-2 max-w-sm">

                                                    @if ($submission->getMedia('submission')->count() > 0)
                                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                                            @foreach ($submission->getMedia('submission') as $media)
                                                                <button type="button" onclick="openPdfModal('{{ $media->getUrl() }}', '{{ addslashes($submission->student->full_name) }}')"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-500/10 hover:bg-primary-100 dark:hover:bg-primary-500/20 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-500/20 text-[10px] font-bold uppercase tracking-widest transition-colors decoration-transparent shrink-0 focus:outline-none">
                                                                    <x-heroicon-m-document-arrow-down
                                                                        class="w-4 h-4 shrink-0" />
                                                                    <span
                                                                        class="truncate max-w-[150px]">{{ $media->file_name }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span
                                                            class="text-[10px] text-danger-500 uppercase tracking-widest font-bold italic mt-1">File
                                                            tidak ditemukan</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-right align-top">
                                                @php
                                                    $isLate =
                                                        $assignment &&
                                                        $assignment->due_date &&
                                                        $submission->submitted_at > $assignment->due_date;
                                                @endphp
                                                <div
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $isLate ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }} font-bold text-[10px] uppercase tracking-widest">
                                                    <span
                                                        class="w-1.5 h-1.5 rounded-full {{ $isLate ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                                    {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y H:i') : '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-16 text-center">
                                                <div
                                                    class="mx-auto flex max-w-xs flex-col items-center justify-center text-center">
                                                    <div
                                                        class="mb-4 rounded-full bg-gray-100 dark:bg-gray-800 p-3 ring-1 ring-gray-200 dark:ring-gray-700">
                                                        <x-heroicon-o-document-text
                                                            class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                                                    </div>
                                                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
                                                        Tidak ada pengumpulan</h4>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada
                                                        mahasiswa yang mengumpulkan tugas ini.</p>
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

                    <!-- Info Tugas -->
                    <div class="flex flex-col gap-3">
                        <h3
                            class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest pl-1">
                            Informasi Tugas</h3>

                        <div
                            class="fi-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm p-5 flex flex-col gap-5">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest pl-1">
                                    Judul Tugas
                                </span>
                                <h4 class="text-sm font-bold text-gray-950 dark:text-white pl-1 leading-tight mb-0.5">
                                    {{ $assignment ? $assignment->title : 'Tugas belum dipilih' }}
                                </h4>
                                @if($assignment && $assignment->classSession)
                                    <span class="text-[10px] font-bold text-fuchsia-600 dark:text-fuchsia-400 uppercase tracking-widest pl-1">
                                        Sesi Ke-{{ $assignment->classSession->session_number }}
                                    </span>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 dark:border-gray-800"></div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1 w-full relative">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Terkumpul</span>
                                    <span
                                        class="text-sm font-bold text-gray-950 dark:text-white">{{ $submittedCount }}<span
                                            class="text-[10px] text-gray-400 font-normal ml-0.5">/{{ $totalStudents }}</span>
                                        Orang</span>

                                    <div class="mt-2 flex items-center gap-2">
                                        <div
                                            class="h-1 flex-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-info-500 rounded-full transition-all duration-500"
                                                style="width: {{ $submissionPercentage }}%"></div>
                                        </div>
                                        <span
                                            class="text-[9px] font-bold text-gray-500">{{ $submissionPercentage }}%</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 text-right">
                                    <span
                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-0.5">Batas
                                        Pengumpulan</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white">
                                        {{ $formattedDeadline }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- PDF Modal -->
    <div id="pdf-modal" class="hidden relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity cursor-pointer" onclick="closePdfModal()"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col pointer-events-auto">
                    
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate pr-4" id="modal-title">Title</h3>
                        <button type="button" onclick="closePdfModal()" class="rounded-full bg-white dark:bg-gray-800 p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition focus:outline-none">
                            <span class="sr-only">Tutup</span>
                            <x-heroicon-m-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                    
                    <div class="flex-1 bg-gray-100 dark:bg-gray-950 p-2 sm:p-4">
                        <iframe id="pdf-iframe" src="" class="w-full h-full rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-inner" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPdfModal(url, title) {
            document.getElementById('pdf-modal').classList.remove('hidden');
            document.getElementById('pdf-iframe').src = url;
            document.getElementById('modal-title').innerText = title;
            document.body.style.overflow = 'hidden';
        }

        function closePdfModal() {
            document.getElementById('pdf-modal').classList.add('hidden');
            document.getElementById('pdf-iframe').src = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePdfModal();
            }
        });
    </script>
</body>

</html>
