<footer class="mt-auto px-6 py-6 bg-gradient-to-r from-amber-50/80 via-white/95 to-orange-50/80 dark:from-gray-900 dark:via-gray-900/95 dark:to-gray-900 border-t border-gray-200 dark:border-gray-800 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
        <p>&copy; {{ date('Y') }} <span class="font-semibold text-amber-600 dark:text-amber-400">UNICODE</span> — UKM Pemrograman UNIBANG</p>
        <div class="flex items-center gap-4">
            <a href="{{ route('about') }}" class="hover:text-amber-600 transition">Tentang</a>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <a href="{{ route('kontak') }}" class="hover:text-amber-600 transition">Kontak</a>
            <span class="text-gray-300 dark:text-gray-600">·</span>
            <a href="{{ route('login') }}" class="hover:text-amber-600 transition">Masuk</a>
        </div>
    </div>
</footer>
