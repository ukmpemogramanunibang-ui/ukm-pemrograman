@if(session('impersonation.active'))
    @php
        $imp = session('impersonation');
        $elapsed   = now()->timestamp - (int) ($imp['started_at'] ?? now()->timestamp);
        $remaining = max(0, 3600 - $elapsed);
    @endphp

    <div class="sticky top-0 z-[9999]
                bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500
                text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2.5
                    flex items-center gap-3 flex-wrap">

            <span class="flex items-center gap-2
                         text-[11px] font-black uppercase tracking-wider shrink-0">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full
                                 rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                </span>
                <i class="fas fa-user-secret"></i>
                Mode Jelajah Aktif
            </span>

            <span class="text-[11px] text-white/90 hidden md:inline truncate">
                Owner <b>{{ $imp['owner_name'] ?? '—' }}</b>
                melihat sebagai
                <b>{{ $imp['target_name'] ?? '—' }}</b>
                ({{ ucfirst($imp['role_slug'] ?? 'user') }})
            </span>

            <span class="ml-auto inline-flex items-center gap-2">
                <span class="text-[10px] font-mono bg-black/25 px-2 py-1 rounded
                             inline-flex items-center gap-1.5"
                      id="impersonation-timer"
                      data-seconds="{{ $remaining }}">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="impersonation-timer-text">
                        {{ gmdate('i:s', $remaining) }}
                    </span>
                </span>

                <form action="{{ route('owner.settings.explore.exit') }}"
                      method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Keluar dari mode jelajah akun?')"
                            class="bg-black/30 hover:bg-black/50
                                   text-white text-[11px] font-black
                                   px-3 py-1.5 rounded-lg
                                   transition inline-flex items-center gap-1.5">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </span>
        </div>
    </div>

    <script>
    (function () {
        const el = document.getElementById('impersonation-timer');
        if (!el) return;
        let s = parseInt(el.dataset.seconds || '0', 10);
        const txt = document.getElementById('impersonation-timer-text');
        if (!txt) return;

        function tick() {
            if (s <= 0) {
                txt.textContent = '00:00';
                setTimeout(() => window.location.reload(), 500);
                return;
            }
            const m   = String(Math.floor(s / 60)).padStart(2, '0');
            const sec = String(s % 60).padStart(2, '0');
            txt.textContent = `${m}:${sec}`;
            s--;
        }
        tick();
        setInterval(tick, 1000);
    })();
    </script>
@endif