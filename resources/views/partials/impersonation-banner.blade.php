{{-- resources/views/partials/impersonation-banner.blade.php --}}
@php
    $__imp = session('impersonation', []);
    $__impActive = (bool) ($__imp['active'] ?? false);
@endphp

@if($__impActive)
    @php
        $__targetName = $__imp['target_name'] ?? '—';
        $__roleSlug   = $__imp['role_slug'] ?? 'anggota';
        $__startedAt  = (int) ($__imp['started_at'] ?? time());
        $__total      = 3600;
        $__elapsed    = max(0, time() - $__startedAt);
        $__remaining  = max(0, $__total - $__elapsed);
        $__fmt        = fn($s) => sprintf('%02d:%02d',
            intdiv($s, 60), $s % 60);
    @endphp

    <div id="impBanner"
         class="relative z-40 w-full
                bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500
                text-white shadow-lg shadow-amber-500/20">
        <div class="max-w-[1600px] mx-auto px-3 sm:px-5 py-2.5 flex items-center gap-3">

            <span class="hidden sm:inline-flex items-center gap-2 shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-white rc-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-widest">Mode Jelajah</span>
            </span>

            <div class="flex items-center gap-2 min-w-0 flex-1">
                <i class="fas fa-user-secret text-sm opacity-90"></i>
                <p class="text-sm font-black truncate">{{ $__targetName }}</p>
            </div>

            <div class="hidden sm:flex items-center gap-2 shrink-0 px-3 py-1
                        rounded-lg bg-white/15 border border-white/20">
                <i class="fas fa-hourglass-half text-xs"></i>
                <span class="text-xs font-black" id="impRemaining">{{ $__fmt($__remaining) }}</span>
            </div>

            @if(\Illuminate\Support\Facades\Route::has('owner.settings.explore.exit'))
                <form method="POST"
                      action="{{ route('owner.settings.explore.exit') }}"
                      onsubmit="return confirm('Keluar dari mode jelajah? Anda akan kembali ke akun Owner.')"
                      class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg
                                   bg-white text-amber-600 hover:bg-amber-50
                                   text-[11px] font-black shadow-sm transition">
                        <i class="fas fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Keluar Jelajah</span>
                        <span class="sm:hidden">Keluar</span>
                    </button>
                </form>
            @endif
        </div>
        <div class="h-0.5 w-full bg-white/20">
            <div class="h-full rc-progress transition-all duration-1000"
                 style="width: {{ (int) round(($__elapsed / $__total) * 100) }}%"></div>
        </div>
    </div>

    <script>
        (function () {
            let remaining = {{ $__remaining }};
            const el = document.getElementById('impRemaining');
            const fmt = (s) => {
                s = Math.max(0, Math.floor(s));
                return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
            };
            setInterval(() => {
                remaining = Math.max(0, remaining - 1);
                if (el) el.textContent = fmt(remaining);
            }, 1000);
        })();
    </script>
@endif