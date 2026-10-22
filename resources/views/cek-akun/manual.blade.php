@extends('layouts.app')

@section('title', 'Cek Akun Manual – UNICODE')

@include('cek-akun.partials.styles')

@section('content')
<div class="account-check-page py-6 md:py-10">
    <div class="check-shell">

        {{-- BREADCRUMB --}}
       <nav class="flex items-center gap-2 text-sm text-slate-400 mb-5">
    <a href="{{ route('account.check.index') }}" class="breadcrumb-link hover:text-cyan-300 transition">
        <i class="fa-solid fa-arrow-left mr-1"></i> Cek Akun
    </a>
    <span class="text-slate-600">/</span>
    <span class="text-cyan-300 font-bold">Manual</span>
</nav>

        {{-- HEADER --}}
        <section class="glass-panel rounded-3xl p-5 md:p-8 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 text-blue-300 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-keyboard"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs uppercase tracking-[.25em] text-blue-300 font-bold mb-1">METHOD 01</div>
                    <h1 class="text-2xl md:text-3xl font-black text-white">Cek Akun Manual</h1>
                    <p class="text-sm text-slate-400 mt-2 leading-6">
                        Masukkan NIM, Member Number, atau UUID untuk mencari akun UNICODE.
                    </p>
                </div>
            </div>
        </section>

        {{-- FORM --}}
        <section class="glass-panel rounded-3xl p-5 md:p-8">
            <div class="max-w-2xl mx-auto">
                <label for="manualValue" class="block text-xs uppercase tracking-widest text-slate-500 mb-2">
                    NIM / Member Number / UUID
                </label>
                <input
                    type="text"
                    id="manualValue"
                    class="field text-base"
                    autocomplete="off"
                    placeholder="Contoh: 2301xxxx atau UKM25xxxxxx atau 8f6e..."
                >
                <button
                    type="button"
                    id="manualCheckBtn"
                    class="primary-btn w-full mt-4 text-base py-3"
                >
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Cek Akun Sekarang
                </button>

               <div class="mt-4 p-3 rounded-xl border border-blue-400/10 bg-blue-400/5 text-[11px] leading-5 text-blue-200/80 text-center">
    <i class="fa-solid fa-shield-halved mr-1"></i>
    Pencarian menggunakan identifier yang tepat dan dilindungi pembatasan permintaan berlapis.
    Hasil pencarian manual menampilkan data terbatas.
    <span class="block mt-1 text-blue-300/70">
        Untuk verifikasi penuh, gunakan QR atau NFC.
    </span>
</div>
            </div>
        </section>

        {{-- HASIL --}}
        @include('cek-akun.partials.result-container')

    </div>
</div>
@endsection

@include('cek-akun.partials.resolver')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const $ = window.AccountCheck.$;
    $('manualCheckBtn')?.addEventListener('click', () => window.AccountCheck.resolve($('manualValue').value, 'manual'));
    $('manualValue')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') window.AccountCheck.resolve($('manualValue').value, 'manual');
    });
});
</script>
@endpush