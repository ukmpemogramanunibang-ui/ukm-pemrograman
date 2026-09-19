@extends('layouts.roles.owner')

@section('title', 'Testing Center – Owner UNICODE')
@section('page-title', 'Testing Center')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
            <i class="fas fa-flask text-orange-500 mr-2"></i> Testing Center
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Pilih metode pengujian di bawah ini.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            {{-- QR Testing --}}
            <a href="{{ route('owner.management.testing.qr') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">QR Testing</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Scan / Manual</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Uji QR Code dengan scanner atau input manual.</p>
                <span class="inline-block mt-2 text-xs text-blue-600 dark:text-blue-400 group-hover:underline">Buka Testing →</span>
            </a>

            {{-- NIM Testing --}}
            <a href="{{ route('owner.management.testing.nim') }}" class="group bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border border-green-200 dark:border-green-800 hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">NIM Testing</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cari Akun</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Cari user berdasarkan NIM.</p>
                <span class="inline-block mt-2 text-xs text-green-600 dark:text-green-400 group-hover:underline">Buka Testing →</span>
            </a>

            {{-- Identity Testing --}}
            <a href="{{ route('owner.management.testing.identity') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-5 border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">Identity Test</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Credential → User</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Uji hubungan credential dengan identitas user.</p>
                <span class="inline-block mt-2 text-xs text-purple-600 dark:text-purple-400 group-hover:underline">Buka Testing →</span>
            </a>

            {{-- Validate --}}
            <a href="{{ route('owner.management.testing.validate') }}" class="group bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-5 border border-red-200 dark:border-red-800 hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">Validation</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Security Check</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Validasi keamanan credential secara mendalam.</p>
                <span class="inline-block mt-2 text-xs text-red-600 dark:text-red-400 group-hover:underline">Buka Testing →</span>
            </a>

            {{-- Card Upload QR (baru) --}}
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-white">Upload QR</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">File QR</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Unggah hingga 10 file QR untuk validasi otomatis.</p>
                <form id="uploadForm" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <input type="file" name="files[]" multiple accept="image/png,image/jpeg,image/webp,image/svg+xml,application/pdf" 
                           class="w-full text-xs text-gray-500 dark:text-gray-400 mb-2" id="fileInput">
                    <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-sm">
                        <i class="fas fa-upload mr-1"></i> Upload & Validasi
                    </button>
                </form>
                <div id="uploadResult" class="mt-3 grid grid-cols-1 gap-2 max-h-40 overflow-y-auto"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const resultContainer = document.getElementById('uploadResult');
        resultContainer.innerHTML = '<div class="text-center py-2 text-xs text-gray-500">Memproses...</div>';

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
                resultContainer.innerHTML = '<div class="text-center py-2 text-xs text-gray-500">Tidak ada file diproses.</div>';
                return;
            }
            data.results.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'bg-white dark:bg-gray-800 border rounded p-2 shadow-sm text-xs';
                if (item.valid) {
                    const user = item.user || {};
                    card.innerHTML = `
                        <div class="flex items-center gap-1 mb-1">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="font-semibold text-green-600">Valid</span>
                        </div>
                        <p class="truncate" title="${item.filename}">${item.filename}</p>
                        <p class="text-gray-500">User: ${user.name || '-'}</p>
                        <p class="text-gray-500">NIM: ${user.nim || '-'}</p>
                    `;
                } else {
                    card.innerHTML = `
                        <div class="flex items-center gap-1 mb-1">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="font-semibold text-red-600">Invalid</span>
                        </div>
                        <p class="truncate" title="${item.filename}">${item.filename}</p>
                        <p class="text-gray-500">${item.message}</p>
                    `;
                }
                resultContainer.appendChild(card);
            });
        })
        .catch(error => {
            resultContainer.innerHTML = `<div class="text-center text-red-500 text-xs">Error: ${error.message}</div>`;
            console.error('Upload error:', error);
        });
    });
</script>
@endpush
@endsection