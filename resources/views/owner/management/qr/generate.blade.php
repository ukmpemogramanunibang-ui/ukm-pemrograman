@extends('layouts.roles.owner')

@section('title', 'Generate QR Code – Owner UNICODE')
@section('page-title', 'Generate QR Credential (Massal)')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-qrcode text-cyan-600 mr-2"></i> Generate QR Code
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih satu atau lebih user untuk dibuatkan QR credential.</p>
            </div>
            <a href="{{ route('owner.management.qr.index') }}"
               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- FORM UTAMA --}}
        <form action="{{ route('owner.management.qr.generate.store') }}" method="POST" id="generateForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: DAFTAR USER --}}
                <div class="lg:col-span-2 space-y-4">
                    {{-- Pencarian --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari User</label>
                        <div class="relative">
                            <input type="text" id="searchUser"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 pl-10 focus:ring-2 focus:ring-cyan focus:border-transparent"
                                   placeholder="Ketik nama, NIM, atau email..."
                                   autocomplete="off">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Filter Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Role</label>
                        <select id="filterRole"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Daftar User dengan Checkbox --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih User</label>
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-y-auto max-h-80">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 w-10">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                                        </th>
                                        <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Nama</th>
                                        <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 hidden md:table-cell">NIM</th>
                                        <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 hidden sm:table-cell">Role</th>
                                        <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Status QR</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @forelse($users as $user)
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition user-row"
                                            data-name="{{ strtolower($user->name) }}"
                                            data-nim="{{ strtolower($user->nim ?? '') }}"
                                            data-email="{{ strtolower($user->email ?? '') }}"
                                            data-role="{{ $user->level_id }}"
                                            data-user-id="{{ $user->id }}">
                                            <td class="px-4 py-2">
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                       class="user-checkbox rounded border-gray-300 text-cyan-600 focus:ring-cyan-500"
                                                       data-has-qr="{{ $user->qrCodes->where('is_active', true)->count() > 0 ? 'yes' : 'no' }}"
                                                       data-name="{{ $user->name }}"
                                                       data-nim="{{ $user->nim ?? '-' }}"
                                                       data-role="{{ $user->level->name ?? 'N/A' }}">
                                            </td>
                                            <td class="px-4 py-2 font-medium text-gray-800 dark:text-white">
                                                {{ $user->name }}
                                                @if($user->qrCodes->where('is_active', true)->count() > 0)
                                                    <span class="ml-2 inline-block px-2 py-0.5 text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">
                                                        <i class="fas fa-check-circle"></i> Ada QR
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $user->nim ?? '-' }}</td>
                                            <td class="px-4 py-2 hidden sm:table-cell">
                                                <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs">
                                                    {{ $user->level->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">
                                                @php $qr = $user->qrCodes->where('is_active', true)->first(); @endphp
                                                @if($qr)
                                                    <span class="text-green-600 dark:text-green-400 text-sm">
                                                        <i class="fas fa-check-circle"></i> Aktif
                                                        @if($qr->expired_at)
                                                            <span class="text-xs text-gray-400">(exp {{ $qr->expired_at->format('d/m/Y') }})</span>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-sm"><i class="fas fa-minus-circle"></i> Belum</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada user ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
                            <span id="selectedCount">0 user dipilih</span>
                            <span id="selectedWithQrWarning" class="text-yellow-600 dark:text-yellow-400 hidden">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Beberapa sudah memiliki QR
                            </span>
                        </div>
                        <button type="button" onclick="clearSelection()" class="mt-1 text-xs text-red-500 hover:text-red-700">
                            <i class="fas fa-times-circle mr-1"></i> Hapus semua pilihan
                        </button>
                    </div>
                </div>

                {{-- KOLOM KANAN: KONFIGURASI & PREVIEW --}}
                <div class="lg:col-span-1 space-y-4">
                    {{-- Opsi Expired --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Masa Berlaku</label>
                        <input type="datetime-local" name="expired_at"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan = tidak terbatas</p>
                    </div>

                    {{-- Opsi Replace --}}
                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="replace" value="1" id="replaceCheck"
                                   class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                <i class="fas fa-sync-alt text-yellow-500 mr-1"></i> Replace jika sudah ada QR
                            </span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Akan menonaktifkan QR lama dan membuat yang baru (credential baru).</p>
                    </div>

                    {{-- Format Gambar QR --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Format Gambar QR</label>
                        <select name="format" id="formatSelect" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                            <option value="png">PNG (default)</option>
                            <option value="jpg">JPG</option>
                            <option value="jpeg">JPEG</option>
                            <option value="webp">WEBP</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Pilih format file gambar QR yang akan dihasilkan.</p>
                    </div>

                    {{-- Preview User Terpilih --}}
                    <div id="previewPanel" class="hidden bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600 max-h-60 overflow-y-auto">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <i class="fas fa-users text-cyan-500"></i> Preview Terpilih
                            <span id="previewCount" class="text-xs bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200 px-2 py-0.5 rounded-full"></span>
                        </h4>
                        <div id="previewContent">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih user untuk melihat preview</p>
                        </div>
                    </div>

                    {{-- Tombol Generate --}}
                    <button type="submit"
                            id="generateBtn"
                            class="w-full px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-qrcode mr-2"></i> <span id="generateBtnText">Generate QR</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- CARD KONFIRMASI (MODAL) --}}
        <div id="confirmCard" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-qrcode text-cyan-500"></i> Konfirmasi Generate QR
                </h3>
                <div id="confirmList" class="space-y-2 text-sm"></div>
                <div class="mt-4 p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg text-xs text-cyan-700 dark:text-cyan-300">
                    <i class="fas fa-info-circle mr-1"></i> Format: <span id="confirmFormat" class="font-bold">png</span>
                    &nbsp;|&nbsp; <span id="confirmReplaceStatus" class="font-bold">Tanpa replace</span>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeConfirm()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">Batal</button>
                    <button type="button" onclick="submitGenerate()" class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-check mr-2"></i> Ya, Generate!
                    </button>
                </div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <strong>Prinsip keamanan:</strong> QR Code hanya berisi credential token (bukan data user).
                    Data user diambil dari database saat scan, sehingga perubahan nama, NIM, role, poin, dll.
                    <strong>tidak memerlukan cetak ulang QR</strong>.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    QR yang dihasilkan memiliki signature digital dan status lifecycle (ACTIVE / BLOCKED / REVOKED / EXPIRED).
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- DOM References ----------
    const searchInput = document.getElementById('searchUser');
    const filterRole = document.getElementById('filterRole');
    const rows = document.querySelectorAll('.user-row');
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const selectedWithQrWarning = document.getElementById('selectedWithQrWarning');
    const previewPanel = document.getElementById('previewPanel');
    const previewContent = document.getElementById('previewContent');
    const previewCount = document.getElementById('previewCount');
    const generateBtn = document.getElementById('generateBtn');
    const generateBtnText = document.getElementById('generateBtnText');
    const replaceCheck = document.getElementById('replaceCheck');
    const formatSelect = document.getElementById('formatSelect');

    // ---------- Flag untuk mencegah loop ----------
    let isConfirmed = false;

    // ---------- Live Search & Filter ----------
    function filterRows() {
        const search = searchInput.value.toLowerCase().trim();
        const role = filterRole.value;
        rows.forEach(row => {
            const name = row.dataset.name || '';
            const nim = row.dataset.nim || '';
            const email = row.dataset.email || '';
            const rowRole = row.dataset.role || '';
            const matchSearch = name.includes(search) || nim.includes(search) || email.includes(search);
            const matchRole = role === '' || rowRole === role;
            row.style.display = (matchSearch && matchRole) ? '' : 'none';
        });
        updateUI();
    }

    searchInput.addEventListener('input', filterRows);
    filterRole.addEventListener('change', filterRows);

    // ---------- Select All ----------
    selectAll.addEventListener('change', function() {
        const visibleCheckboxes = document.querySelectorAll('.user-row:not([style*="display: none"]) .user-checkbox');
        visibleCheckboxes.forEach(cb => cb.checked = selectAll.checked);
        updateUI();
    });

    // ---------- Individual Checkbox Changes ----------
    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    // ---------- Clear Selection ----------
    window.clearSelection = function() {
        userCheckboxes.forEach(cb => cb.checked = false);
        updateUI();
    };

    // ---------- Update UI ----------
    function updateUI() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const count = checked.length;
        const hasQrSelected = Array.from(checked).some(cb => cb.dataset.hasQr === 'yes');

        selectedCount.textContent = count + ' user dipilih';
        if (hasQrSelected && count > 0) {
            selectedWithQrWarning.classList.remove('hidden');
        } else {
            selectedWithQrWarning.classList.add('hidden');
        }

        generateBtn.disabled = count === 0;
        generateBtnText.textContent = count > 0 ? `Generate QR untuk ${count} user` : 'Generate QR';

        updatePreview(checked);
    }

    // ---------- Preview ----------
    function updatePreview(checkedItems) {
        const count = checkedItems.length;
        if (count === 0) {
            previewPanel.classList.add('hidden');
            return;
        }

        previewPanel.classList.remove('hidden');
        previewCount.textContent = count + ' user';

        let html = '<div class="space-y-2">';
        checkedItems.forEach(cb => {
            const name = cb.dataset.name || 'Unknown';
            const nim = cb.dataset.nim || '-';
            const role = cb.dataset.role || 'N/A';
            const hasQr = cb.dataset.hasQr === 'yes';
            const statusIcon = hasQr ? '⚠️' : '✅';
            const statusColor = hasQr ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400';
            html += `
                <div class="flex items-center justify-between text-sm border-b border-gray-100 dark:border-gray-600 pb-1.5 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 text-white flex items-center justify-center text-xs font-bold">
                            ${name.charAt(0).toUpperCase()}
                        </span>
                        <span class="font-medium text-gray-800 dark:text-white">${name}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">(${nim})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200 rounded-full">${role}</span>
                        <span class="text-xs ${statusColor}">${statusIcon} ${hasQr ? 'Ada QR' : 'Belum'}</span>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        const hasQrSelected = Array.from(checkedItems).some(cb => cb.dataset.hasQr === 'yes');
        if (hasQrSelected) {
            html += `
                <div class="mt-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg text-xs text-yellow-700 dark:text-yellow-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Beberapa user sudah memiliki QR. Centang opsi <strong>"Replace"</strong> untuk mengganti.
                </div>
            `;
        }

        previewContent.innerHTML = html;
    }

    // ---------- Submit dengan Konfirmasi ----------
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        if (isConfirmed) return true;
        e.preventDefault();

        const checked = document.querySelectorAll('.user-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu user.');
            return;
        }

        const hasQrSelected = Array.from(checked).some(cb => cb.dataset.hasQr === 'yes');
        const replaceChecked = replaceCheck.checked;

        // Simpan data untuk konfirmasi
        window.selectedUsers = Array.from(checked).map(cb => ({
            id: cb.value,
            name: cb.dataset.name || 'Unknown',
            nim: cb.dataset.nim || '-',
            role: cb.dataset.role || 'N/A',
            hasQr: cb.dataset.hasQr === 'yes'
        }));
        window.selectedFormat = formatSelect.value;

        showConfirmCard(replaceChecked);
    });

    // ---------- Konfirmasi Card ----------
    function showConfirmCard(replaceChecked) {
        const list = document.getElementById('confirmList');
        list.innerHTML = window.selectedUsers.map(u => `
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="font-medium">${u.name}</span>
                <span class="text-xs text-gray-500">${u.nim} • ${u.role}</span>
                <span class="text-xs ${u.hasQr ? 'text-yellow-500' : 'text-green-500'}">${u.hasQr ? '⚠️ Ada QR' : '✅ Baru'}</span>
            </div>
        `).join('');

        document.getElementById('confirmFormat').textContent = window.selectedFormat.toUpperCase();
        document.getElementById('confirmReplaceStatus').textContent = replaceChecked ? 'Replace ON (mengganti QR lama)' : 'Tanpa replace (tambah QR baru)';
        document.getElementById('confirmCard').classList.remove('hidden');
    }

    window.closeConfirm = function() {
        document.getElementById('confirmCard').classList.add('hidden');
    };

    window.submitGenerate = function() {
        isConfirmed = true;
        document.getElementById('generateForm').submit();
    };

    // ---------- Inisialisasi ----------
    updateUI();
    generateBtn.disabled = true;

    // ---------- Keyboard Shortcut ----------
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'a') {
            const activeElement = document.activeElement;
            if (activeElement && activeElement.closest('.border.rounded-lg')) {
                e.preventDefault();
                const visibleCheckboxes = document.querySelectorAll('.user-row:not([style*="display: none"]) .user-checkbox');
                const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
                visibleCheckboxes.forEach(cb => cb.checked = !allChecked);
                updateUI();
            }
        }
    });
});
</script>
@endpush
@endsection