@extends('layouts.roles.owner')

@section('title', 'Sistem Penyelamatan QR – Owner UNICODE')
@section('page-title', 'Sistem Penyelamatan QR Code')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-shield-alt text-cyan-600 mr-2"></i>
                    Sistem Penyelamatan QR
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pulihkan QR resmi akun UKM Programming menggunakan QR lama/tercetak.
                </p>
            </div>
            <a href="{{ route('owner.management.qr.index') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Upload Area --}}
        <div class="rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-8 text-center mb-6 hover:border-cyan-500 transition" id="dropzone">
            <form id="recoveryForm" method="POST" action="{{ route('owner.management.qr.recovery.submit') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="files[]" id="qrFiles" multiple accept=".png,.jpg,.jpeg,.webp,.pdf" class="hidden">
                <label for="qrFiles" class="cursor-pointer block">
                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Klik atau seret file QR di sini</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Format: PNG, JPG, JPEG, WEBP, PDF (maks 10MB)</p>
                </label>
                <div id="fileList" class="mt-4 text-left"></div>
                <input type="hidden" name="decoded_payloads" id="decodedPayloads" value="">
            </form>
        </div>

        {{-- Preview Results --}}
        <div id="previewSection" class="hidden mt-6">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                <i class="fas fa-search text-cyan-600 mr-2"></i> Hasil Verifikasi QR
            </h4>
            <div id="previewGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"></div>
        </div>

        {{-- Submit Button --}}
        <div id="submitSection" class="hidden mt-6 p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl border border-cyan-200 dark:border-cyan-800">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        QR siap dipulihkan
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tekan tombol di bawah untuk memulai pemulihan.
                    </p>
                </div>
                <button type="submit" form="recoveryForm" id="submitRecoveryBtn"
                        class="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-undo-alt mr-2"></i> Pulihkan QR
                </button>
            </div>
        </div>

        {{-- Informasi --}}
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 text-center">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
            <p class="text-gray-700 dark:text-gray-300">
                Sistem ini memulihkan akun dan QR Code yang hilang karena reset database.
                QR harus memiliki signature resmi dari UKM Programming.
            </p>
        </div>
    </div>
</div>

{{-- JS untuk membaca QR dan preview --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('qrFiles');
    const fileList = document.getElementById('fileList');
    const previewSection = document.getElementById('previewSection');
    const previewGrid = document.getElementById('previewGrid');
    const submitSection = document.getElementById('submitSection');
    const submitBtn = document.getElementById('submitRecoveryBtn');
    const form = document.getElementById('recoveryForm');
    const decodedPayloadsInput = document.getElementById('decodedPayloads');

    let verifiedCount = 0;
    let totalFiles = 0;
    let decodedPayloads = [];

    // Drag & Drop support
    const dropzone = document.getElementById('dropzone');
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-cyan-500', 'bg-cyan-50/20');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-cyan-500', 'bg-cyan-50/20');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-cyan-500', 'bg-cyan-50/20');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    fileInput.addEventListener('change', async function() {
        const files = Array.from(this.files || []);
        totalFiles = files.length;
        verifiedCount = 0;
        decodedPayloads = [];
        previewGrid.innerHTML = '';
        fileList.innerHTML = '';
        submitSection.classList.add('hidden');
        submitBtn.disabled = true;

        if (files.length === 0) {
            previewSection.classList.add('hidden');
            return;
        }

        previewSection.classList.remove('hidden');

        // Tampilkan daftar file
        files.forEach(file => {
            const div = document.createElement('div');
            div.className = 'text-sm text-gray-600 dark:text-gray-300';
            div.textContent = `📄 ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            fileList.appendChild(div);
        });

        // Proses setiap file
        for (const file of files) {
            await processFile(file);
        }

        // Jika semua berhasil, aktifkan submit
        if (verifiedCount === totalFiles && totalFiles > 0) {
            submitSection.classList.remove('hidden');
            submitBtn.disabled = false;
            // Simpan semua decoded payloads ke hidden input
            decodedPayloadsInput.value = JSON.stringify(decodedPayloads);
        }
    });

    async function processFile(file) {
        const card = document.createElement('div');
        card.className = 'bg-gray-50 dark:bg-gray-700 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-600';
        card.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">${file.name}</span>
                <span class="status-badge px-2 py-1 rounded-full bg-gray-200 text-gray-600 text-xs">Memproses...</span>
            </div>
            <div class="qr-image-box aspect-square bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden mb-2">
                <span class="text-sm text-gray-400">Membaca QR...</span>
            </div>
            <div class="qr-result text-sm space-y-1"></div>
        `;
        previewGrid.appendChild(card);

        const badge = card.querySelector('.status-badge');
        const imageBox = card.querySelector('.qr-image-box');
        const result = card.querySelector('.qr-result');

        try {
            let decoded = null;

            // Baca QR dari file (gambar atau PDF)
            if (file.type === 'application/pdf') {
                // Untuk PDF, kita perlu render dengan pdf.js (belum diimplementasi)
                // Sementara kita lewati
                badge.textContent = 'PDF tidak didukung';
                badge.className = 'status-badge px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs';
                return;
            }

            // Decode gambar
            const image = new Image();
            image.src = URL.createObjectURL(file);
            await new Promise((resolve, reject) => {
                image.onload = resolve;
                image.onerror = reject;
            });

            const canvas = document.createElement('canvas');
            canvas.width = image.naturalWidth;
            canvas.height = image.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(image, 0, 0);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            decoded = code ? code.data : null;

            // Tampilkan gambar di card
            imageBox.innerHTML = `<img src="${image.src}" class="w-full h-full object-contain" alt="QR">`;

            if (!decoded) {
                badge.textContent = 'Tidak terbaca';
                badge.className = 'status-badge px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs';
                result.innerHTML = `<span class="text-red-600">QR tidak dapat dibaca.</span>`;
                return;
            }

            // Kirim ke server untuk verifikasi
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('file', file);
            formData.append('decoded_payload', decoded);

            const response = await fetch('{{ route("owner.management.qr.recovery.preview") }}', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!data.success) {
                badge.textContent = 'Ditolak';
                badge.className = 'status-badge px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs';
                result.innerHTML = `<span class="text-red-600">${data.message}</span>`;
                return;
            }

            // Berhasil
            badge.textContent = 'Valid ✓';
            badge.className = 'status-badge px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs';

            const user = data.user;
            result.innerHTML = `
                <div class="grid grid-cols-2 gap-x-2">
                    <div><span class="text-gray-500">Nama</span><br><strong>${user.name}</strong></div>
                    <div><span class="text-gray-500">NIM</span><br><strong>${user.nim || '-'}</strong></div>
                    <div><span class="text-gray-500">Role</span><br><strong>${user.role || '-'}</strong></div>
                    <div><span class="text-gray-500">Status</span><br><strong>${data.recovery_status}</strong></div>
                </div>
            `;

            // Simpan decoded payload untuk submit
            decodedPayloads.push(decoded);
            verifiedCount++;

        } catch (err) {
            badge.textContent = 'Error';
            badge.className = 'status-badge px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs';
            result.innerHTML = `<span class="text-red-600">${err.message}</span>`;
        }
    }

    // Submit form
    submitBtn.addEventListener('click', function(e) {
        if (decodedPayloads.length === 0) {
            e.preventDefault();
            alert('Tidak ada QR yang valid untuk dipulihkan.');
        }
    });
});
</script>

<style>
    #dropzone {
        transition: border-color 0.3s, background-color 0.3s;
    }
    .status-badge {
        font-weight: 600;
        letter-spacing: 0.025em;
    }
</style>
@endsection