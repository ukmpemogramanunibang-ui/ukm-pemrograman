@extends('layouts.roles.owner')

@section('title', 'Naik/Turun Pangkat – Owner UNICODE')
@section('page-title', 'Naik / Turun Pangkat')

@push('styles')
<style>
    /* ========== GLOBAL ========== */
    .promote-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .dark .promote-container {
        background: #1f2937;
        border-color: #374151;
    }

    .promote-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }
    .promote-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dark .promote-header h3 { color: #f3f4f6; }
    .promote-header p {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    .dark .promote-header p { color: #9ca3af; }

    .stats-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        padding: 0.5rem 1rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
    .dark .stats-bar { background: #111827; }
    .stats-bar .stat-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .stats-bar .stat-number {
        font-weight: 700;
        color: #6366f1;
        font-size: 1.1rem;
    }
    .dark .stats-bar .stat-number { color: #818cf8; }

    .table-container {
        overflow-x: auto;
        margin-top: 0.5rem;
    }
    .table-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .table-container th {
        text-align: left;
        padding: 0.6rem 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    .dark .table-container th {
        color: #9ca3af;
        border-color: #374151;
        background: #1f2937;
    }
    .table-container td {
        padding: 0.6rem 0.75rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .dark .table-container td {
        border-color: #374151;
        color: #e5e7eb;
    }
    .table-container tr:hover td {
        background: #f9fafb;
    }
    .dark .table-container tr:hover td {
        background: #374151;
    }

    .badge-role {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        background: rgba(99,102,241,0.12);
        color: #6366f1;
    }
    .dark .badge-role {
        background: rgba(99,102,241,0.2);
        color: #818cf8;
    }

    .badge-pin {
        display: inline-block;
        padding: 0.1rem 0.4rem;
        border-radius: 9999px;
        font-size: 0.6rem;
        font-weight: 600;
        background: rgba(34,197,94,0.12);
        color: #22c55e;
    }
    .dark .badge-pin {
        background: rgba(34,197,94,0.2);
        color: #4ade80;
    }
    .badge-no-pin {
        background: rgba(239,68,68,0.12);
        color: #ef4444;
    }
    .dark .badge-no-pin {
        background: rgba(239,68,68,0.2);
        color: #f87171;
    }

    .checkbox-column { width: 36px; text-align: center; }
    .checkbox-column input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .btn-pangkat {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 0.4rem 1.2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
    }
    .btn-pangkat:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(245,158,11,0.4);
    }
    .btn-pangkat:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .btn-outline {
        background: transparent;
        border: 1px solid #d1d5db;
        color: #4b5563;
        padding: 0.4rem 1.2rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-outline:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }
    .dark .btn-outline {
        color: #e5e7eb;
        border-color: #4b5563;
    }
    .dark .btn-outline:hover { background: #374151; }

    .btn-simpan {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        padding: 0.5rem 1.8rem;
        border-radius: 0.5rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-simpan:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(34,197,94,0.4);
    }
    .btn-simpan:disabled { opacity: 0.5; cursor: not-allowed; }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-box {
        background: white;
        border-radius: 1rem;
        max-width: 680px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 1.5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: fadeInUp 0.25s ease;
    }
    .dark .modal-box {
        background: #1f2937;
        color: #e5e7eb;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group { margin-bottom: 1rem; }
    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .form-control {
        width: 100%;
        padding: 0.5rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: white;
        font-size: 0.9rem;
        transition: 0.15s;
    }
    .dark .form-control {
        background: #374151;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }

    .selected-users-list {
        max-height: 140px;
        overflow-y: auto;
        background: #f3f4f6;
        padding: 0.4rem;
        border-radius: 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
    }
    .dark .selected-users-list { background: #374151; }
    .selected-users-list .user-badge {
        background: white;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        border: 1px solid #e5e7eb;
    }
    .dark .selected-users-list .user-badge {
        background: #1f2937;
        border-color: #4b5563;
    }

    .jabatan-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.3rem;
        max-height: 160px;
        overflow-y: auto;
        padding: 0.4rem;
        background: #f9fafb;
        border-radius: 0.5rem;
    }
    .dark .jabatan-grid { background: #374151; }
    .jabatan-grid label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        cursor: pointer;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }
    .jabatan-grid label:hover { background: #f3f4f6; }
    .dark .jabatan-grid label:hover { background: #4b5563; }

    .pagination-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;
    }
    .pagination-controls button {
        padding: 0.3rem 0.7rem;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        font-size: 0.8rem;
        transition: 0.15s;
    }
    .dark .pagination-controls button {
        background: #1f2937;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    .pagination-controls button:hover:not(:disabled) { background: #f3f4f6; }
    .dark .pagination-controls button:hover:not(:disabled) { background: #374151; }
    .pagination-controls .active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    .dark .pagination-controls .active {
        background: #818cf8;
        border-color: #818cf8;
        color: #111827;
    }
    .pagination-controls button:disabled { opacity: 0.4; cursor: not-allowed; }

    .toast-container {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .toast {
        padding: 0.6rem 1.2rem;
        border-radius: 0.5rem;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 200px;
    }
    .toast-success { background: #22c55e; }
    .toast-error { background: #ef4444; }
    .toast-info { background: #3b82f6; }
    .toast .toast-close {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        cursor: pointer;
        font-size: 1.1rem;
        margin-left: auto;
        padding: 0 0.2rem;
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .hidden { display: none !important; }
    .text-center { text-align: center; }
    .text-gray-500 { color: #6b7280; }
    .dark .text-gray-500 { color: #9ca3af; }
    .text-red-500 { color: #ef4444; }
    .text-green-500 { color: #22c55e; }
    .text-yellow-500 { color: #f59e0b; }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .font-bold { font-weight: 700; }
    .font-semibold { font-weight: 600; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-3 { margin-top: 0.75rem; }
    .mt-4 { margin-top: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 0.75rem; }
    .flex { display: flex; }
    .flex-wrap { flex-wrap: wrap; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .justify-end { justify-content: flex-end; }
    .w-full { width: 100%; }

    .spinner-border {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        border: 2px solid rgba(255,255,255,0.3);
        border-top: 2px solid #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .security-check-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.3rem;
        padding: 0.6rem 1rem;
        background: #eff6ff;
        border-radius: 0.5rem;
        border: 1px solid #bfdbfe;
    }
    .dark .security-check-wrap {
        background: #1e293b;
        border-color: #334155;
    }
    .security-check-wrap input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #3b82f6;
    }
    .security-check-wrap label {
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        color: #1e40af;
    }
    .dark .security-check-wrap label {
        color: #93c5fd;
    }

    .security-fields {
        margin-top: 0.75rem;
        padding: 0.8rem 1rem;
        background: #f3f4f6;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
    }
    .dark .security-fields {
        background: #374151;
        border-color: #4b5563;
    }
    .security-fields .form-group:last-child { margin-bottom: 0; }

    .pin-required-badge {
        display: inline-block;
        padding: 0.1rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        background: #fef3c7;
        color: #92400e;
        margin-left: 0.5rem;
    }
    .dark .pin-required-badge {
        background: #1f2937;
        color: #fbbf24;
        border: 1px solid #f59e0b;
    }

    .pin-warning-box {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        margin-top: 0.5rem;
    }
    .dark .pin-warning-box {
        background: #1f2937;
        border-color: #f59e0b;
    }
    .pin-warning-box .pin-warning-title {
        font-weight: 600;
        color: #92400e;
    }
    .dark .pin-warning-box .pin-warning-title {
        color: #fbbf24;
    }
    .pin-warning-box .pin-warning-text {
        font-size: 0.85rem;
        color: #78350f;
    }
    .dark .pin-warning-box .pin-warning-text {
        color: #fcd34d;
    }

    @media (max-width: 640px) {
        .promote-container { padding: 1rem; }
        .stats-bar { gap: 0.75rem; font-size: 0.8rem; }
        .modal-box { padding: 1rem; }
        .jabatan-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="promote-container">

    {{-- HEADER --}}
    <div class="promote-header">
        <div>
            <h3>
                <i class="fas fa-arrows-alt-v text-amber-500"></i>
                Manajemen Pangkat & Jabatan
            </h3>
            <p>Kelola level dan jabatan seluruh user. <span class="text-red-500">* Owner tidak dapat diubah.</span></p>
        </div>
        <div class="flex items-center gap-2">
            <button id="toggle-checkbox" class="btn-outline text-sm">
                <i class="fas fa-check-square"></i> Pilih
            </button>
            <button id="btn-pangkat" class="btn-pangkat text-sm" disabled>
                <i class="fas fa-crown"></i> Pangkat <span id="selected-badge" class="bg-white/20 px-2 py-0.5 rounded-full text-xs ml-1">0</span>
            </button>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats-bar">
        <div class="stat-item"><i class="fas fa-users text-gray-400"></i> Total User: <span class="stat-number" id="total-users">0</span></div>
        <div class="stat-item"><i class="fas fa-check-circle text-green-500"></i> Dipilih: <span class="stat-number" id="selected-stats">0</span></div>
        <div class="stat-item"><i class="fas fa-shield-alt text-amber-500"></i> Superadmin: <span class="stat-number" id="superadmin-count">0</span></div>
        <div class="stat-item"><i class="fas fa-key text-blue-500"></i> Ada PIN: <span class="stat-number" id="pin-count">0</span></div>
    </div>

    {{-- TABLE --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="checkbox-column hidden" id="th-checkbox"><input type="checkbox" id="select-all"></th>
                    <th class="text-center" style="width:40px;">#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Jabatan</th>
                    <th>PIN</th>
                    <th>Poin</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <tr><td colspan="10" class="text-center py-6 text-gray-500">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="flex flex-wrap justify-between items-center gap-2 mt-4 text-sm text-gray-500 dark:text-gray-400">
        <span id="info-text">Menampilkan 0 dari 0 user</span>
        <div id="pagination-controls" class="pagination-controls"></div>
    </div>
</div>

{{-- ===== MODAL PANGKAT ===== --}}
<div id="pangkat-modal" class="modal-backdrop">
    <div class="modal-box">
        <h3 class="font-bold text-lg flex items-center gap-2">
            <i class="fas fa-crown text-amber-500"></i> Atur Pangkat / Jabatan
        </h3>

        <div class="form-group mt-4">
            <label>User Terpilih (<span id="selected-count">0</span>)</label>
            <div id="selected-users-list" class="selected-users-list"></div>
        </div>

        <div class="form-group">
            <label>Aksi</label>
            <select id="action-select" class="form-control">
                <option value="promote">⬆️ Naikkan Level</option>
                <option value="demote">⬇️ Turunkan Level</option>
                <option value="add_position">➕ Tambah Jabatan</option>
                <option value="remove_position">➖ Kurangi Jabatan</option>
            </select>
        </div>

        <div id="target-role-group" class="form-group">
            <label>Target Role</label>
            <select id="target-role" class="form-control">
                <option value="superadmin">Superadmin</option>
                <option value="ketua">Ketua</option>
                <option value="pengurus">Pengurus</option>
                <option value="anggota">Anggota UKM</option>
                <option value="anggota_umum">Anggota Umum</option>
                <option value="pengunjung">Pengunjung</option>
            </select>
        </div>

        {{-- PASSWORD OWNER (muncul jika target role = superadmin) --}}
        <div id="pin-superadmin-group" class="form-group pin-superadmin-group hidden">
            <label class="text-amber-600 dark:text-amber-400">
                <i class="fas fa-shield-alt"></i> Password Owner <span class="text-red-500">*</span>
            </label>
            <input type="password" id="pin-superadmin" class="form-control" placeholder="Masukkan password Owner Anda" maxlength="255">
            <p class="text-xs text-gray-400 mt-1">Masukkan password akun Owner sebagai verifikasi keamanan.</p>
        </div>

        {{-- PIN WARNING (muncul jika target role = superadmin) --}}
        <div id="pin-warning-group" class="pin-warning-box hidden">
            <div class="pin-warning-title">
                <i class="fas fa-exclamation-triangle text-yellow-500"></i> Perhatian: Superadmin Membutuhkan PIN!
            </div>
            <div class="pin-warning-text mt-1">
                Untuk menjadi Superadmin, setiap user harus memiliki PIN baru. Silakan isi PIN di bawah.
            </div>
        </div>

        {{-- JABATAN (muncul jika aksi add/remove position) --}}
        <div id="jabatan-group" class="form-group hidden">
            <label>Pilih Jabatan</label>
            <div id="jabatan-list" class="jabatan-grid"></div>
        </div>

        {{-- UPDATE SECURITY --}}
        <div class="form-group mt-3">
            <div class="security-check-wrap">
                <input type="checkbox" id="update-security-check">
                <label for="update-security-check">
                    <i class="fas fa-shield-alt text-blue-500"></i> Perbarui Keamanan Akun
                    <span class="text-xs text-gray-400">(Password & PIN)</span>
                </label>
            </div>
            <div id="security-fields" class="security-fields hidden">
                <div class="form-group">
                    <label>Password Baru <span class="text-xs text-gray-400">(opsional, min 4 karakter)</span></label>
                    <input type="text" id="new-password" class="form-control" placeholder="Masukkan password baru">
                </div>
                <div class="form-group">
                    <label>
                        PIN Baru 
                        <span id="pin-required-label" class="hidden text-red-500">*</span>
                        <span class="text-xs text-gray-400">(min 4, max 10 karakter)</span>
                    </label>
                    <input type="text" id="new-pin" class="form-control" placeholder="Masukkan PIN baru">
                    <p class="text-xs text-gray-400 mt-1" id="pin-helper-text">* Kosongkan jika tidak ingin mengubah PIN.</p>
                </div>
            </div>
        </div>

        <div id="modal-error" class="hidden text-red-600 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg mb-3"></div>

        <div class="flex justify-end gap-3 mt-6">
            <button id="btn-modal-batal" class="btn-outline">Batal</button>
            <button id="btn-modal-simpan" class="btn-simpan"><i class="fas fa-save mr-2"></i> Simpan</button>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div class="toast-container" id="toast-container"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================================================
    // DOM REFS
    // ============================================================
    const tableBody = document.getElementById('user-table-body');
    const toggleCheckbox = document.getElementById('toggle-checkbox');
    const btnPangkat = document.getElementById('btn-pangkat');
    const selectAll = document.getElementById('select-all');
    const thCheckbox = document.getElementById('th-checkbox');
    const infoText = document.getElementById('info-text');
    const paginationControls = document.getElementById('pagination-controls');
    const selectedBadge = document.getElementById('selected-badge');
    const selectedStats = document.getElementById('selected-stats');
    const totalUsersEl = document.getElementById('total-users');
    const superadminCountEl = document.getElementById('superadmin-count');
    const pinCountEl = document.getElementById('pin-count');

    const modal = document.getElementById('pangkat-modal');
    const selectedUsersList = document.getElementById('selected-users-list');
    const selectedCount = document.getElementById('selected-count');
    const actionSelect = document.getElementById('action-select');
    const targetRoleGroup = document.getElementById('target-role-group');
    const targetRole = document.getElementById('target-role');
    const pinSuperadminGroup = document.getElementById('pin-superadmin-group');
    const pinSuperadminInput = document.getElementById('pin-superadmin');
    const pinWarningGroup = document.getElementById('pin-warning-group');
    const pinRequiredLabel = document.getElementById('pin-required-label');
    const pinHelperText = document.getElementById('pin-helper-text');
    const jabatanGroup = document.getElementById('jabatan-group');
    const jabatanList = document.getElementById('jabatan-list');
    const updateSecurityCheck = document.getElementById('update-security-check');
    const securityFields = document.getElementById('security-fields');
    const newPassword = document.getElementById('new-password');
    const newPin = document.getElementById('new-pin');
    const modalError = document.getElementById('modal-error');
    const btnSimpan = document.getElementById('btn-modal-simpan');
    const btnBatal = document.getElementById('btn-modal-batal');

    const toastContainer = document.getElementById('toast-container');

    // ============================================================
    // STATE
    // ============================================================
    let allUsers = [];
    let selectedIds = new Set();
    let isCheckboxMode = false;
    let currentPage = 1;
    const perPage = 15;
    let isLoading = false;
    let isSaving = false;

    // ============================================================
    // TOAST
    // ============================================================
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icons = { success: '✅', error: '❌', info: 'ℹ️' };
        toast.innerHTML = `<span>${icons[type] || 'ℹ️'} ${message}</span>
            <button class="toast-close">&times;</button>`;
        toastContainer.appendChild(toast);
        toast.querySelector('.toast-close').addEventListener('click', () => toast.remove());
        setTimeout(() => toast.remove(), 4000);
    }

    // ============================================================
    // FETCH DATA
    // ============================================================
    function fetchUsers() {
        if (isLoading) return;
        isLoading = true;
        tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-6"><div class="spinner-border"></div><p class="mt-2 text-gray-500">Memuat data...</p></td></tr>`;

        fetch('{{ route("owner.promote-demote.data") }}')
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!Array.isArray(data)) throw new Error('Data bukan array');
                allUsers = data;
                renderTable();
                updateStats();
                showToast('Data berhasil dimuat', 'success');
            })
            .catch(err => {
                console.error('Error:', err);
                tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-6 text-red-500">❌ Gagal memuat data: ${err.message}</td></tr>`;
                showToast('Gagal memuat data', 'error');
            })
            .finally(() => { isLoading = false; });
    }

    // ============================================================
    // RENDER TABLE
    // ============================================================
    function renderTable() {
        const total = allUsers.length;
        if (total === 0) {
            tableBody.innerHTML = `<tr><td colspan="10" class="text-center py-6 text-gray-500">Tidak ada user</td></tr>`;
            infoText.textContent = 'Menampilkan 0 dari 0 user';
            paginationControls.innerHTML = '';
            return;
        }

        const totalPages = Math.ceil(total / perPage);
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const end = Math.min(start + perPage, total);
        const pageData = allUsers.slice(start, end);

        infoText.textContent = `Menampilkan ${start+1} - ${end} dari ${total} user`;

        let html = '';
        pageData.forEach((user, idx) => {
            const num = start + idx + 1;
            const checked = selectedIds.has(user.id) ? 'checked' : '';
            const isOwner = user.level_name === 'owner';
            const isSuperadmin = user.level_name === 'superadmin';
            const hasPin = user.has_pin;
            const checkboxHtml = isCheckboxMode ?
                `<td class="checkbox-column"><input type="checkbox" class="user-checkbox" data-id="${user.id}" ${checked} ${isOwner ? 'disabled' : ''}></td>` :
                '';
            html += `<tr>
                ${checkboxHtml}
                <td class="text-center">${num}</td>
                <td>${user.nim || '-'}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge-role">${user.level_name}</span></td>
                <td>${user.jabatan_names || '-'}</td>
                <td>
                    ${hasPin 
                        ? '<span class="badge-pin"><i class="fas fa-check-circle"></i> Ada</span>'
                        : '<span class="badge-pin badge-no-pin"><i class="fas fa-times-circle"></i> Tidak Ada</span>'
                    }
                </td>
                <td>${user.total_points || 0}</td>
                <td>${user.is_active ? 'Aktif' : 'Nonaktif'}</td>
            </tr>`;
        });
        tableBody.innerHTML = html;

        document.querySelectorAll('.checkbox-column').forEach(el => el.classList.toggle('hidden', !isCheckboxMode));
        thCheckbox.classList.toggle('hidden', !isCheckboxMode);

        document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(cb => {
            cb.removeEventListener('change', handleCheckboxChange);
            cb.addEventListener('change', handleCheckboxChange);
        });
        selectAll.removeEventListener('change', handleSelectAll);
        selectAll.addEventListener('change', handleSelectAll);

        let pagHtml = '';
        if (totalPages > 1) {
            pagHtml += `<button data-page="${currentPage-1}" ${currentPage===1?'disabled':''}>‹</button>`;
            for (let i = 1; i <= totalPages; i++) {
                pagHtml += `<button class="${i===currentPage?'active':''}" data-page="${i}">${i}</button>`;
            }
            pagHtml += `<button data-page="${currentPage+1}" ${currentPage===totalPages?'disabled':''}>›</button>`;
        }
        paginationControls.innerHTML = pagHtml;
        paginationControls.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= totalPages && page !== currentPage) {
                    currentPage = page;
                    renderTable();
                }
            });
        });

        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    // ============================================================
    // CHECKBOX HANDLING
    // ============================================================
    function handleCheckboxChange(e) {
        const id = parseInt(e.target.dataset.id);
        if (e.target.checked) selectedIds.add(id);
        else selectedIds.delete(id);
        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    function handleSelectAll(e) {
        const checked = e.target.checked;
        document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = checked;
            const id = parseInt(cb.dataset.id);
            if (checked) selectedIds.add(id);
            else selectedIds.delete(id);
        });
        updatePangkatButton();
        updateSelectAllState();
        updateStats();
    }

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
        const checked = document.querySelectorAll('.user-checkbox:not(:disabled):checked');
        selectAll.checked = checkboxes.length > 0 && checkboxes.length === checked.length;
    }

    function updatePangkatButton() {
        const count = selectedIds.size;
        btnPangkat.disabled = count === 0;
        selectedBadge.textContent = count;
        selectedStats.textContent = count;
    }

    function updateStats() {
        totalUsersEl.textContent = allUsers.length;
        selectedStats.textContent = selectedIds.size;
        selectedBadge.textContent = selectedIds.size;
        const superadmins = allUsers.filter(u => u.level_name === 'superadmin');
        superadminCountEl.textContent = superadmins.length;
        const hasPin = allUsers.filter(u => u.has_pin === true);
        pinCountEl.textContent = hasPin.length;
    }

    // ============================================================
    // MODAL
    // ============================================================
    function showModal(users) {
        selectedUsersList.innerHTML = users.map(u =>
            `<span class="user-badge">${u.name}</span>`
        ).join('');
        selectedCount.textContent = users.length;
        modalError.classList.add('hidden');
        pinSuperadminInput.value = '';
        newPassword.value = '';
        newPin.value = '';
        updateSecurityCheck.checked = false;
        securityFields.classList.add('hidden');
        pinWarningGroup.classList.add('hidden');
        pinRequiredLabel.classList.add('hidden');
        pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
        actionSelect.value = 'promote';
        toggleModalFields();
        fetchJabatans();
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function toggleModalFields() {
        const val = actionSelect.value;
        const targetRoleVal = targetRole.value;
        const isSuperadminTarget = targetRoleVal === 'superadmin';

        // Target role group
        if (val === 'promote' || val === 'demote') {
            targetRoleGroup.classList.remove('hidden');
            if (isSuperadminTarget) {
                pinSuperadminGroup.classList.remove('hidden');
                pinWarningGroup.classList.remove('hidden');
                pinRequiredLabel.classList.remove('hidden');
                pinHelperText.textContent = '* Wajib diisi untuk Superadmin baru.';
                // Auto-check security
                updateSecurityCheck.checked = true;
                securityFields.classList.remove('hidden');
            } else {
                pinSuperadminGroup.classList.add('hidden');
                pinWarningGroup.classList.add('hidden');
                pinRequiredLabel.classList.add('hidden');
                pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
            }
        } else {
            targetRoleGroup.classList.add('hidden');
            pinSuperadminGroup.classList.add('hidden');
            pinWarningGroup.classList.add('hidden');
            pinRequiredLabel.classList.add('hidden');
            pinHelperText.textContent = '* Kosongkan jika tidak ingin mengubah PIN.';
        }

        // Jabatan group
        if (val === 'add_position' || val === 'remove_position') {
            jabatanGroup.classList.remove('hidden');
        } else {
            jabatanGroup.classList.add('hidden');
        }
    }

    function fetchJabatans() {
        fetch('{{ route("owner.promote-demote.jabatans") }}')
            .then(res => res.json())
            .then(data => {
                jabatanList.innerHTML = data.map(j =>
                    `<label><input type="checkbox" name="jabatan_ids[]" value="${j.id}"> ${j.name}</label>`
                ).join('');
            })
            .catch(() => showToast('Gagal memuat jabatan', 'error'));
    }

    // ============================================================
    // SAVE
    // ============================================================
    function handleSave() {
        if (isSaving) return;

        const action = actionSelect.value;
        const userIds = Array.from(selectedIds);
        const targetRoleVal = targetRole.value;
        const pinSuperadmin = pinSuperadminInput.value.trim();
        const updateSecurity = updateSecurityCheck.checked;
        const newPass = newPassword.value.trim();
        const newPinVal = newPin.value.trim();
        const isSuperadminTarget = targetRoleVal === 'superadmin';

        // Validasi user
        if (userIds.length === 0) {
            showToast('Tidak ada user yang dipilih.', 'error');
            return;
        }

        // 🔥 Validasi Password Owner untuk Superadmin
        if ((action === 'promote' && isSuperadminTarget) || (action === 'add_position' && isSuperadminTarget)) {
            if (!pinSuperadmin) {
                modalError.textContent = '⚠️ Masukkan password Owner untuk verifikasi keamanan.';
                modalError.classList.remove('hidden');
                return;
            }
        }

        // 🔥 Validasi PIN untuk Superadmin baru
        if (action === 'promote' && isSuperadminTarget) {
            if (!newPinVal || newPinVal.length < 4) {
                modalError.textContent = '⚠️ PIN wajib diisi minimal 4 karakter untuk Superadmin baru.';
                modalError.classList.remove('hidden');
                return;
            }
        }

        // Validasi jabatan
        if (action === 'add_position' || action === 'remove_position') {
            const checked = document.querySelectorAll('#jabatan-list input:checked');
            if (checked.length === 0) {
                modalError.textContent = '⚠️ Pilih minimal satu jabatan.';
                modalError.classList.remove('hidden');
                return;
            }
        }

        // Validasi security fields
        if (updateSecurity) {
            if (!newPass && !newPinVal) {
                modalError.textContent = '⚠️ Isi password atau PIN baru jika ingin memperbarui keamanan.';
                modalError.classList.remove('hidden');
                return;
            }
            if (newPass && newPass.length < 4) {
                modalError.textContent = '⚠️ Password minimal 4 karakter.';
                modalError.classList.remove('hidden');
                return;
            }
            if (newPinVal && (newPinVal.length < 4 || newPinVal.length > 10)) {
                modalError.textContent = '⚠️ PIN harus 4-10 karakter.';
                modalError.classList.remove('hidden');
                return;
            }
        }

        // 🔥 Cek duplikat role
        const selectedUsers = allUsers.filter(u => userIds.includes(u.id));
        if ((action === 'promote' || action === 'add_position') && isSuperadminTarget) {
            const hasSuperadmin = selectedUsers.some(u => u.level_name === 'superadmin');
            if (hasSuperadmin) {
                modalError.textContent = '⚠️ User sudah memiliki role Superadmin. Tidak dapat memilih role yang sama.';
                modalError.classList.remove('hidden');
                return;
            }
        }

        if (!confirm(`Yakin ingin ${action === 'promote' ? 'menaikkan' : action === 'demote' ? 'menurunkan' : action === 'add_position' ? 'menambah' : 'mengurangi'} pangkat/jabatan untuk ${userIds.length} user?`)) return;

        isSaving = true;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border"></span> Menyimpan...';
        modalError.classList.add('hidden');

        const payload = {
            user_ids: userIds,
            action: action,
            target_role: targetRoleVal,
            jabatan_ids: Array.from(document.querySelectorAll('#jabatan-list input:checked')).map(cb => parseInt(cb.value)),
            pin_superadmin: pinSuperadmin,
            update_security: updateSecurity,
            new_password: newPass,
            new_pin: newPinVal,
            _token: '{{ csrf_token() }}'
        };

        fetch('{{ route("owner.promote-demote.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                showToast(response.message, 'success');
                closeModal();
                selectedIds.clear();
                fetchUsers();
                updatePangkatButton();
            } else {
                modalError.textContent = '❌ ' + (response.message || 'Terjadi kesalahan.');
                modalError.classList.remove('hidden');
                showToast(response.message || 'Gagal', 'error');
            }
        })
        .catch(err => {
            modalError.textContent = '❌ Gagal menghubungi server.';
            modalError.classList.remove('hidden');
            showToast('Gagal menghubungi server', 'error');
        })
        .finally(() => {
            isSaving = false;
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan';
        });
    }

    // ============================================================
    // EVENT LISTENERS
    // ============================================================
    toggleCheckbox.addEventListener('click', function() {
        isCheckboxMode = !isCheckboxMode;
        this.innerHTML = isCheckboxMode ? '<i class="fas fa-times"></i> Batal' : '<i class="fas fa-check-square"></i> Pilih';
        renderTable();
    });

    btnPangkat.addEventListener('click', function() {
        if (selectedIds.size === 0) return;
        const selectedUsers = allUsers.filter(u => selectedIds.has(u.id));
        if (selectedUsers.some(u => u.level_name === 'owner')) {
            showToast('Tidak dapat mengubah Owner!', 'error');
            return;
        }
        showModal(selectedUsers);
    });

    btnBatal.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === this) closeModal(); });

    actionSelect.addEventListener('change', function() {
        toggleModalFields();
    });

    targetRole.addEventListener('change', function() {
        toggleModalFields();
    });

    updateSecurityCheck.addEventListener('change', function() {
        securityFields.classList.toggle('hidden', !this.checked);
    });

    btnSimpan.addEventListener('click', handleSave);

    // ============================================================
    // INIT
    // ============================================================
    fetchUsers();
});
</script>
@endpush
@endsection