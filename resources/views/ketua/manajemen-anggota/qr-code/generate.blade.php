@extends('layouts.roles.ketua')

@section('title', 'Generate QR Code Anggota')

@push('styles')
<style>
    /* ====== CUSTOM STYLES UNTUK HALAMAN QR ====== */
    .qr-preview-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .qr-preview-card:hover {
        border-color: var(--primary);
        box-shadow: 0 8px 30px rgba(59, 184, 255, 0.15);
    }
    .qr-preview-card .qr-image-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 1rem;
        background: white;
        border-radius: 12px;
        padding: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .qr-preview-card .qr-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .qr-preview-card .logo-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--primary);
        font-weight: 700;
        font-size: 12px;
        color: var(--primary);
    }
    .qr-preview-card .qr-detail {
        width: 100%;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.25rem 1rem;
        text-align: left;
    }
    .qr-preview-card .qr-detail .label {
        color: var(--text-secondary);
        font-weight: 500;
    }
    .qr-preview-card .qr-detail .value {
        color: var(--text-primary);
        font-weight: 600;
        word-break: break-all;
    }
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge.active { background: var(--success); color: white; }
    .status-badge.revoked { background: var(--danger); color: white; }
    .status-badge.expired { background: var(--warning); color: #111; }
    .status-badge.nonactive { background: #6B7280; color: white; }

    /* Indikator status QR di daftar anggota */
    .qr-status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-left: 6px;
    }
    .qr-status-indicator.sudah { background: var(--success); }
    .qr-status-indicator.belum { background: var(--danger); }
    .qr-status-indicator.expired { background: var(--warning); }
    .qr-status-indicator.nonaktif { background: #6B7280; }

    /* Checkbox custom */
    .custom-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        transition: background 0.15s;
    }
    .custom-checkbox:hover {
        background: rgba(59, 184, 255, 0.05);
    }
    .custom-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        color: white;
        font-size: 1.2rem;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s;
    }
    .loading-overlay.show {
        visibility: visible;
        opacity: 1;
    }
    .loading-overlay .spinner {
        width: 60px;
        height: 60px;
        border: 5px solid rgba(255,255,255,0.1);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 1rem;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Grid preview */
    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    /* Search result item */
    .search-result-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background 0.15s;
        border-bottom: 1px solid var(--border-color);
    }
    .search-result-item:hover {
        background: rgba(59, 184, 255, 0.08);
    }
    .search-result-item:last-child {
        border-bottom: none;
    }
    .search-result-item .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Mini modal untuk print */
    .mini-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        visibility: hidden;
        opacity: 0;
        transition: all 0.25s;
    }
    .mini-modal.show {
        visibility: visible;
        opacity: 1;
    }
    .mini-modal .modal-content {
        background: var(--bg-card);
        border-radius: 1.5rem;
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        border: 1px solid var(--border-color);
    }
    .mini-modal .modal-content h3 {
        color: var(--text-primary);
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .mini-modal .modal-content .format-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin: 1rem 0;
    }
    .mini-modal .modal-content .format-options button {
        padding: 0.5rem 0;
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }
    .mini-modal .modal-content .format-options button:hover {
        background: rgba(59, 184, 255, 0.1);
        border-color: var(--primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .qr-detail {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- BREADCRUMB --}}
{{-- ============================================================ --}}
<nav class="text-sm text-muted mb-6 flex items-center gap-2">
    <a href="{{ route('ketua.dashboard') }}" class="hover:text-primary transition">Dashboard</a>
    <span>/</span>
    <a href="{{ route('ketua.manajemen-anggota.index') }}" class="hover:text-primary transition">Manajemen Anggota</a>
    <span>/</span>
    <a href="{{ route('ketua.manajemen-anggota.qr.index') }}" class="hover:text-primary transition">QR Code</a>
    <span>/</span>
    <span class="text-primary font-medium">Generate QR Code</span>
</nav>

{{-- ============================================================ --}}
{{-- CARD 1: INFORMASI & STATISTIK --}}
{{-- ============================================================ --}}
<div class="glass-card p-6 rounded-2xl border-custom mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center text-3xl text-primary">
                🪪
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary">Generate QR Code Anggota</h1>
                <p class="text-muted max-w-2xl mt-1 text-sm">
                    Silakan pilih satu atau beberapa data anggota yang ingin dikonversi menjadi QR Code resmi UKM Pemrograman.
                    QR Code ini nantinya dapat digunakan sebagai:
                    <span class="block mt-1 text-xs text-soft-gray">
                        ✔ Login QR &nbsp;•&nbsp; ✔ Presensi &nbsp;•&nbsp; ✔ Identitas Digital &nbsp;•&nbsp; ✔ Verifikasi Anggota &nbsp;•&nbsp; ✔ Scan Event &nbsp;•&nbsp; ✔ Scan Sertifikat
                    </span>
                </p>
            </div>
        </div>
        {{-- Statistik dari database --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center text-sm bg-white/5 p-3 rounded-xl border border-custom" id="statsContainer">
            <div><span class="block text-xl font-bold text-primary" id="statTotal">0</span><span class="text-xs text-muted">Total</span></div>
            <div><span class="block text-xl font-bold text-emerald" id="statSudah">0</span><span class="text-xs text-muted">Sudah QR</span></div>
            <div><span class="block text-xl font-bold text-warning" id="statBelum">0</span><span class="text-xs text-muted">Belum QR</span></div>
            <div><span class="block text-xl font-bold text-danger" id="statExpired">0</span><span class="text-xs text-muted">Expired</span></div>
            <div><span class="block text-xl font-bold text-soft-gray" id="statNonaktif">0</span><span class="text-xs text-muted">Nonaktif</span></div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CARD 2: PENCARIAN & PEMILIHAN ANGGOTA --}}
{{-- ============================================================ --}}
<div class="glass-card p-6 rounded-2xl border-custom mb-6">
    <h2 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
        <i class="fas fa-search text-electric-blue"></i> Cari & Pilih Anggota
    </h2>

    {{-- Input pencarian dengan shortcut Ctrl+F --}}
    <div class="relative mb-4">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
        <input type="text" id="searchMember" placeholder="Cari Nama, NIM, UUID, Jabatan, Email... (Ctrl+F)"
               class="w-full bg-white/5 border border-custom rounded-xl py-3 pl-11 pr-4 text-sm text-primary placeholder-muted focus:outline-none focus:border-primary transition focus:ring-2 focus:ring-primary/20">
        <div id="searchResultsContainer" class="absolute top-full left-0 right-0 mt-1 bg-card rounded-xl shadow-2xl border border-custom hidden max-h-60 overflow-y-auto z-50">
            <!-- Hasil live search -->
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <span class="text-xs text-muted font-medium">Filter:</span>
        <div class="flex flex-wrap gap-1.5" id="filterButtons">
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition active-filter" data-filter="semua">Semua</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="ketua">Ketua</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="pengurus">Pengurus</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="anggota">Anggota</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="alumni">Alumni</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="aktif">Aktif</button>
            <button class="filter-btn px-3 py-1 rounded-full text-xs border border-custom text-muted hover:border-primary hover:text-primary transition" data-filter="tidakaktif">Tidak Aktif</button>
        </div>
    </div>

    {{-- Daftar anggota --}}
    <div class="border border-custom rounded-xl p-3 max-h-64 overflow-y-auto">
        <div class="flex items-center gap-2 border-b border-custom pb-2 mb-2">
            <label class="custom-checkbox">
                <input type="checkbox" id="selectAll"> <span class="text-sm text-muted">Pilih Semua</span>
            </label>
            <span class="text-xs text-muted ml-auto" id="selectedCount">0 dipilih</span>
        </div>
        <div id="memberList" class="space-y-1">
            <!-- Data anggota dari API -->
        </div>
    </div>

    <div class="mt-4 flex justify-end">
        <button id="generateBtn" disabled
                class="px-6 py-2.5 rounded-xl font-semibold bg-primary/20 text-muted border border-custom cursor-not-allowed transition flex items-center gap-2">
            <i class="fas fa-qrcode"></i> Generate QR
        </button>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CARD 3: PREVIEW HASIL GENERATE --}}
{{-- ============================================================ --}}
<div id="previewContainer" class="glass-card p-6 rounded-2xl border-custom mb-6 hidden">
    <h2 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
        <i class="fas fa-eye text-neon-cyan"></i> Preview QR Code
    </h2>
    <div id="qrPreviewGrid" class="qr-grid">
        <!-- Hasil generate QR -->
    </div>
    <div class="mt-6 flex flex-wrap gap-3 justify-end">
        <button id="saveQrBtn" class="px-5 py-2 bg-emerald/20 text-emerald rounded-xl hover:bg-emerald/30 transition flex items-center gap-2">
            <i class="fas fa-save"></i> 💾 Simpan
        </button>
        <button id="printQrBtn" class="px-5 py-2 bg-primary/20 text-primary rounded-xl hover:bg-primary/30 transition flex items-center gap-2">
            <i class="fas fa-print"></i> 🖨 Print
        </button>
        <button id="cancelQrBtn" class="px-5 py-2 bg-danger/20 text-danger rounded-xl hover:bg-danger/30 transition flex items-center gap-2">
            <i class="fas fa-times"></i> 🗑 Batalkan
        </button>
        <button id="downloadQrBtn" class="px-5 py-2 bg-royal-purple/20 text-royal-purple rounded-xl hover:bg-royal-purple/30 transition flex items-center gap-2">
            <i class="fas fa-download"></i> ⬇ Download
        </button>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CARD 4: RIWAYAT AKTIVITAS --}}
{{-- ============================================================ --}}
<div class="glass-card p-6 rounded-2xl border-custom">
    <h2 class="text-lg font-semibold text-primary mb-4 flex items-center gap-2">
        <i class="fas fa-history text-warning"></i> Riwayat Aktivitas QR (Terbaru)
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-custom text-left text-muted">
                <tr><th class="py-2 px-3">Waktu</th><th class="py-2 px-3">Anggota</th><th class="py-2 px-3">Aksi</th><th class="py-2 px-3">Status</th></tr>
            </thead>
            <tbody class="text-primary" id="activityLog">
                <tr><td colspan="4" class="py-4 text-center text-muted">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================================ --}}
{{-- LOADING OVERLAY --}}
{{-- ============================================================ --}}
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <p id="loadingText">Memproses...</p>
    <div class="w-64 bg-white/20 rounded-full h-2 mt-3 overflow-hidden">
        <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: 0%;" id="progressBar"></div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MINI MODAL PRINT --}}
{{-- ============================================================ --}}
<div id="printModal" class="mini-modal">
    <div class="modal-content">
        <h3>🖨 Pilih Format Cetak</h3>
        <div class="format-options" id="formatOptions">
            <button data-format="pdf">PDF</button>
            <button data-format="word">Word</button>
            <button data-format="excel">Excel</button>
            <button data-format="png">PNG</button>
            <button data-format="zip">ZIP</button>
            <button data-format="idcard">🪪 ID Card</button>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button id="closePrintModal" class="px-4 py-2 text-sm border border-custom rounded-lg hover:bg-white/5 transition">Batal</button>
            <button id="confirmPrint" class="px-4 py-2 text-sm bg-primary text-white rounded-lg hover:bg-primary/90 transition">Cetak</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- QR Code Generator Library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
    // ============================================================
    // KONFIGURASI
    // ============================================================
    const APP_URL = "{{ config('app.url') }}";
    const API_BASE = "{{ route('ketua.manajemen-anggota.qr.index') }}/api"; // /ketua/manajemen-anggota/qr/api

    // State
    let allMembers = [];          // semua anggota yang sedang ditampilkan (filter)
    let selectedMembers = [];    // id anggota yang dipilih
    let generatedData = null;    // hasil generate
    let currentFilter = 'semua';

    // Elemen DOM
    const memberListEl = document.getElementById('memberList');
    const searchInput = document.getElementById('searchMember');
    const searchResultsContainer = document.getElementById('searchResultsContainer');
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectedCountSpan = document.getElementById('selectedCount');
    const generateBtn = document.getElementById('generateBtn');
    const previewContainer = document.getElementById('previewContainer');
    const qrPreviewGrid = document.getElementById('qrPreviewGrid');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const progressBar = document.getElementById('progressBar');
    const loadingText = document.getElementById('loadingText');
    const printModal = document.getElementById('printModal');
    const formatOptionsContainer = document.getElementById('formatOptions');

    // ============================================================
    // FUNGSI UTILITY
    // ============================================================
    function showLoading(text = 'Memproses...') {
        loadingText.textContent = text;
        loadingOverlay.classList.add('show');
        progressBar.style.width = '0%';
    }

    function hideLoading() {
        loadingOverlay.classList.remove('show');
    }

    function updateProgress(percent) {
        progressBar.style.width = percent + '%';
    }

    // ============================================================
    // 1. AMBIL STATISTIK DARI API
    // ============================================================
    async function fetchStatistik() {
        try {
            const res = await fetch(`${API_BASE}/statistik`);
            if (!res.ok) throw new Error('Gagal mengambil statistik');
            const json = await res.json();
            document.getElementById('statTotal').textContent = json.total_anggota || 0;
            document.getElementById('statSudah').textContent = json.sudah_qr || 0;
            document.getElementById('statBelum').textContent = json.belum_qr || 0;
            document.getElementById('statExpired').textContent = json.expired || 0;
            document.getElementById('statNonaktif').textContent = json.nonaktif || 0;
        } catch (err) {
            console.error(err);
        }
    }

    // ============================================================
    // 2. AMBIL DAFTAR ANGGOTA SESUAI FILTER & SEARCH
    // ============================================================
    async function fetchMembers(search = '', filter = 'semua') {
        try {
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (filter && filter !== 'semua') params.append('filter', filter);
            const url = `${API_BASE}/anggota?${params.toString()}`;
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal mengambil data anggota');
            const json = await res.json();
            allMembers = json.members || [];
            renderMemberList(allMembers);
        } catch (err) {
            console.error(err);
            memberListEl.innerHTML = '<p class="text-muted text-sm p-2">Gagal memuat data anggota.</p>';
        }
    }

    // Render checkbox list
    function renderMemberList(members) {
        memberListEl.innerHTML = '';
        if (members.length === 0) {
            memberListEl.innerHTML = '<p class="text-muted text-sm p-2">Tidak ada anggota.</p>';
            return;
        }
        members.forEach(m => {
            const checked = selectedMembers.includes(m.id) ? 'checked' : '';
            // Tentukan kelas dan teks status QR
            let qrStatusClass = '', qrStatusText = '';
            if (m.qr_status === 'active' || m.has_qr) {
                qrStatusClass = 'sudah';
                qrStatusText = 'Sudah QR';
            } else if (m.qr_status === 'expired') {
                qrStatusClass = 'expired';
                qrStatusText = 'Expired';
            } else if (!m.is_active) {
                qrStatusClass = 'nonaktif';
                qrStatusText = 'Nonaktif';
            } else {
                qrStatusClass = 'belum';
                qrStatusText = 'Belum QR';
            }

            const label = document.createElement('label');
            label.className = 'custom-checkbox';
            label.innerHTML = `
                <input type="checkbox" class="member-checkbox" value="${m.id}" ${checked}>
                <span class="text-sm text-primary">${m.name}</span>
                <span class="text-xs text-muted ml-auto">${m.nim || '-'} · ${m.jabatan || m.level || ''}</span>
                <span class="qr-status-indicator ${qrStatusClass}" title="${qrStatusText}"></span>
                <span class="text-xs" style="color: ${qrStatusClass === 'sudah' ? 'var(--success)' : qrStatusClass === 'belum' ? 'var(--danger)' : qrStatusClass === 'expired' ? 'var(--warning)' : '#6B7280'}">${qrStatusText}</span>
            `;
            memberListEl.appendChild(label);
        });

        // Event listener untuk checkbox individu
        document.querySelectorAll('.member-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const id = parseInt(this.value);
                if (this.checked) {
                    if (!selectedMembers.includes(id)) selectedMembers.push(id);
                } else {
                    selectedMembers = selectedMembers.filter(x => x !== id);
                }
                updateSelectionUI();
            });
        });
        updateSelectionUI();
    }

    function updateSelectionUI() {
        selectedCountSpan.textContent = `${selectedMembers.length} dipilih`;
        const allCheckboxes = document.querySelectorAll('.member-checkbox');
        const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
        // Enable/disable tombol generate
        if (selectedMembers.length > 0) {
            generateBtn.disabled = false;
            generateBtn.className = 'px-6 py-2.5 rounded-xl font-semibold bg-primary text-white hover:bg-primary/90 transition flex items-center gap-2 cursor-pointer';
            generateBtn.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR';
        } else {
            generateBtn.disabled = true;
            generateBtn.className = 'px-6 py-2.5 rounded-xl font-semibold bg-primary/20 text-muted border border-custom cursor-not-allowed transition flex items-center gap-2';
            generateBtn.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR';
        }
    }

    // ============================================================
    // 3. LIVE SEARCH DENGAN DEBOUNCE & DROPDOWN
    // ============================================================
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const keyword = this.value.trim();
        if (keyword.length === 0) {
            searchResultsContainer.classList.add('hidden');
            // Kembalikan ke daftar normal (fetch ulang tanpa search)
            fetchMembers('', currentFilter);
            return;
        }
        searchTimeout = setTimeout(async () => {
            try {
                const params = new URLSearchParams();
                params.append('search', keyword);
                if (currentFilter !== 'semua') params.append('filter', currentFilter);
                const res = await fetch(`${API_BASE}/anggota?${params.toString()}`);
                const json = await res.json();
                const filtered = json.members || [];
                if (filtered.length === 0) {
                    searchResultsContainer.classList.remove('hidden');
                    searchResultsContainer.innerHTML = '<div class="p-3 text-muted text-sm">Tidak ditemukan.</div>';
                    return;
                }
                searchResultsContainer.classList.remove('hidden');
                searchResultsContainer.innerHTML = filtered.map(m => {
                    let status = m.has_qr ? '✓ QR' : '✗ QR';
                    return `
                        <div class="search-result-item" data-id="${m.id}">
                            <div class="avatar">${m.name.charAt(0)}</div>
                            <div class="flex-1">
                                <div class="text-sm text-primary font-medium">${m.name}</div>
                                <div class="text-xs text-muted">${m.nim || '-'} · ${m.jabatan || m.level || ''}</div>
                            </div>
                            <span class="text-xs ${m.has_qr ? 'text-emerald' : 'text-danger'}">${status}</span>
                        </div>
                    `;
                }).join('');

                // Klik hasil -> tambahkan ke selectedMembers
                document.querySelectorAll('.search-result-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const id = parseInt(this.dataset.id);
                        if (!selectedMembers.includes(id)) {
                            selectedMembers.push(id);
                        }
                        searchInput.value = '';
                        searchResultsContainer.classList.add('hidden');
                        // Refresh daftar checkbox agar centang muncul
                        fetchMembers('', currentFilter).then(() => {
                            updateSelectionUI();
                        });
                    });
                });
            } catch (err) {
                console.error(err);
            }
        }, 300);
    });

    // Sembunyikan dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
            searchResultsContainer.classList.add('hidden');
        }
    });

    // ============================================================
    // 4. FILTER BUTTONS
    // ============================================================
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            currentFilter = filter;
            // Update UI tombol
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active-filter', 'border-primary', 'text-primary');
                b.classList.add('border-custom', 'text-muted');
            });
            this.classList.add('active-filter', 'border-primary', 'text-primary');
            this.classList.remove('border-custom', 'text-muted');
            // Fetch anggota sesuai filter
            searchInput.value = '';
            searchResultsContainer.classList.add('hidden');
            fetchMembers('', filter);
        });
    });

    // ============================================================
    // 5. SELECT ALL
    // ============================================================
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.member-checkbox');
        const isChecked = this.checked;
        checkboxes.forEach(cb => {
            cb.checked = isChecked;
            const id = parseInt(cb.value);
            if (isChecked) {
                if (!selectedMembers.includes(id)) selectedMembers.push(id);
            } else {
                selectedMembers = selectedMembers.filter(x => x !== id);
            }
        });
        updateSelectionUI();
    });

    // ============================================================
    // 6. GENERATE QR CODE (API CALL)
    // ============================================================
    generateBtn.addEventListener('click', async function() {
        if (selectedMembers.length === 0) return;
        if (generatedData) {
            if (!confirm('Anda sudah memiliki hasil generate. Ingin generate ulang?')) return;
        }

        showLoading('Menghasilkan QR Code...');
        updateProgress(0);

        try {
            const response = await fetch(`${API_BASE}/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_ids: selectedMembers })
            });

            // Simulasi progress (karena backend real bisa cepat)
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                if (progress > 90) clearInterval(interval);
                updateProgress(progress);
            }, 150);

            const json = await response.json();
            clearInterval(interval);
            updateProgress(100);

            if (!json.success) {
                throw new Error(json.message || 'Gagal generate QR');
            }

            generatedData = json.data; // array of { user_id, name, token, ... }
            setTimeout(() => {
                hideLoading();
                renderPreview(generatedData);
                previewContainer.classList.remove('hidden');
            }, 300);
        } catch (err) {
            hideLoading();
            alert('Error: ' + err.message);
            console.error(err);
        }
    });

    // ============================================================
    // 7. RENDER PREVIEW QR (CLIENT-SIDE QR GENERATION)
    // ============================================================
    async function renderPreview(data) {
        qrPreviewGrid.innerHTML = '';
        for (const item of data) {
            const card = document.createElement('div');
            card.className = 'qr-preview-card';
            // Buat QR code dengan data URL login
            const qrValue = `${APP_URL}/login/qr/${item.token}`;
            let qrDataUrl = '';
            try {
                qrDataUrl = await QRCode.toDataURL(qrValue, { width: 160, margin: 1 });
            } catch (e) {
                qrDataUrl = ''; // fallback
            }
            // Dapatkan informasi tambahan dari anggota yang dipilih (dari allMembers)
            const member = allMembers.find(m => m.id === item.user_id) || {};
            const status = member.is_active ? (item.qr_status === 'expired' ? 'Expired' : 'Aktif') : 'Nonaktif';
            const statusClass = status === 'Aktif' ? 'active' : (status === 'Expired' ? 'expired' : 'nonactive');

            card.innerHTML = `
                <div class="qr-image-wrapper">
                    ${qrDataUrl ? `<img src="${qrDataUrl}" alt="QR Code">` : '<span class="text-muted">QR gagal dimuat</span>'}
                    <div class="logo-overlay">UKM</div>
                </div>
                <div class="qr-detail">
                    <span class="label">Nama</span><span class="value">${item.name || member.name || '-'}</span>
                    <span class="label">NIM</span><span class="value">${member.nim || '-'}</span>
                    <span class="label">UUID</span><span class="value">${member.user_uuid || '-'}</span>
                    <span class="label">Jabatan</span><span class="value">${member.jabatan || member.level || '-'}</span>
                    <span class="label">Status</span><span class="value"><span class="status-badge ${statusClass}">${status}</span></span>
                    <span class="label">Token</span><span class="value" title="${item.token}">${item.token.substring(0,12)}...</span>
                </div>
            `;
            qrPreviewGrid.appendChild(card);
        }
    }

    // ============================================================
    // 8. SIMPAN QR (Sekadar konfirmasi, karena generate sudah menyimpan)
    // ============================================================
    document.getElementById('saveQrBtn').addEventListener('click', async function() {
        if (!generatedData) return;
        try {
            showLoading('Menyimpan...');
            const res = await fetch(`${API_BASE}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_ids: selectedMembers })
            });
            const json = await res.json();
            hideLoading();
            if (json.success) {
                alert('✅ QR Code telah disimpan dan siap digunakan.');
                // Refresh statistik
                fetchStatistik();
            } else {
                alert('Gagal menyimpan: ' + (json.message || ''));
            }
        } catch (err) {
            hideLoading();
            alert('Error: ' + err.message);
        }
    });

    // ============================================================
    // 9. PRINT QR (Modal pilih format, lalu download)
    // ============================================================
    document.getElementById('printQrBtn').addEventListener('click', () => {
        if (!generatedData) return;
        // Reset pilihan format
        document.querySelectorAll('#formatOptions button').forEach(b => b.classList.remove('active', 'bg-primary/20', 'border-primary'));
        printModal.classList.add('show');
    });

    document.getElementById('closePrintModal').addEventListener('click', () => {
        printModal.classList.remove('show');
    });

    document.getElementById('confirmPrint').addEventListener('click', async function() {
        const activeFormat = document.querySelector('#formatOptions button.active');
        if (!activeFormat) {
            alert('Pilih format terlebih dahulu.');
            return;
        }
        const format = activeFormat.dataset.format;
        printModal.classList.remove('show');
        showLoading('Menyiapkan cetakan...');
        try {
            const res = await fetch(`${API_BASE}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_ids: selectedMembers, format: format })
            });
            if (!res.ok) throw new Error('Gagal mencetak');
            // Asumsikan server mengembalikan file download
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `qr_codes.${format === 'idcard' ? 'pdf' : format}`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            hideLoading();
        } catch (err) {
            hideLoading();
            alert('Error: ' + err.message);
        }
    });

    // Format option active styling
    document.querySelectorAll('#formatOptions button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#formatOptions button').forEach(b => {
                b.classList.remove('active', 'bg-primary/20', 'border-primary');
            });
            this.classList.add('active', 'bg-primary/20', 'border-primary');
        });
    });

    // ============================================================
    // 10. BATALKAN PREVIEW
    // ============================================================
    document.getElementById('cancelQrBtn').addEventListener('click', function() {
        if (confirm('Batalkan generate QR? Data yang belum disimpan akan hilang.')) {
            previewContainer.classList.add('hidden');
            generatedData = null;
            selectedMembers = [];
            // Refresh daftar anggota untuk menghilangkan centang
            fetchMembers(searchInput.value.trim(), currentFilter);
        }
    });

    // ============================================================
    // 11. DOWNLOAD QR (ZIP)
    // ============================================================
    document.getElementById('downloadQrBtn').addEventListener('click', async function() {
        if (!generatedData) return;
        showLoading('Menyiapkan unduhan...');
        try {
            const res = await fetch(`${API_BASE}/download`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_ids: selectedMembers })
            });
            if (!res.ok) throw new Error('Gagal mengunduh');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'qr_codes.zip';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            hideLoading();
        } catch (err) {
            hideLoading();
            alert('Error: ' + err.message);
        }
    });

    // ============================================================
    // 12. SHORTCUT CTRL+F
    // ============================================================
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

    // ============================================================
    // 13. RIWAYAT AKTIVITAS (DUMMY ATAU AMBIL DARI ENDPOINT)
    // ============================================================
    async function fetchActivityLog() {
        // Untuk sementara gunakan data dummy, nanti bisa diganti dengan API
        const dummy = [
            { waktu: '04 Aug 2026, 14:23', anggota: 'Muhammad Ibni', aksi: 'Generate QR', status: 'Aktif' },
            { waktu: '03 Aug 2026, 09:15', anggota: 'Andi', aksi: 'Print QR', status: 'Aktif' },
            { waktu: '02 Aug 2026, 16:47', anggota: 'Budi', aksi: 'Revoke QR', status: 'Revoked' }
        ];
        const tbody = document.getElementById('activityLog');
        tbody.innerHTML = dummy.map(r => `
            <tr>
                <td class="py-2 px-3">${r.waktu}</td>
                <td class="py-2 px-3">${r.anggota}</td>
                <td class="py-2 px-3">${r.aksi}</td>
                <td class="py-2 px-3"><span class="status-badge ${r.status === 'Aktif' ? 'active' : 'revoked'}">${r.status}</span></td>
            </tr>
        `).join('');
    }

    // ============================================================
    // INISIALISASI HALAMAN
    // ============================================================
    async function init() {
        showLoading('Memuat data...');
        await Promise.all([
            fetchStatistik(),
            fetchMembers('', currentFilter),
            fetchActivityLog()
        ]);
        hideLoading();
    }

    init();
</script>
