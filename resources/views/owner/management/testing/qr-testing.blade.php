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
                <p class="text-sm text-gray-500 dark:text-gray-400">Uji QR Code dengan scanner, manual, atau upload file.</p>
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

        {{-- Card Upload QR --}}
        <div class="mt-6">
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center text-white text-2xl">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white">Card Upload QR Code</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Unggah hingga 10 file QR untuk validasi otomatis.</p>
                    </div>
                </div>

                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="file" name="files[]" multiple accept="image/png,image/jpeg,image/webp,image/svg+xml,application/pdf" 
                               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2" id="fileInput">
                        <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-upload mr-1"></i> Upload & Validasi
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Maks 10 file, format: PNG, JPG, JPEG, WEBP, SVG, PDF (max 5MB per file)</p>
                </form>

                <div id="uploadResult" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"></div>
            </div>
        </div>

        {{-- Area Hasil (Scanner/Manual) --}}
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
    // ... (kode JavaScript scanner/manual yang sudah ada)
    // Tambahkan JavaScript untuk upload di bawah ini:

    // Upload QR
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('uploadResult');
        resultContainer.innerHTML = '<div class="col-span-full text-center py-4">Memproses...</div>';

        fetch('{{ route("owner.management.testing.upload-qr") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            resultContainer.innerHTML = '';
            if (!data.results || data.results.length === 0) {
                resultContainer.innerHTML = '<div class="col-span-full text-center py-4 text-gray-500">Tidak ada file diproses.</div>';
                return;
            }
            data.results.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'bg-white dark:bg-gray-800 border rounded-lg p-4 shadow-sm';
                if (item.valid) {
                    const user = item.user || {};
                    card.innerHTML = `
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="font-semibold text-green-600">Valid</span>
                        </div>
                        <p class="text-sm font-semibold truncate" title="${item.filename}">${item.filename}</p>
                        <p class="text-xs text-gray-500">User: ${user.name || '-'}</p>
                        <p class="text-xs text-gray-500">NIM: ${user.nim || '-'}</p>
                        <p class="text-xs text-gray-500">Role: ${user.role || '-'}</p>
                    `;
                } else {
                    card.innerHTML = `
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="font-semibold text-red-600">Invalid</span>
                        </div>
                        <p class="text-sm font-semibold truncate" title="${item.filename}">${item.filename}</p>
                        <p class="text-xs text-gray-500">${item.message}</p>
                    `;
                }
                resultContainer.appendChild(card);
            });
        })
        .catch(error => {
            resultContainer.innerHTML = `<div class="col-span-full text-center text-red-500">Error: ${error.message}</div>`;
            console.error('Upload error:', error);
        });
    });
</script>
@endpush
@endsection