@extends('layouts.roles.owner')

@section('title', 'Verifikasi – Jelajah Akun')

@section('content')
<div class="p-4 md:p-6 max-w-2xl mx-auto space-y-4">

    {{-- Target Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-start gap-3 mb-5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-violet-500/20">
                <i class="fas fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">Verifikasi Owner</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Masukkan password + PIN Owner untuk mulai menjelajahi.</p>
            </div>
        </div>

        {{-- Target Info --}}
        <div class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-900/60 border border-slate-200 dark:border-slate-700 mb-5">
            <p class="text-[10px] uppercase font-black text-gray-400 tracking-wider mb-2">Target Akun</p>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center font-black text-sm shrink-0 ring-2 ring-white dark:ring-slate-800">
                    {{ strtoupper(mb_substr($target->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-base font-black text-gray-900 dark:text-white truncate">{{ $target->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        NIM: {{ $target->nim ?? '-' }}
                        · Level: {{ $target->level->name ?? '-' }}
                    </p>
                    @if(isset($card) && $card->folder)
                        <p class="text-[10px] text-indigo-500 dark:text-indigo-400 mt-1">
                            <i class="fas fa-folder mr-1"></i>{{ $card->folder->name }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lockout Banner --}}
        @if(!empty($rateLimited) && $rateLimited)
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-2 border-rose-300 dark:border-rose-700/60 text-rose-700 dark:text-rose-300 mb-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <p class="font-black">Akun Terkunci Sementara</p>
                        <p class="mt-1 text-xs">Coba lagi dalam <b id="countdown" class="font-mono text-sm">{{ gmdate('i:s', $lockoutSec ?? 0) }}</b> menit.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Error --}}
        @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-700 dark:text-rose-300 text-sm mb-4">
                @foreach($errors->all() as $err)
                    <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('owner.settings.explore.verify.authorize') }}" method="POST" autocomplete="off" @if(!empty($rateLimited) && $rateLimited) inert @endif>
            @csrf
            <input type="hidden" name="user_id" value="{{ $target->id }}">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Password Owner</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password" id="pw" required autocomplete="current-password"
                           placeholder="Password login Owner"
                           class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-colors">
                    <button type="button" onclick="togglePwd('pw',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-violet-500">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">PIN Owner</label>
                <div class="relative">
                    <i class="fas fa-shield-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="pin" id="pin" required inputmode="numeric" minlength="4" maxlength="12" autocomplete="off"
                           placeholder="••••••"
                           class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm tracking-[0.3em] focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-colors">
                    <button type="button" onclick="togglePwd('pin',this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-violet-500">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <a href="{{ route('owner.settings.explore.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-center transition">
                    <i class="fas fa-arrow-left mr-1"></i> Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-violet-500 to-fuchsia-500 hover:shadow-lg hover:shadow-violet-500/30 active:scale-[0.98] transition-all inline-flex items-center justify-center gap-2">
                    <i class="fas fa-eye"></i> Mulai Menjelajahi
                </button>
            </div>
        </form>
    </div>

    <div class="text-center text-[10px] text-gray-400 dark:text-gray-500">
        <i class="fas fa-shield-halved mr-1"></i> Setiap percobaan dicatat & diaudit
    </div>
</div>

@push('scripts')
<script>
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    if (!inp) return;
    const icon = btn.querySelector('i');
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    if (icon) icon.className = show ? 'fas fa-eye-slash text-sm' : 'fas fa-eye text-sm';
}
@if(!empty($rateLimited) && $rateLimited && !empty($lockoutSec))
(function () {
    let r = {{ (int) $lockoutSec }};
    const el = document.getElementById('countdown');
    const t = setInterval(() => {
        if (r-- <= 0) { clearInterval(t); location.reload(); return; }
        el.textContent = String(Math.floor(r/60)).padStart(2,'0') + ':' + String(r%60).padStart(2,'0');
    }, 1000);
})();
@endif
</script>
@endpush
@endsection