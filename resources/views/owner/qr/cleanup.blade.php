@extends('layouts.roles.owner')

@section('title', 'Pembersihan QR Corrupt – Owner UNICODE')

@section('content')
<div class="p-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                <i class="fas fa-broom text-cyan-600 mr-2"></i> Pembersihan File QR Corrupt
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Sistem akan mendeteksi file PNG QR yang <strong>tidak dapat dibuka (corrupt)</strong> di <code>storage/app/public/qr/</code>.
                Hanya Owner yang dapat menghapus. Diperlukan PIN khusus dan alasan.
            </p>

            @if(count($corruptedFiles) === 0)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-green-700 dark:text-green-300">
                    <i class="fas fa-check-circle mr-2"></i> Tidak ditemukan file QR yang corrupt. Sistem Anda aman.
                </div>
            @else
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                    <p class="font-bold text-red-700 dark:text-red-300">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Ditemukan {{ count($corruptedFiles) }} file QR yang corrupt!
                    </p>
                    <p class="text-sm text-red-600 dark:text-red-300 mt-1">File-file ini tidak bisa di-download atau di-scan. Segera hapus.</p>
                </div>

                <form id="cleanupForm" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div class="overflow-x-auto border dark:border-gray-700 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-2 w-10">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600">
                                    </th>
                                    <th class="px-4 py-2">Nama File</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($corruptedFiles as $file)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="files[]" value="{{ $file }}" class="file-checkbox rounded border-gray-300 dark:border-gray-600">
                                    </td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $file }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full text-xs">
                                            <i class="fas fa-times-circle mr-1"></i> Corrupt
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Alasan Penghapusan <span class="text-red-500">*</span></label>
                            <textarea name="reason" id="reason" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-red-500 focus:border-red-500" placeholder="Contoh: File QR rusak saat generate, tidak bisa di-scan..." required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">PIN Keamanan <span class="text-red-500">*</span></label>
                            <input type="password" name="pin" id="pin" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-red-500 focus:border-red-500" placeholder="Masukkan PIN Khusus" required>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hanya pemilik yang mengetahui PIN ini.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="deleteSelected()" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus File yang Dipilih
                        </button>
                        <button type="button" onclick="deleteAll()" class="px-6 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg shadow-md transition">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Semua File Corrupt
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    // Select all logic
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.file-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function deleteSelected() {
        const checked = document.querySelectorAll('.file-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu file yang ingin dihapus.');
            return;
        }
        submitCleanup(Array.from(checked).map(cb => cb.value));
    }

    function deleteAll() {
        const allFiles = document.querySelectorAll('.file-checkbox');
        if (allFiles.length === 0) {
            alert('Tidak ada file corrupt untuk dihapus.');
            return;
        }
        const values = Array.from(allFiles).map(cb => cb.value);
        submitCleanup(values);
    }

    function submitCleanup(files) {
        const pin = document.getElementById('pin').value;
        const reason = document.getElementById('reason').value;

        if (!pin) {
            alert('PIN keamanan wajib diisi.');
            return;
        }
        if (!reason || reason.length < 5) {
            alert('Alasan penghapusan wajib diisi minimal 5 karakter.');
            return;
        }

        if (!confirm('PERINGATAN! Anda akan menghapus ' + files.length + ' file QR corrupt. Pastikan Anda yakin dan file benar-benar rusak. Lanjutkan?')) {
            return;
        }

        fetch('{{ route("owner.qr-cleanup.destroy") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                pin: pin,
                reason: reason,
                files: files
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Gagal: ' + data.error);
            } else {
                alert(data.success);
                location.reload();
            }
        })
        .catch(err => alert('Terjadi kesalahan server: ' + err.message));
    }
</script>
@endsection