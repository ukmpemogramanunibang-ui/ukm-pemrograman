@extends('layouts.roles.owner')

@section('title', 'Testing QR Code – Owner')

@push('styles')
<style>
    .test-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        scroll-margin-top: 80px;
    }
    .test-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.08);
    }
    .result-valid {
        animation: pulseGreen 2s ease-in-out infinite;
    }
    @keyframes pulseGreen {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    }
    .result-invalid {
        animation: pulseRed 2s ease-in-out infinite;
    }
    @keyframes pulseRed {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }
    .scan-area {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        background: #000;
        min-height: 300px;
    }
    .scan-area video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .scan-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 3px solid rgba(6, 182, 212, 0.6);
        border-radius: 12px;
        box-shadow: 0 0 30px rgba(6, 182, 212, 0.3);
        pointer-events: none;
    }
    .scan-overlay::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        width: 20px;
        height: 20px;
        border-top: 4px solid #06b6d4;
        border-left: 4px solid #06b6d4;
        border-radius: 4px 0 0 0;
    }
    .scan-overlay::after {
        content: '';
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 20px;
        height: 20px;
        border-bottom: 4px solid #06b6d4;
        border-right: 4px solid #06b6d4;
        border-radius: 0 0 4px 0;
    }
    .fade-in {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    #scanner-container {
        width: 100%;
        min-height: 300px;
        background: #1a1a2e;
        border-radius: 16px;
        position: relative;
    }
    #scanner-container video {
        border-radius: 16px;
    }
    #scan-result-area {
        transition: all 0.3s ease;
    }
    .scan-status {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: #fff;
        padding: 6px 20px;
        border-radius: 30px;
        font-size: 13px;
        pointer-events: none;
        white-space: nowrap;
        z-index: 10;
        backdrop-filter: blur(4px);
    }
    .scan-status .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Dashboard</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.rooms.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Ruangan</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Testing QR Code</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <i class="fas fa-qrcode text-teal-500 text-2xl"></i>
                Testing QR Code
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Periksa QR sebelum digunakan atau dipublikasikan.
            </p>
        </div>
        <a href="{{ route('owner.rooms.index') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT: Scan QR --}}
        <div class="test-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                <i class="fas fa-camera text-purple-500"></i>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Scan QR Code</h2>
                <span class="ml-auto text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 px-3 py-0.5 rounded-full">Kamera</span>
            </div>
            <div class="p-6">
                <div class="scan-area">
                    <div id="scanner-container" class="relative w-full min-h-[300px] bg-gray-900 rounded-xl flex items-center justify-center">
                        {{-- HTML5 QR Code akan merender di sini --}}
                        <div id="scanner-placeholder" class="text-center text-gray-400">
                            <i class="fas fa-qrcode text-7xl mb-4 block opacity-30"></i>
                            <p class="text-sm">Klik "Mulai Scan" untuk mengaktifkan kamera</p>
                            <p class="text-xs text-gray-500 mt-1">QR akan terdeteksi secara otomatis</p>
                        </div>
                        <div class="scan-overlay hidden" id="scanOverlay"></div>
                        <div class="scan-status hidden" id="scanStatus">
                            <span class="spinner"></span> Memindai...
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button id="startScanBtn" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-play"></i> Mulai Scan
                    </button>
                    <button id="stopScanBtn" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2 hidden">
                        <i class="fas fa-stop"></i> Hentikan
                    </button>
                    <button id="switchCameraBtn" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-sync-alt"></i> Ganti Kamera
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-3">Hasil scan akan muncul di panel hasil di bawah</p>
            </div>
        </div>

        {{-- RIGHT: Manual Input --}}
        <div class="test-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center gap-2">
                <i class="fas fa-keyboard text-amber-500"></i>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Testing Manual</h2>
                <span class="ml-auto text-xs bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 px-3 py-0.5 rounded-full">Manual</span>
            </div>
            <div class="p-6">
                <form id="manualTestForm" action="{{ route('owner.rooms.testing.manual') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Input</label>
                            <div class="flex flex-wrap gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="radio" name="input_type" value="token" checked class="text-blue-600 focus:ring-blue-500">
                                    Token QR
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="radio" name="input_type" value="url" class="text-blue-600 focus:ring-blue-500">
                                    URL QR
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="radio" name="input_type" value="data" class="text-blue-600 focus:ring-blue-500">
                                    Data QR
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Masukkan Token/URL/Data</label>
                            <input type="text" name="input_value" id="manualInput" placeholder="Contoh: 8f91a2c3d4e5 atau https://..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-flask"></i> Test Sekarang
                        </button>
                    </div>
                </form>
                <p class="text-[10px] text-gray-400 mt-3">Masukkan token, URL lengkap, atau data QR untuk verifikasi</p>
            </div>
        </div>
    </div>

    {{-- HASIL TESTING (Real-time dari scan & manual) --}}
    <div id="scan-result-area" class="fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-indigo-500"></i>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Hasil Testing</h2>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400" id="resultTimestamp">{{ now()->format('H:i:s') }}</span>
            </div>
            <div class="p-6" id="resultContent">
                @if(isset($result))
                    @include('owner.rooms.partials.result', ['result' => $result])
                @else
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-qrcode text-5xl mb-3 block opacity-20"></i>
                        <p class="text-sm">Belum ada hasil testing.</p>
                        <p class="text-xs text-gray-500 mt-1">Scan QR atau masukkan token manual untuk verifikasi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ========== JAVASCRIPT ========== --}}
@push('scripts')
{{-- CDN html5-qrcode --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== SCANNER ==========
    let html5QrCode = null;
    let isScanning = false;
    let currentFacingMode = 'environment';
    let lastScannedToken = null;

    const scannerContainer = document.getElementById('scanner-container');
    const startBtn = document.getElementById('startScanBtn');
    const stopBtn = document.getElementById('stopScanBtn');
    const switchBtn = document.getElementById('switchCameraBtn');
    const scanStatus = document.getElementById('scanStatus');
    const scanOverlay = document.getElementById('scanOverlay');
    const placeholder = document.getElementById('scanner-placeholder');

    // Fungsi untuk menampilkan hasil di panel
    function displayResult(data) {
        const resultContent = document.getElementById('resultContent');
        const timestamp = document.getElementById('resultTimestamp');
        timestamp.textContent = new Date().toLocaleTimeString();

        if (data.valid) {
            // QR valid
            resultContent.innerHTML = `
                <div class="result-valid bg-green-50 dark:bg-green-900/20 border-2 border-green-500 rounded-xl p-6 text-center mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-green-700 dark:text-green-300">QR VALID</h3>
                    <p class="text-green-600 dark:text-green-400">QR berhasil diverifikasi oleh sistem</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">IDENTITAS QR</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">ID QR</span>
                                <span class="font-mono text-gray-800 dark:text-white">${data.qr_id || 'QR-ROOM-000123'}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Token</span>
                                <span class="font-mono text-gray-800 dark:text-white">${(data.token || '').substring(0, 8)}••••••••</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Jenis</span>
                                <span class="text-gray-800 dark:text-white">QR Ruangan</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-gray-500 dark:text-gray-400">Status QR</span>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">🟢 Aktif</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">DATA RUANGAN</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Nama</span>
                                <span class="text-gray-800 dark:text-white font-medium">${data.room.name}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Kode</span>
                                <span class="font-mono text-gray-800 dark:text-white">${data.room.code}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Gedung</span>
                                <span class="text-gray-800 dark:text-white">${data.room.building}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">Lantai</span>
                                <span class="text-gray-800 dark:text-white">${data.room.floor}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-gray-500 dark:text-gray-400">Kapasitas</span>
                                <span class="text-gray-800 dark:text-white">${data.room.capacity} orang</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">PUBLIKASI</h4>
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="px-3 py-1.5 rounded-full text-sm bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                            <i class="fas fa-globe mr-1"></i> Publik
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Mode: <span class="text-gray-700 dark:text-gray-300 font-medium">🌎 Tanpa Login</span></span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">LINK</h4>
                    <div class="flex flex-wrap items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                        <code class="text-sm text-gray-700 dark:text-gray-300 flex-1 break-all">https://domain/rooms/qr/${data.token}</code>
                        <button onclick="navigator.clipboard?.writeText(this.previousElementSibling.textContent)" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition">
                            <i class="fas fa-copy mr-1"></i> Salin
                        </button>
                        <a href="#" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg transition">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka
                        </a>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">PEMERIKSAAN</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> QR ditemukan
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> Token valid
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> Ruangan ditemukan
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> QR aktif
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> Publikasi aktif
                        </div>
                        <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i> Akses sesuai
                        </div>
                    </div>
                </div>
            `;
        } else {
            // QR invalid
            resultContent.innerHTML = `
                <div class="result-invalid bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-xl p-6 text-center">
                    <i class="fas fa-times-circle text-5xl text-red-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-red-700 dark:text-red-300">QR INVALID</h3>
                    <p class="text-red-600 dark:text-red-400">${data.message || 'QR tidak ditemukan di database.'}</p>
                </div>
                <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl">
                    <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Kemungkinan:</h4>
                    <ul class="list-disc list-inside text-sm text-yellow-700 dark:text-yellow-400 mt-2 space-y-1">
                        <li>QR rusak atau tidak terbaca</li>
                        <li>QR berasal dari sistem lain</li>
                        <li>QR sudah dihapus atau dinonaktifkan</li>
                        <li>Token tidak valid</li>
                    </ul>
                </div>
            `;
        }
        // Scroll ke hasil
        document.getElementById('scan-result-area').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Fungsi scan via AJAX
    async function verifyToken(token) {
        if (!token) return;
        if (token === lastScannedToken) return; // hindari duplikat
        lastScannedToken = token;

        try {
            const response = await fetch('{{ route("owner.rooms.testing.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: token })
            });
            const data = await response.json();
            displayResult(data);
        } catch (error) {
            console.error('Scan error:', error);
            displayResult({ valid: false, message: 'Gagal menghubungi server.' });
        }
    }

    // Inisialisasi scanner
    function initScanner(facingMode = 'environment') {
        if (html5QrCode) {
            html5QrCode.clear();
            html5QrCode = null;
        }

        html5QrCode = new Html5Qrcode('scanner-container');

        const config = {
            fps: 30,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ]
        };

        html5QrCode.start(
            { facingMode: facingMode },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            placeholder.classList.add('hidden');
            scanOverlay.classList.remove('hidden');
            scanStatus.classList.remove('hidden');
            scanStatus.innerHTML = '<span class="spinner"></span> Memindai...';
        }).catch(err => {
            alert('Gagal mengakses kamera: ' + err);
            stopScan();
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // decodedText = token atau URL
        console.log('QR detected:', decodedText);
        // Ekstrak token jika URL
        let token = decodedText;
        if (decodedText.includes('/room/scan/')) {
            const parts = decodedText.split('/');
            token = parts[parts.length - 1];
        }
        verifyToken(token);
        // Bisa tambahkan efek suara atau getaran
    }

    function onScanError(errorMessage) {
        // Abaikan error biasa
    }

    function stopScan() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                isScanning = false;
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                placeholder.classList.remove('hidden');
                scanOverlay.classList.add('hidden');
                scanStatus.classList.add('hidden');
                lastScannedToken = null;
            }).catch(err => console.warn('Stop error:', err));
        }
    }

    function switchCamera() {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        if (isScanning) {
            stopScan();
            setTimeout(() => initScanner(currentFacingMode), 500);
        }
    }

    // Event listeners
    startBtn.addEventListener('click', () => initScanner(currentFacingMode));
    stopBtn.addEventListener('click', stopScan);
    switchBtn.addEventListener('click', switchCamera);

    // ========== MANUAL INPUT (tetap menggunakan form) ==========
    const manualForm = document.getElementById('manualTestForm');
    manualForm.addEventListener('submit', function(e) {
        const input = document.getElementById('manualInput');
        if (!input.value.trim()) {
            e.preventDefault();
            alert('Masukkan token atau URL QR terlebih dahulu!');
        }
        // Form akan submit secara normal ke route manual, hasil akan di-render dari server
        // Tapi kita juga bisa override dengan AJAX jika mau, biarkan default
    });

    // ========== AUTO RESULT SCROLL ==========
    if (document.querySelector('.result-valid') || document.querySelector('.result-invalid')) {
        setTimeout(() => {
            document.getElementById('scan-result-area')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }
});
</script>
@endpush
@endsection