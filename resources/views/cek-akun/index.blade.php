@extends('layouts.app')

@section('title', 'Cek Akun – Pilih Metode | UNICODE')

@include('cek-akun.partials.styles')

@section('content')
<div class="account-check-page py-6 md:py-10">
    <div class="check-shell">

        {{-- HEADER --}}
        <section class="glass-panel rounded-3xl p-6 md:p-10 mb-6 text-center">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fa-solid fa-user-check text-2xl"></i>
                </div>
            </div>
            <div class="text-xs uppercase tracking-[.3em] text-cyan-300 font-bold mb-2">UNICODE • PUBLIC IDENTITY</div>
            <h1 class="text-3xl md:text-5xl font-black text-white mb-3">Cek Akun Anda</h1>
            <p class="max-w-2xl mx-auto text-sm md:text-base text-slate-300 leading-7">
                Pilih salah satu metode di bawah ini untuk memeriksa status akun UNICODE Anda —
                <span class="text-cyan-300 font-semibold">tanpa perlu login</span>.
            </p>

            <div class="flex flex-wrap justify-center gap-2 mt-5">
                <div class="px-3 py-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 text-emerald-300 text-xs font-bold">● Tanpa Login</div>
                <div class="px-3 py-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 text-cyan-300 text-xs font-bold">● Public Check</div>
                <div class="px-3 py-2 rounded-full border border-violet-400/20 bg-violet-400/10 text-violet-300 text-xs font-bold">● Smart Resolver</div>
            </div>
        </section>

        {{-- METHOD SELECTION --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-6">

            {{-- MANUAL --}}
            <a href="{{ route('account.check.manual') }}" class="method-card glass-panel rounded-3xl p-7 block">
                <div class="method-icon bg-blue-500/10 text-blue-300">
                    <i class="fa-solid fa-keyboard"></i>
                </div>
                <div class="text-xs uppercase tracking-widest text-blue-300/70 font-bold mb-1">Method 01</div>
                <h2 class="text-xl font-black text-white mb-2">Manual</h2>
                <p class="text-sm text-slate-400 leading-6 mb-5">
                    Masukkan NIM, Member Number, atau UUID secara manual untuk memeriksa akun.
                </p>
                <div class="inline-flex items-center gap-2 text-cyan-300 text-sm font-bold">
                    Buka Manual <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            {{-- BARCODE / QR --}}
            <a href="{{ route('account.check.qr') }}" class="method-card glass-panel rounded-3xl p-7 block">
                <div class="method-icon bg-cyan-500/10 text-cyan-300">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div class="text-xs uppercase tracking-widest text-cyan-300/70 font-bold mb-1">Method 02</div>
                <h2 class="text-xl font-black text-white mb-2">Barcode / QR</h2>
                <p class="text-sm text-slate-400 leading-6 mb-5">
                    Scan barcode/QR dengan kamera, upload gambar, atau masukkan UID secara manual.
                </p>
                <div class="inline-flex items-center gap-2 text-cyan-300 text-sm font-bold">
                    Buka Barcode / QR <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            {{-- NFC --}}
            <a href="{{ route('account.check.nfc') }}" class="method-card glass-panel rounded-3xl p-7 block">
                <div class="method-icon bg-violet-500/10 text-violet-300">
                    <i class="fa-solid fa-wifi"></i>
                </div>
                <div class="text-xs uppercase tracking-widest text-violet-300/70 font-bold mb-1">Method 03</div>
                <h2 class="text-xl font-black text-white mb-2">NFC</h2>
                <p class="text-sm text-slate-400 leading-6 mb-5">
                    Tap kartu NFC ke ponsel atau masukkan UID NFC secara manual.
                </p>
                <div class="inline-flex items-center gap-2 text-cyan-300 text-sm font-bold">
                    Buka NFC <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

        </section>

        {{-- INFO FOOTER --}}
        <section class="mt-6 glass-panel rounded-3xl p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="smart-line pl-3">
                    <div class="text-white font-bold">Smart Resolver</div>
                    <p class="text-slate-500 text-xs mt-1 leading-5">Semua metode memakai satu mesin pencarian agar hasil konsisten.</p>
                </div>
                <div class="smart-line pl-3">
                    <div class="text-white font-bold">Anti-Enumerasi</div>
                    <p class="text-slate-500 text-xs mt-1 leading-5">Sistem memakai exact identifier, tidak pencarian nama bebas.</p>
                </div>
                <div class="smart-line pl-3">
                    <div class="text-white font-bold">Tanpa Login</div>
                    <p class="text-slate-500 text-xs mt-1 leading-5">Dirancang untuk pengecekan identitas tanpa autentikasi.</p>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection