@extends('layouts.roles.owner')

@section('title', 'Pilih Target - ' . $generation->name)

@push('styles')
<style>
    /* ================================================================
       1. GLOBAL & UTILITY
       ================================================================ */
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }
    .dark .form-control {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
        outline: none;
    }
    .dark .form-control:focus {
        border-color: #a78bfa;
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.25);
    }

    /* ================================================================
       2. USER ITEM (DAFTAR ANGGOTA)
       ================================================================ */
    .user-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        transition: background-color 0.15s ease, transform 0.1s;
        cursor: pointer;
    }
    .user-item:hover {
        background-color: #f3f4f6;
    }
    .dark .user-item:hover {
        background-color: #374151;
    }
    .user-item:active {
        transform: scale(0.99);
    }

    .user-item input[type="checkbox"] {
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        accent-color: #8b5cf6;
        cursor: pointer;
    }
    .user-item input[type="checkbox"]:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .user-item .user-name {
        font-weight: 500;
        color: #1f2937;
    }
    .dark .user-item .user-name {
        color: #f3f4f6;
    }
    .user-item .user-nim {
        font-size: 0.75rem;
        color: #6b7280;
    }
    .dark .user-item .user-nim {
        color: #9ca3af;
    }
    .user-item .user-role {
        font-size: 0.7rem;
        padding: 2px 10px;
        border-radius: 9999px;
        background: #e5e7eb;
        color: #374151;
    }
    .dark .user-item .user-role {
        background: #4b5563;
        color: #e5e7eb;
    }
    .user-item .user-status {
        font-size: 0.7rem;
        margin-left: auto;
        white-space: nowrap;
    }
    .user-item .user-status.active {
        color: #eab308;
    }
    .user-item .user-status.other {
        color: #f97316;
    }
    .user-item .user-status.none {
        color: #22c55e;
    }

    /* ================================================================
       3. SPINNER LOADING
       ================================================================ */
    .spinner {
        width: 32px;
        height: 32px;
        margin: 20px auto;
        border: 3px solid #e5e7eb;
        border-top: 3px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* ================================================================
       4. USER LIST CONTAINER
       ================================================================ */
    #userList {
        min-height: 120px;
        max-height: 320px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.5rem;
        background-color: #f9fafb;
        scroll-behavior: smooth;
    }
    .dark #userList {
        border-color: #4b5563;
        background-color: #1f2937;
    }
    #userList::-webkit-scrollbar {
        width: 6px;
    }
    #userList::-webkit-scrollbar-track {
        background: #e5e7eb;
        border-radius: 9999px;
    }
    .dark #userList::-webkit-scrollbar-track {
        background: #374151;
    }
    #userList::-webkit-scrollbar-thumb {
        background: #8b5cf6;
        border-radius: 9999px;
    }

    /* ================================================================
       5. BADGE & STATS
       ================================================================ */
    .badge-role {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        background: #e5e7eb;
        color: #374151;
    }
    .dark .badge-role {
        background: #4b5563;
        color: #e5e7eb;
    }
    .stat-card {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    .dark .stat-card {
        background: #1f2937;
        border-color: #4b5563;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .dark .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    /* ================================================================
       6. RESPONSIVE TWEAKS
       ================================================================ */
    @media (max-width: 640px) {
        .filter-group {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-group .form-control,
        .filter-group .btn {
            width: 100% !important;
        }
        .user-item {
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        .user-item .user-status {
            margin-left: 0;
            width: 100%;
            text-align: right;
        }
        .stat-card .text-2xl {
            font-size: 1.5rem;
        }
    }
    @media (min-width: 768px) {
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
    }

    /* ================================================================
       7. TOMBOL & INTERAKSI
       ================================================================ */
    .btn-primary {
        background: #8b5cf6;
        color: #fff;
        padding: 0.6rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-primary:hover {
        background: #7c3aed;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
        padding: 0.6rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dark .btn-secondary {
        background: #4b5563;
        color: #f3f4f6;
    }
    .btn-secondary:hover {
        background: #d1d5db;
        transform: translateY(-1px);
    }
    .dark .btn-secondary:hover {
        background: #6b7280;
    }
    .btn-success {
        background: #22c55e;
        color: #fff;
        padding: 0.6rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-success:hover {
        background: #16a34a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* ================================================================
       8. NOTIFIKASI ALERT
       ================================================================ */
    .alert-error {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .dark .alert-error {
        background: #7f1d1d;
        border-left-color: #ef4444;
    }
    .alert-error span {
        color: #b91c1c;
    }
    .dark .alert-error span {
        color: #fca5a5;
    }
</style>
@endpush

@section('content')
<div class="p-3 md:p-6 lg:p-8 max-w-6xl mx-auto">

    {{-- ================================================================
         HEADER
    ================================================================ --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('owner.financial.generations.show', $generation) }}"
           class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl"
           title="Kembali ke detail generasi">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1 min-w-[200px]">
            <h1 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-2 flex-wrap">
                <i class="fas fa-bullseye text-purple-500"></i>
                <span>Pilih Target untuk {{ $generation->name }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2 flex-wrap">
                <span class="bg-purple-100 dark:bg-purple-900/30 px-2 py-0.5 rounded-full text-xs font-mono">{{ $generation->code ?? 'GEN-' . $generation->id }}</span>
                <span>· Pilih anggota yang akan dimasukkan ke generasi ini</span>
            </p>
        </div>
        <div class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
            <i class="fas fa-calendar-alt mr-1"></i> {{ $generation->entry_year }}
        </div>
    </div>

    {{-- ================================================================
         FORM
    ================================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-4 md:p-6 transition-all">

        {{-- ERROR SESSION --}}
        @if(session('error'))
            <div class="alert-error">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:text-red-400">&times;</button>
            </div>
        @endif

        {{-- FORM --}}
        <form id="targetForm" action="{{ route('owner.financial.generations.target-preview', $generation) }}" method="POST" novalidate>
            @csrf

            {{-- 1. PILIH MODE --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-sliders-h mr-1"></i> Mode Seleksi
                </label>
                <div class="flex flex-wrap gap-3">
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 dark:bg-gray-700/50 px-4 py-2 rounded-lg border border-transparent has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/20 transition">
                        <input type="radio" name="mode" value="simple" checked class="text-purple-500 focus:ring-purple-500 mode-radio">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-users text-purple-400 mr-1"></i> Simple (Role)
                        </span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 dark:bg-gray-700/50 px-4 py-2 rounded-lg border border-transparent has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/20 transition">
                        <input type="radio" name="mode" value="detail" class="text-purple-500 focus:ring-purple-500 mode-radio">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <i class="fas fa-user-check text-blue-400 mr-1"></i> Detail (Individu)
                        </span>
                    </label>
                </div>
            </div>

            {{-- 2. MODE SIMPLE --}}
            <div id="simpleMode" class="space-y-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="fas fa-tag mr-1"></i> Pilih Role yang Akan Dimasukkan
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $eligibleRoles = [
                            ['id' => 3, 'name' => 'Ketua', 'icon' => 'fa-crown', 'color' => 'amber'],
                            ['id' => 4, 'name' => 'Pengurus', 'icon' => 'fa-user-tie', 'color' => 'blue'],
                            ['id' => 5, 'name' => 'Anggota UKM', 'icon' => 'fa-user-graduate', 'color' => 'green'],
                        ];
                    @endphp
                    @foreach($eligibleRoles as $role)
                    <label class="flex items-center gap-2 p-3 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/20">
                        <input type="checkbox" name="role_ids[]" value="{{ $role['id'] }}"
                               class="rounded text-purple-500 focus:ring-purple-500 role-checkbox">
                        <i class="fas {{ $role['icon'] }} text-{{ $role['color'] }}-500 dark:text-{{ $role['color'] }}-400"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role['name'] }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i> Centang role yang ingin dimasukkan ke generasi ini
                </p>
            </div>

            {{-- 3. MODE DETAIL --}}
            <div id="detailMode" class="hidden space-y-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="fas fa-users mr-1"></i> Pilih Anggota Secara Manual
                </label>

                {{-- Filter & Pencarian --}}
                <div class="filter-group flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <input type="text" id="searchUser" class="form-control w-full"
                               placeholder="Cari nama / NIM..." autocomplete="off">
                    </div>
                    <select id="filterRole" class="form-control w-36">
                        <option value="">Semua Role</option>
                        @foreach($eligibleRoles as $role)
                            <option value="{{ $role['id'] }}">{{ $role['name'] }}</option>
                        @endforeach
                    </select>
                    <select id="filterJabatan" class="form-control w-40">
                        <option value="">Semua Jabatan</option>
                        @foreach(\App\Models\Jabatan::orderBy('name')->get() as $jabatan)
                            <option value="{{ $jabatan->id }}">{{ $jabatan->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="btnSearch"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <button type="button" id="btnReset"
                            class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i class="fas fa-undo-alt"></i> Reset
                    </button>
                </div>

                {{-- Daftar User --}}
                <div id="userList">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">
                        <i class="fas fa-search mr-2"></i> Gunakan pencarian untuk menemukan anggota.
                    </p>
                </div>

                {{-- Kontrol Seleksi --}}
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <button type="button" id="selectAllUsers"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                        <i class="fas fa-check-double"></i> Pilih Semua
                    </button>
                    <button type="button" id="deselectAllUsers"
                            class="text-sm text-red-600 dark:text-red-400 hover:underline flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Batalkan Semua
                    </button>
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-auto" id="selectedCount">0 dipilih</span>
                    <span id="resultCount" class="text-sm text-gray-400 dark:text-gray-500"></span>
                </div>
            </div>

            {{-- 4. TOMBOL AKSI --}}
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                <button type="submit" id="previewBtn"
                        class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-eye"></i> Preview Target
                </button>
                <a href="{{ route('owner.financial.generations.show', $generation) }}"
                   class="btn-secondary">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================================================================
    // 1. REFERENSI ELEMEN
    // ================================================================
    const form         = document.getElementById('targetForm');
    const modeRadios   = document.querySelectorAll('.mode-radio');
    const simpleMode   = document.getElementById('simpleMode');
    const detailMode   = document.getElementById('detailMode');
    const searchInput  = document.getElementById('searchUser');
    const filterRole   = document.getElementById('filterRole');
    const filterJabatan= document.getElementById('filterJabatan');
    const btnSearch    = document.getElementById('btnSearch');
    const btnReset     = document.getElementById('btnReset');
    const userList     = document.getElementById('userList');
    const selectedCount= document.getElementById('selectedCount');
    const resultCount  = document.getElementById('resultCount');
    const previewBtn   = document.getElementById('previewBtn');

    // ================================================================
    // 2. UPDATE COUNTER
    // ================================================================
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCount.textContent = checked + ' dipilih';
    }

    // ================================================================
    // 3. LOAD USERS (AJAX) – DENGAN NAMA GENERASI
    // ================================================================
    function loadUsers() {
        const q       = searchInput.value.trim();
        const role    = filterRole.value;
        const jabatan = filterJabatan ? filterJabatan.value : '';

        const params = new URLSearchParams();
        if (q) params.append('q', q);
        if (role) params.append('role', role);
        if (jabatan) params.append('jabatan', jabatan);

        const url = '{{ route("owner.financial.generations.search-users", $generation) }}?' + params.toString();

        // Show loading
        userList.innerHTML = `
            <div class="spinner"></div>
            <p class="text-center text-sm text-gray-500">Memuat data...</p>
        `;
        resultCount.textContent = '';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || `Server error ${response.status}`);
                }).catch(() => {
                    throw new Error(`Server error ${response.status} - ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                userList.innerHTML = `
                    <p class="text-sm text-red-500 text-center py-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${data.error}
                    </p>
                `;
                resultCount.textContent = '';
                return;
            }

            if (!data || data.length === 0) {
                userList.innerHTML = `
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">
                        <i class="fas fa-user-slash mr-2"></i>
                        Tidak ada anggota ditemukan.
                    </p>
                `;
                resultCount.textContent = '0 ditemukan';
                return;
            }

            // Render users dengan status detail
            let html = '';
            data.forEach(user => {
                const roleName = user.level_id == 3 ? 'Ketua'
                    : user.level_id == 4 ? 'Pengurus'
                    : user.level_id == 5 ? 'Anggota UKM'
                    : 'Unknown';

                // === STATUS DENGAN NAMA GENERASI ===
                let statusText = '';
                let statusClass = 'none';

                if (user.membership_status === 'active') {
                    statusText = `✅ Aktif di <strong>${user.current_generation_name || 'generasi ini'}</strong>`;
                    statusClass = 'active';
                } else if (user.membership_status === 'other') {
                    statusText = `🔄 Bergabung di <strong>${user.current_generation_name || 'generasi lain'}</strong>`;
                    statusClass = 'other';
                } else {
                    statusText = '⬜ Belum tergabung';
                    statusClass = 'none';
                }

                html += `
                    <label class="user-item">
                        <input type="checkbox"
                               name="user_ids[]"
                               value="${user.id}"
                               class="user-checkbox">
                        <span class="user-name">${user.name}</span>
                        <span class="user-nim">${user.nim || '-'}</span>
                        <span class="user-role">${roleName}</span>
                        <span class="user-status ${statusClass}">${statusText}</span>
                    </label>
                `;
            });
            userList.innerHTML = html;
            resultCount.textContent = data.length + ' ditemukan';
            updateSelectedCount();
        })
        .catch(err => {
            console.error('Error loading users:', err);
            userList.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded p-4">
                    <p class="text-sm text-red-700 dark:text-red-300 flex items-start gap-2">
                        <i class="fas fa-exclamation-circle text-red-500 text-lg mt-0.5"></i>
                        <span>
                            <strong>Gagal memuat data:</strong><br>
                            ${err.message}
                            <br><span class="text-xs opacity-75">Silakan coba lagi atau hubungi administrator.</span>
                        </span>
                    </p>
                </div>
            `;
            resultCount.textContent = '';
        });
    }

    // ================================================================
    // 4. EVENT SEARCH & FILTER
    // ================================================================
    btnSearch.addEventListener('click', loadUsers);
    btnReset.addEventListener('click', function() {
        searchInput.value = '';
        filterRole.value = '';
        if (filterJabatan) filterJabatan.value = '';
        loadUsers();
    });
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') loadUsers();
    });
    filterRole.addEventListener('change', loadUsers);
    if (filterJabatan) filterJabatan.addEventListener('change', loadUsers);

    // ================================================================
    // 5. TOGGLE MODE
    // ================================================================
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'simple') {
                simpleMode.classList.remove('hidden');
                detailMode.classList.add('hidden');
            } else {
                simpleMode.classList.add('hidden');
                detailMode.classList.remove('hidden');
                loadUsers(); // auto-load
            }
        });
    });

    // Jika mode detail sudah aktif saat halaman dimuat
    if (document.querySelector('input[name="mode"][value="detail"]:checked')) {
        detailMode.classList.remove('hidden');
        simpleMode.classList.add('hidden');
        loadUsers();
    }

    // ================================================================
    // 6. SELECT / DESELECT ALL
    // ================================================================
    document.getElementById('selectAllUsers').addEventListener('click', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = true);
        updateSelectedCount();
    });
    document.getElementById('deselectAllUsers').addEventListener('click', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        updateSelectedCount();
    });
    document.addEventListener('change', function(e) {
        if (e.target.classList && e.target.classList.contains('user-checkbox')) {
            updateSelectedCount();
        }
    });

    // ================================================================
    // 7. VALIDASI SEBELUM SUBMIT – PASTIKAN POST
    // ================================================================
    form.addEventListener('submit', function(e) {
        const mode = document.querySelector('input[name="mode"]:checked');
        if (!mode) {
            e.preventDefault();
            alert('⚠️ Silakan pilih mode terlebih dahulu.');
            return false;
        }

        const modeValue = mode.value;
        let selected = false;

        if (modeValue === 'simple') {
            const roleChecks = document.querySelectorAll('.role-checkbox:checked');
            if (roleChecks.length > 0) selected = true;
        } else {
            const userChecks = document.querySelectorAll('.user-checkbox:checked');
            if (userChecks.length > 0) selected = true;
        }

        if (!selected) {
            e.preventDefault();
            alert('⚠️ Silakan pilih minimal satu target (role atau individu) sebelum melanjutkan.');
            return false;
        }

        // Pastikan method POST
        if (this.method.toUpperCase() !== 'POST') {
            e.preventDefault();
            alert('❌ Metode form salah. Silakan refresh halaman dan coba lagi.');
            return false;
        }

        // Logging (untuk debugging)
        console.log('Form submitted with method:', this.method);
        console.log('Data:', new FormData(this));

        return true;
    });

    // ================================================================
    // 8. CEGAH LINK LANGSUNG KE target-preview (opsional)
    // ================================================================
    document.querySelectorAll('a[href*="target-preview"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            alert('❌ Akses langsung tidak diizinkan. Gunakan tombol "Preview Target" di halaman ini.');
            window.location.href = '{{ route("owner.financial.generations.target", $generation) }}';
        });
    });

});
</script>
@endpush
@endsection