@extends('layouts.roles.owner')

@section('title', 'QR Testing – Owner UNICODE')
@section('page-title', 'QR Code Testing')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-qrcode text-cyan-600 mr-2"></i> QR Code Testing
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Uji QR Code dengan scanner atau input manual.</p>
            </div>
            <a href="{{ route('owner.management.testing.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Pilihan Metode --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Scanner --}}
            <div id="scannerMethod" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 cursor-pointer hover:shadow-lg transition-all" onclick="activateScanner()">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">Scanner Kamera</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Gunakan kamera perangkat</p>
                    </div>
                </div>
                <button class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm" onclick="event.stopPropagation(); activateScanner()">
                    <i class="fas fa-play mr-1"></i> Mulai Scan
                </button>
            </div>

            {{-- Manual --}}
            <div id="manualMethod" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 cursor-pointer hover:shadow-lg transition-all" onclick="activateManual()">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">Manual Input</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Masukkan URL atau UUID</p>
                    </div>
                </div>
                <button class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm" onclick="event.stopPropagation(); activateManual()">
                    <i class="fas fa-pen mr-1"></i> Input Manual
                </button>
            </div>
        </div>

        {{-- Area Hasil --}}
        <div id="resultArea" class="hidden">
            <div id="resultContent"></div>
        </div>

        {{-- Area Scanner --}}
        <div id="scannerArea" class="hidden mt-4">
            <div class="bg-gray-900 rounded-xl p-4 text-center relative">
                <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                <p class="text-white text-sm mt-2" id="scanStatus">Menunggu QR Code...</p>
                <div class="flex justify-center gap-3 mt-3 flex-wrap">
                    <button onclick="stopScanner()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                        <i class="fas fa-stop mr-1"></i> Hentikan
                    </button>
                    <button onclick="refreshScanner()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-sm">
                        <i class="fas fa-sync mr-1"></i> Refresh Kamera
                    </button>
                    <button onclick="zoomIn()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-plus mr-1"></i> Zoom +
                    </button>
                    <button onclick="zoomOut()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-minus mr-1"></i> Zoom -
                    </button>
                    <button onclick="toggleTorch()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                        <i class="fas fa-lightbulb mr-1"></i> Lampu
                    </button>
                </div>
                <div id="scannerError" class="hidden text-red-400 text-sm mt-2 bg-red-500/10 border border-red-500/20 rounded-lg p-2"></div>
            </div>
        </div>

        {{-- Area Manual Input --}}
        <div id="manualArea" class="hidden mt-4">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan QR Payload / UUID</label>
                <div class="flex gap-2">
                    <input type="text" id="manualInput" placeholder="https://ukmprogramming-unibang.id/q/... atau UUID" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                    <button onclick="testManual()" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                        <i class="fas fa-search mr-1"></i> Test
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Contoh: https://ukmprogramming-unibang.id/q/00d4b669-fcb4-45c7-bfca-28220fa6316d</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // State variabel
    let html5QrCode = null;
    let isScannerStarted = false;
    let currentZoom = 2.0;
    let torchOn = false;
    let scanInProgress = false; // mencegah scan ganda

    // Fungsi aktivasi metode
    function activateScanner() {
        document.getElementById('scannerArea').classList.remove('hidden');
        document.getElementById('manualArea').classList.add('hidden');
        document.getElementById('resultArea').classList.add('hidden');
        document.getElementById('scannerError').classList.add('hidden');
        if (!isScannerStarted) {
            startScanner();
        }
    }

    function activateManual() {
        document.getElementById('manualArea').classList.remove('hidden');
        document.getElementById('scannerArea').classList.add('hidden');
        document.getElementById('resultArea').classList.add('hidden');
        stopScanner();
    }

    // Mulai scanner
    function startScanner() {
        if (html5QrCode) {
            html5QrCode.clear();
            html5QrCode = null;
        }
        const errorDiv = document.getElementById('scannerError');
        errorDiv.classList.add('hidden');

        // Inisialisasi Html5Qrcode
        html5QrCode = new Html5Qrcode("reader");

        // Tentukan ukuran qrbox responsif
        const qrboxSize = Math.min(window.innerWidth, 400) * 0.7;

        // Konfigurasi kamera
        const config = {
            fps: 10,
            qrbox: { width: qrboxSize, height: qrboxSize },
            aspectRatio: 1.0,
            videoConstraints: {
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 },
                advanced: [{ zoom: currentZoom }]
            }
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            isScannerStarted = true;
            document.getElementById('scanStatus').textContent = '✓ Kamera aktif — arahkan QR';
            // Coba aktifkan zoom & fokus otomatis
            applyZoom();
        }).catch(err => {
            console.error('Scanner start error:', err);
            isScannerStarted = false;
            document.getElementById('scanStatus').textContent = '❌ Gagal akses kamera';
            errorDiv.textContent = 'Error: ' + err.message + '. Coba refresh atau gunakan manual.';
            errorDiv.classList.remove('hidden');
        });
    }

    // Refresh scanner
    function refreshScanner() {
        stopScanner();
        setTimeout(() => startScanner(), 500);
    }

    // Berhenti scanner
    function stopScanner() {
        if (html5QrCode && isScannerStarted) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
                isScannerStarted = false;
                document.getElementById('scanStatus').textContent = 'Scanner dihentikan';
            }).catch(() => {});
        }
    }

    // Fungsi sukses scan
    function onScanSuccess(decodedText, decodedResult) {
        if (scanInProgress) return;
        scanInProgress = true;
        document.getElementById('scanStatus').textContent = '✓ QR Terdeteksi!';
        // Berhenti scanner setelah jeda singkat
        setTimeout(() => {
            stopScanner();
            validateQr(decodedText);
            // Reset flag setelah 2 detik untuk memungkinkan scan berikutnya
            setTimeout(() => scanInProgress = false, 2000);
        }, 300);
    }

    // Fungsi error scan (abaikan)
    function onScanError(err) {
        // ignore
    }

    // Fungsi zoom
    function zoomIn() {
        currentZoom = Math.min(currentZoom + 0.5, 5.0);
        applyZoom();
    }

    function zoomOut() {
        currentZoom = Math.max(currentZoom - 0.5, 1.0);
        applyZoom();
    }

    function applyZoom() {
        if (html5QrCode && isScannerStarted) {
            html5QrCode.applyVideoConstraints({
                facingMode: "environment",
                zoom: currentZoom
            }).then(() => {
                console.log('Zoom applied: ' + currentZoom);
            }).catch(err => {
                console.warn('Zoom failed:', err);
            });
        }
    }

    // Fungsi lampu senter
    function toggleTorch() {
        if (html5QrCode && isScannerStarted) {
            html5QrCode.applyVideoConstraints({
                facingMode: "environment",
                advanced: [{ torch: !torchOn }]
            }).then(() => {
                torchOn = !torchOn;
                console.log('Torch toggled: ' + torchOn);
            }).catch(err => {
                console.warn('Torch not supported:', err);
                alert('Perangkat tidak mendukung lampu senter.');
            });
        }
    }

    // Input manual
    function testManual() {
        const input = document.getElementById('manualInput').value.trim();
        if (!input) {
            alert('Masukkan URL atau UUID QR.');
            return;
        }
        validateQr(input);
    }

    // Validasi QR ke server
    function validateQr(input) {
        fetch('{{ route("owner.management.testing.qr.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payload: input })
        })
        .then(response => response.json())
        .then(data => {
            showResult(data);
        })
        .catch(error => {
            console.error('Validation error:', error);
            alert('Terjadi kesalahan saat validasi. Cek konsol untuk detail.');
        });
    }

    // Tampilkan hasil
    function showResult(data) {
        const area = document.getElementById('resultArea');
        const content = document.getElementById('resultContent');
        area.classList.remove('hidden');

        if (data.valid) {
            const user = data.user || data.profile || {};
            content.innerHTML = `
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-green-700 dark:text-green-300">✓ QR VALID</h4>
                            <p class="text-sm text-green-600 dark:text-green-400">Credential terverifikasi</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-500 dark:text-gray-400">User:</span> <span class="font-semibold">${user.name || '-'}</span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">NIM:</span> <span class="font-semibold">${user.nim || '-'}</span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Role:</span> <span class="font-semibold">${user.role || '-'}</span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Status QR:</span> <span class="font-semibold text-green-600">ACTIVE</span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Member No:</span> <span class="font-semibold">${user.member_number || '-'}</span></div>
                        <div><span class="text-gray-500 dark:text-gray-400">Jabatan:</span> <span class="font-semibold">${(user.jabatans || []).join(', ') || '-'}</span></div>
                    </div>
                    <pre class="mt-4 text-xs bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center text-white text-2xl">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-red-700 dark:text-red-300">✗ QR INVALID</h4>
                            <p class="text-sm text-red-600 dark:text-red-400">${data.message || 'Credential tidak valid'}</p>
                        </div>
                    </div>
                    <pre class="mt-4 text-xs bg-gray-100 dark:bg-gray-800 p-3 rounded-lg overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                </div>
            `;
        }
    }
</script>
@endpush
@endsection