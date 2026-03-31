<x-filament-widgets::widget>
    <x-filament::section
        class="group relative overflow-hidden bg-gradient-to-br from-primary-50/50 to-white dark:from-primary-500/10 dark:to-gray-900 ring-1 ring-primary-500/30 hover:ring-primary-500/50 hover:shadow-2xl hover:shadow-primary-500/20 transition-all duration-500">

        <div
            class="absolute -right-10 -top-10 w-40 h-40 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl group-hover:bg-primary-500/20 transition-all duration-500">
        </div>
        <div
            class="absolute -left-10 -bottom-10 w-40 h-40 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl group-hover:bg-primary-500/20 transition-all duration-500">
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start gap-5 relative z-10">
            <div
                class="p-3 bg-primary-100 dark:bg-primary-500/20 rounded-2xl shrink-0 w-fit group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 shadow-sm border border-primary-200 dark:border-primary-500/20">
                <x-filament::icon icon="heroicon-o-chat-bubble-left-ellipsis"
                    class="w-7 h-7 text-primary-600 dark:text-primary-400 group-hover:animate-pulse" />
            </div>
            <div>
                <h2
                    class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 group-hover:from-primary-600 group-hover:to-primary-400 transition-all duration-300">
                    Informasi & Bantuan</h2>
                <p
                    class="text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed group-hover:text-gray-900 dark:group-hover:text-gray-300 transition-colors duration-300">
                    Jika Anda menemukan masalah dalam penggunaan aplikasi, membutuhkan bantuan, atau memiliki ide
                    perbaikan serta penambahan fitur baru, jangan ragu untuk menyampaikannya. Masukan Anda sangat
                    berarti bagi kami.
                </p>
                <div class="flex flex-wrap items-center gap-3 mt-5">
                    <a href="https://wa.me/6285819894938" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-500/30 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-500/10 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-500/20 active:scale-95 transition-all duration-300 text-sm font-semibold">
                        <x-filament::icon icon="heroicon-m-chat-bubble-oval-left" class="w-4 h-4" />
                        Komala
                    </a>
                    <a href="https://wa.me/6282121495806" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-500/30 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-500/10 hover:-translate-y-1 hover:shadow-lg hover:shadow-primary-500/20 active:scale-95 transition-all duration-300 text-sm font-semibold">
                        <x-filament::icon icon="heroicon-m-code-bracket" class="w-4 h-4" />
                        Pengembang
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
