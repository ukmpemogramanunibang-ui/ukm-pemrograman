{{-- Header khusus halaman login --}}
<header class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-40 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto flex items-center justify-between p-4">
        {{-- Tombol Kembali ke Beranda --}}
        <a href="{{ url('/') }}" 
           class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors duration-200 group">
            <i class="fas fa-arrow-left text-sm group-hover:-translate-x-1 transition-transform duration-200"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>

        {{-- Logo di tengah --}}
        <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo UNICODE"
                 class="h-10 w-10 md:h-12 md:w-12 rounded-full border-2 border-cyan-500 shadow-md group-hover:scale-105 transition-transform duration-300">
            <span class="text-lg md:text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan to-blue-600">
                UNICODE
            </span>
        </a>

        <div class="flex items-center space-x-4">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 md:px-6 md:py-2.5 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold rounded-full hover:scale-105 transition-transform shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 flex items-center gap-2 text-sm md:text-base">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            @else
                <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                    <i class="fas fa-lock mr-1"></i> Halaman Login
                </span>
            @endauth
        </div>
    </div>
</header>