<header class="bg-midnight/80 backdrop-blur-sm border-b border-white/10 py-3">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo_ukm3.jpeg') }}" alt="Logo" class="h-10 w-10 rounded-full border-2 border-white/20">
            <div>
                <span class="text-xl font-bold bg-gradient-to-r from-cyan to-electric text-transparent bg-clip-text">UNICODE</span>
                <span class="block text-[10px] text-white/50 -mt-1">UKM Pemrograman</span>
            </div>
        </a>
        <a href="{{ url('/login') }}" class="text-white/60 hover:text-white transition text-sm flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</header>
