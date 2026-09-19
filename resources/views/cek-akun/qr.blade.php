@extends('layouts.app')

@section('title', 'Cek Akun Barcode / QR – UNICODE')

@include('cek-akun.partials.styles')

@section('content')
<div class="account-check-page py-6 md:py-10">
    <div class="check-shell">

        {{-- BREADCRUMB --}}
        <nav class="flex items-center gap-2 text-sm text-slate-400 mb-5">
            <a href="{{ route('account.check') }}" class="breadcrumb-link hover:text-cyan-300 transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Cek Akun
            </a>
            <span class="text-slate-600">/</span>
            <span class="text-cyan-300 font-bold">Barcode / QR</span>
        </nav>

        {{-- HEADER --}}
        <section class="glass-panel rounded-3xl p-5 md:p-8 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-300 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs uppercase tracking-[.25em] text-cyan-300 font-bold mb-1">METHOD 02</div>
                    <h1 class="text-2xl md:text-3xl font-black text-white">Cek Akun Barcode / QR</h1>
                    <p class="text-sm text-slate-400 mt-2 leading-6">
                        Pilih salah satu cara: masukkan UID secara manual, scan dengan kamera, atau upload gambar.
                    </p>
                </div>
            </div>
        </section>

        {{-- 3 SUB-METHOD GRID --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- 1. Manual UID --}}
            <article class="glass-panel rounded-3xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-300 flex items-center justify-center">
                        <i class="fa-solid fa-keyboard"></i>
                    </div>
                    <div>
                        <div class="text-white font-extrabold">Input UID Manual</div>
                        <div class="text-xs text-slate-500">Ketik UID / token QR</div>
                    </div>
                </div>
                <input type="text" id="barcodeManualValue" class="field" autocomplete="off" placeholder="Masukkan UID / token QR">
                <button type="button" id="barcodeManualBtn" class="primary-btn w-full mt-3">
                    <i class="fa-solid fa-barcode mr-2"></i> Cek UID Barcode
                </button>
            </article>

            {{-- 2. Kamera --}}
            <article class="glass-panel rounded-3xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-300 flex items-center justify-center">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <div class="text-white font-extrabold">Scan dengan Kamera</div>
                        <div class="text-xs text-slate-500">Arahkan ke QR / barcode</div>
                    </div>
                </div>
                <button type="button" id="startCameraBtn" class="primary-btn w-full">
                    <i class="fa-solid fa-camera mr-2"></i> Aktifkan Kamera
                </button>
                <button type="button" id="stopCameraBtn" class="danger-btn w-full mt-2 hidden">
                    <i class="fa-solid fa-xmark mr-2"></i> Tutup Kamera
                </button>
                <p class="text-[11px] leading-5 text-slate-500 mt-3">
                    Browser akan memakai kamera belakang bila tersedia.
                </p>
            </article>

            {{-- 3. Upload --}}
            <article class="glass-panel rounded-3xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-300 flex items-center justify-center">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <div>
                        <div class="text-white font-extrabold">Upload Gambar</div>
                        <div class="text-xs text-slate-500">QR / barcode dari file</div>
                    </div>
                </div>
                <label class="primary-btn w-full text-center cursor-pointer block">
                    <i class="fa-solid fa-upload mr-2"></i> Pilih Gambar
                    <input type="file" id="barcodeUpload" class="hidden" accept="image/*">
                </label>
                <p class="text-[11px] leading-5 text-slate-500 mt-3">
                    Gambar tidak dikirim ke server — QR didecode di browser.
                </p>
            </article>

        </section>

        {{-- KAMERA PREVIEW --}}
        <section class="mt-5">
            <div id="cameraBox" class="camera-preview glass-panel relative rounded-3xl overflow-hidden hidden">
                <video id="barcodeVideo" class="w-full h-[320px] object-cover" muted playsinline></video>
                <div class="scan-corners absolute inset-0 pointer-events-none"></div>
                <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center">
                    <span class="px-3 py-1.5 rounded-full bg-black/60 backdrop-blur text-white text-xs">
                        Arahkan ke QR / Barcode
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

    $('barcodeManualBtn')?.addEventListener('click', () => window.AccountCheck.resolve($('barcodeManualValue').value, 'barcode'));
    $('barcodeManualValue')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') window.AccountCheck.resolve($('barcodeManualValue').value, 'barcode');
    });

    $('startCameraBtn')?.addEventListener('click', async () => {
        await window.AccountCheck.startCamera();
        $('startCameraBtn')?.classList.add('hidden');
        $('stopCameraBtn')?.classList.remove('hidden');
    });

    $('stopCameraBtn')?.addEventListener('click', async () => {
        await window.AccountCheck.stopCamera();
        $('startCameraBtn')?.classList.remove('hidden');
        $('stopCameraBtn')?.classList.add('hidden');
    });

    $('barcodeUpload')?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (file) window.AccountCheck.decodeUploadedImage(file);
        event.target.value = '';
    });
});
</script>
@endpush