@extends('layouts.app')

@section('title', 'Cek Akun NFC – UNICODE')

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
            <span class="text-cyan-300 font-bold">NFC</span>
        </nav>

        {{-- HEADER --}}
        <section class="glass-panel rounded-3xl p-5 md:p-8 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-violet-500/10 text-violet-300 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-wifi"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs uppercase tracking-[.25em] text-violet-300 font-bold mb-1">METHOD 03</div>
                    <h1 class="text-2xl md:text-3xl font-black text-white">Cek Akun NFC</h1>
                    <p class="text-sm text-slate-400 mt-2 leading-6">
                        Tap kartu NFC ke ponsel, atau masukkan UID NFC secara manual.
                    </p>
                </div>
            </div>
        </section>

        {{-- 2 SUB-METHOD GRID --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Tap NFC --}}
            <article class="glass-panel rounded-3xl p-5 md:p-7 text-center">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-violet-500/10 text-violet-300 flex items-center justify-center text-3xl mb-4">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h2 class="text-lg font-extrabold text-white mb-2">Tap Kartu NFC</h2>
                <p class="text-sm text-slate-400 leading-6 mb-5">
                    Aktifkan NFC ponsel Anda, lalu tap kartu ke area pembaca NFC.
                </p>

                <button type="button" id="startNfcBtn" class="primary-btn w-full">
                    <i class="fa-solid fa-satellite-dish mr-2"></i> Aktifkan Scanner NFC
                </button>

                <div id="nfcStatus" class="mt-4 rounded-2xl p-4 border border-slate-700/70 bg-slate-950/30 text-xs text-slate-400 text-left">
                    <div class="flex items-center gap-2">
                        <span class="status-dot bg-slate-600"></span>
                        <span>Belum ada pembacaan NFC.</span>
                    </div>
                </div>

                <p class="text-[11px] leading-5 text-slate-500 mt-3 text-left">
                    Web NFC membutuhkan HTTPS / localhost & browser yang mendukung
                    (Chrome Android). Jika tidak didukung, gunakan input UID manual.
                </p>
            </article>

            {{-- UID Manual --}}
            <article class="glass-panel rounded-3xl p-5 md:p-7">
                <div class="w-14 h-14 rounded-2xl bg-violet-500/10 text-violet-300 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h2 class="text-lg font-extrabold text-white mb-2">Input UID NFC Manual</h2>
                <p class="text-sm text-slate-400 leading-6 mb-4">
                    Masukkan UID NFC secara manual jika ponsel Anda tidak mendukung Web NFC.
                </p>

                <input type="text" id="nfcManualValue" class="field" autocomplete="off" placeholder="Contoh: 04:A2:3B:1C:5D:6E:80">
                <button type="button" id="nfcManualBtn" class="primary-btn w-full mt-3">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Cek UID NFC
                </button>
            </article>

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

    $('nfcManualBtn')?.addEventListener('click', () => window.AccountCheck.resolve($('nfcManualValue').value, 'nfc'));
    $('nfcManualValue')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') window.AccountCheck.resolve($('nfcManualValue').value, 'nfc');
    });

    $('startNfcBtn')?.addEventListener('click', () => window.AccountCheck.startNfc());
});
</script>
@endpush