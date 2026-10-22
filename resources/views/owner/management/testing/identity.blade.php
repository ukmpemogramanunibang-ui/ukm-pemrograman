@extends('layouts.roles.owner')

@section('title', 'Identity Testing – Owner UNICODE')
@section('page-title', 'Identity Test')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-user-check text-purple-600 mr-2"></i> Identity Test
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Uji hubungan antara credential UUID dengan identitas user.</p>
            </div>
            <a href="{{ route('owner.management.testing.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5 border border-gray-200 dark:border-gray-600">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan Credential UUID</label>
            <div class="flex gap-2">
                <input type="text" id="identityInput" placeholder="00d4b669-fcb4-45c7-bfca-28220fa6316d" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-purple-500">
                <button onclick="testIdentity()" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search mr-1"></i> Test
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">Masukkan UUID dari credential QR (bisa ditemukan di halaman detail QR).</p>
        </div>

        {{-- Hasil --}}
        <div id="identityResult" class="mt-6"></div>
    </div>
</div>

@push('scripts')
<script>
    function testIdentity() {
        const credential = document.getElementById('identityInput').value.trim();
        if (!credential) {
            alert('Masukkan UUID credential.');
            return;
        }

        const resultDiv = document.getElementById('identityResult');
        resultDiv.innerHTML = `<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-purple-500 text-2xl"></i><p class="text-gray-500 mt-2">Memproses...</p></div>`;

        fetch('{{ route("owner.management.testing.identity.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ credential: credential })
        })
        .then(response => response.json())
        .then(data => {
            if (data.found) {
                resultDiv.innerHTML = `
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-2xl">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-green-700 dark:text-green-300">✓ Identity Terverifikasi</h4>
                                <p class="text-sm text-green-600 dark:text-green-400">Credential terhubung dengan user</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Credential</h5>
                                <p><span class="text-gray-500">UUID:</span> ${data.credential.uuid}</p>
                                <p><span class="text-gray-500">Token:</span> ${data.credential.token.substring(0, 20)}...</p>
                                <p><span class="text-gray-500">Status:</span> ${data.credential.is_active ? '✅ Aktif' : '❌ Tidak Aktif'}</p>
                                <p><span class="text-gray-500">Revoked:</span> ${data.credential.is_revoked ? 'Ya' : 'Tidak'}</p>
                                <p><span class="text-gray-500">Dibuat:</span> ${data.credential.generated_at || '-'}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">User</h5>
                                <p><span class="text-gray-500">Nama:</span> ${data.user.name}</p>
                                <p><span class="text-gray-500">NIM:</span> ${data.user.nim || '-'}</p>
                                <p><span class="text-gray-500">Email:</span> ${data.user.email || '-'}</p>
                                <p><span class="text-gray-500">Member Number:</span> ${data.user.member_number || '-'}</p>
                                <p><span class="text-gray-500">Role:</span> ${data.user.role}</p>
                                <p><span class="text-gray-500">Status:</span> ${data.user.is_active ? '✅ Aktif' : '❌ Tidak Aktif'}</p>
                                ${data.user.jabatans ? `<p><span class="text-gray-500">Jabatan:</span> ${data.user.jabatans.join(', ') || '-'}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center text-white text-2xl">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-red-700 dark:text-red-300">✗ Tidak Ditemukan</h4>
                                <p class="text-sm text-red-600 dark:text-red-400">${data.message}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5">
                    <p class="text-red-600 dark:text-red-400">Error: ${error.message}</p>
                </div>
            `;
        });
    }
</script>
@endpush
@endsection