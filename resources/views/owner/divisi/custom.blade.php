@extends('layouts.roles.owner')

@section('title', 'Custom Divisi')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.divisi.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-cog text-cyan-500"></i>
            {{ $division ? 'Edit Divisi' : 'Buat Divisi Baru' }}
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ $division ? route('owner.divisi.update', $division) : route('owner.divisi.store') }}" method="POST" id="divisionForm">
            @csrf
            @if($division) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Divisi</label>
                    <input type="text" name="name" value="{{ old('name', $division->name ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500">
                        <option value="aktif" {{ old('status', $division->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $division->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500">{{ old('description', $division->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maksimal Anggota (termasuk Ketua & Pengurus)</label>
                    <input type="number" name="max_members" value="{{ old('max_members', $division->max_members ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Label Ketua (opsional, misal: PJ, Koordinator)</label>
                    <input type="text" name="ketua_label" value="{{ old('ketua_label', $division->ketua_label ?? 'Ketua') }}" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" placeholder="Ketua">
                </div>
            </div>

            @if(!$division)
                {{-- Bagian untuk CREATE --}}
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Pilih Ketua Divisi (hanya satu)</h3>
                    <div class="mt-3">
                        <select name="ketua_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-rose-500" required>
                            <option value="">-- Pilih Ketua --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('ketua_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->level->name ?? '' }}) - {{ $user->nim ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hanya akun dengan role Ketua, Pengurus, atau Anggota UKM yang dapat dipilih.</p>
                    </div>
                </div>

                {{-- Bagian Pengurus Dinamis --}}
                <div class="mt-6" id="pengurus-wrapper">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 flex justify-between items-center">
                        <span>Pilih Pengurus (bisa lebih dari satu)</span>
                        <button type="button" id="add-pengurus-btn" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg transition">
                            <i class="fas fa-plus"></i> Tambah Pengurus
                        </button>
                    </h3>
                    <div id="pengurus-list" class="mt-3 space-y-3">
                        @if(old('pengurus_ids'))
                            @php
                                $oldPengurus = old('pengurus_ids');
                                $oldJabatans = old('pengurus_jabatan', []);
                                $oldUserIds = $oldPengurus ?? [];
                            @endphp
                            @foreach($oldPengurus as $index => $userId)
                                <div class="pengurus-item flex flex-wrap items-end gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Pilih Pengurus</label>
                                        <select name="pengurus_ids[]" class="pengurus-select w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm focus:ring-2 focus:ring-rose-500" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach($users as $user)
                                                @php
                                                    $disabled = in_array($user->id, $oldUserIds) && $user->id != $userId ? 'disabled' : '';
                                                    $selected = ($userId == $user->id) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $user->id }}" {{ $selected }} {{ $disabled }}>
                                                    {{ $user->name }} ({{ $user->level->name ?? '' }}) - {{ $user->nim ?? '' }}
                                                    @if($disabled) [Sudah dipilih] @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Jabatan Khusus (opsional)</label>
                                        <input type="text" name="pengurus_jabatan[]" value="{{ $oldJabatans[$index] ?? '' }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm focus:ring-2 focus:ring-rose-500" placeholder="Misal: Sekretaris">
                                    </div>
                                    <button type="button" class="remove-pengurus-btn px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Setiap pengurus dapat diberi jabatan khusus opsional.</p>
                    <p class="text-xs text-red-400 mt-1" id="pengurus-error" style="display: none;">Akun yang sama tidak boleh dipilih lebih dari satu sebagai pengurus.</p>
                </div>

                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <i class="fas fa-info-circle mr-1"></i>
                        Akun yang sudah menjadi anggota divisi lain <strong>tetap bisa</strong> dipilih sebagai pengurus atau ketua di divisi baru ini.
                    </p>
                </div>
            @else
                {{-- Bagian EDIT --}}
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/20 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-white">Informasi Divisi Saat Ini</h3>
                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        <p><strong>Ketua:</strong> {{ $division->members()->where('position', 'ketua')->first()->user->name ?? 'Tidak ada' }}</p>
                        <p><strong>Jumlah Pengurus:</strong> {{ $division->members()->where('position', 'pengurus')->count() }}</p>
                        <p><strong>Total Anggota:</strong> {{ $division->members()->count() }}</p>
                        <p><strong>Maksimal Anggota:</strong> {{ $division->max_members ?? 'Tidak dibatasi' }}</p>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Untuk mengubah keanggotaan, gunakan menu <a href="{{ route('owner.divisi.anggota', ['division_id' => $division->id]) }}" class="text-blue-500 hover:underline">Anggota Divisi</a>.</p>
                </div>
            @endif

            <div class="mt-8 flex gap-3">
                <button type="submit" class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg transition" id="submitBtn">
                    {{ $division ? 'Perbarui Divisi' : 'Simpan Divisi' }}
                </button>
                <a href="{{ route('owner.divisi.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@if(!$division)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pengurusList = document.getElementById('pengurus-list');
    const addBtn = document.getElementById('add-pengurus-btn');
    const errorEl = document.getElementById('pengurus-error');

    // Data user dari server (sudah disiapkan dari controller sebagai $userOptions)
    const users = @json($userOptions);

    // Fungsi untuk membuat dropdown options dengan disabled untuk yang sudah dipilih
    function createUserOptions(selectedId = null, existingIds = []) {
        let html = '<option value="">-- Pilih --</option>';
        users.forEach(user => {
            const isDisabled = existingIds.includes(user.id) && user.id !== selectedId;
            const selected = (selectedId == user.id) ? 'selected' : '';
            const disabledAttr = isDisabled ? 'disabled' : '';
            const label = isDisabled ? ` ${user.name} (${user.level}) - ${user.nim} [Sudah dipilih]` : `${user.name} (${user.level}) - ${user.nim}`;
            html += `<option value="${user.id}" ${selected} ${disabledAttr}>${label}</option>`;
        });
        return html;
    }

    // Fungsi untuk mendapatkan semua ID pengurus yang sudah dipilih
    function getSelectedPengurusIds() {
        const selects = document.querySelectorAll('.pengurus-select');
        const ids = [];
        selects.forEach(select => {
            if (select.value) ids.push(parseInt(select.value));
        });
        return ids;
    }

    // Fungsi untuk memperbarui semua dropdown (disable yang sudah dipilih)
    function refreshDropdowns() {
        const existingIds = getSelectedPengurusIds();
        document.querySelectorAll('.pengurus-select').forEach(select => {
            const currentVal = select.value;
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                const val = parseInt(option.value);
                if (val) {
                    const isDisabled = existingIds.includes(val) && val !== parseInt(currentVal);
                    option.disabled = isDisabled;
                    if (isDisabled && !option.text.includes('[Sudah dipilih]')) {
                        option.text += ' [Sudah dipilih]';
                    } else if (!isDisabled && option.text.includes('[Sudah dipilih]')) {
                        option.text = option.text.replace(' [Sudah dipilih]', '');
                    }
                }
            });
        });
    }

    // Fungsi untuk membuat elemen pengurus baru
    function createPengurusItem(selectedId = null, jabatan = '') {
        const existingIds = getSelectedPengurusIds();
        const div = document.createElement('div');
        div.className = 'pengurus-item flex flex-wrap items-end gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800';
        div.innerHTML = `
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Pilih Pengurus</label>
                <select name="pengurus_ids[]" class="pengurus-select w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm focus:ring-2 focus:ring-rose-500" required>
                    ${createUserOptions(selectedId, existingIds)}
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Jabatan Khusus (opsional)</label>
                <input type="text" name="pengurus_jabatan[]" value="${jabatan}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm focus:ring-2 focus:ring-rose-500" placeholder="Misal: Sekretaris">
            </div>
            <button type="button" class="remove-pengurus-btn px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        // Event hapus
        div.querySelector('.remove-pengurus-btn').addEventListener('click', function() {
            div.remove();
            refreshDropdowns();
            validatePengurus();
        });
        // Event change
        div.querySelector('.pengurus-select').addEventListener('change', function() {
            refreshDropdowns();
            validatePengurus();
        });
        return div;
    }

    // Validasi duplikat
    function validatePengurus() {
        const selects = document.querySelectorAll('.pengurus-select');
        const values = [];
        let hasDuplicate = false;
        selects.forEach(select => {
            const val = select.value;
            if (val && values.includes(val)) {
                hasDuplicate = true;
            }
            values.push(val);
        });
        if (hasDuplicate) {
            errorEl.style.display = 'block';
        } else {
            errorEl.style.display = 'none';
        }
        return !hasDuplicate;
    }

    // Tambah pengurus
    addBtn.addEventListener('click', function() {
        const item = createPengurusItem();
        pengurusList.appendChild(item);
        refreshDropdowns();
    });

    // Inisialisasi event untuk item yang sudah ada
    document.querySelectorAll('.pengurus-item').forEach(item => {
        const select = item.querySelector('.pengurus-select');
        const removeBtn = item.querySelector('.remove-pengurus-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                item.remove();
                refreshDropdowns();
                validatePengurus();
            });
        }
        if (select) {
            select.addEventListener('change', function() {
                refreshDropdowns();
                validatePengurus();
            });
        }
    });

    // Refresh awal
    refreshDropdowns();

    // Validasi sebelum submit
    document.getElementById('divisionForm').addEventListener('submit', function(e) {
        if (!validatePengurus()) {
            e.preventDefault();
            errorEl.style.display = 'block';
            errorEl.scrollIntoView({ behavior: 'smooth' });
            return false;
        }
        // Cek konflik dengan ketua
        const ketuaId = document.querySelector('select[name="ketua_id"]').value;
        const pengurusSelects = document.querySelectorAll('.pengurus-select');
        let conflict = false;
        pengurusSelects.forEach(select => {
            if (select.value && select.value === ketuaId) {
                conflict = true;
            }
        });
        if (conflict) {
            e.preventDefault();
            alert('Ketua tidak boleh sama dengan pengurus. Silakan pilih pengurus yang berbeda.');
            return false;
        }
        return true;
    });

    // Jika tidak ada item, tambahkan satu default
    if (document.querySelectorAll('.pengurus-item').length === 0) {
        const defaultItem = createPengurusItem();
        pengurusList.appendChild(defaultItem);
        refreshDropdowns();
    }
});
</script>
@endif
@endsection