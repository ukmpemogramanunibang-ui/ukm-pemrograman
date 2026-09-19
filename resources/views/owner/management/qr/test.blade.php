@extends('layouts.roles.owner')

@section('title', 'Test QR – Owner')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Uji Coba QR Code</h1>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <p class="text-gray-600 dark:text-gray-300">Scan QR menggunakan kamera atau masukkan kode credential untuk validasi.</p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Masukkan token QR</label>
                <input type="text" id="qrToken" placeholder="Contoh: UCQR|V1|UNICODE|..." class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2">
            </div>
            <div class="flex items-end">
                <button onclick="testQR()" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">
                    <i class="fas fa-search mr-2"></i> Validasi
                </button>
            </div>
        </div>
        <div id="result" class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg hidden"></div>
    </div>
</div>

@push('scripts')
<script>
    function testQR() {
        const token = document.getElementById('qrToken').value;
        if (!token) return alert('Masukkan token QR.');
        fetch('{{ route("owner.management.qr.test.validate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ token })
        })
        .then(res => res.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = data.valid ? 
                `<span class="text-green-600"><i class="fas fa-check-circle"></i> QR Valid: ${data.user?.name}</span>` :
                `<span class="text-red-600"><i class="fas fa-times-circle"></i> QR Tidak Valid: ${data.message}</span>`;
        })
        .catch(err => {
            alert('Error: ' + err.message);
        });
    }
</script>
@endpush
@endsection