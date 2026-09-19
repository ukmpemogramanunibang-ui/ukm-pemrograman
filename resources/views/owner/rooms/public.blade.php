@extends('layouts.roles.owner')

@section('title', 'Publik QR Code – Owner')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 100%);
        pointer-events: none;
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.12);
    }
    .stat-card .icon-wrapper {
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .stat-card:hover .icon-wrapper {
        transform: scale(1.15) rotate(-8deg);
    }
    .stat-card .stat-value {
        transition: all 0.3s;
    }
    .stat-card:hover .stat-value {
        transform: scale(1.05);
    }
    .fade-in {
        animation: fadeInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        opacity: 0;
    }
    .fade-in:nth-child(2) { animation-delay: 0.05s; }
    .fade-in:nth-child(3) { animation-delay: 0.1s; }
    .fade-in:nth-child(4) { animation-delay: 0.15s; }
    .fade-in:nth-child(5) { animation-delay: 0.2s; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-row {
        transition: all 0.2s ease;
    }
    .table-row:hover {
        background: rgba(6, 182, 212, 0.04);
    }

    .badge-pulse {
        animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .modal-enter {
        animation: modalSlideIn 0.3s cubic-bezier(0.22, 1, 0.36, 1);
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: scale(0.95) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .copy-btn {
        transition: all 0.2s;
    }
    .copy-btn.copied {
        background: #10b981 !important;
        color: white !important;
    }

    .qr-preview {
        transition: all 0.3s;
        cursor: pointer;
    }
    .qr-preview:hover {
        transform: scale(1.5);
        z-index: 10;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    /* Dark mode adjustments */
    .dark .skeleton {
        background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    }

    /* ===== BADGE ROLE ===== */
    .role-badge {
        display: inline-block;
        padding: 0.1rem 0.6rem;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 600;
        margin: 0.1rem 0.15rem;
        white-space: nowrap;
    }
    .role-badge.owner { background: #fef3c7; color: #92400e; }
    .role-badge.superadmin { background: #fecaca; color: #991b1b; }
    .role-badge.ketua { background: #e9d5ff; color: #6b21a8; }
    .role-badge.pengurus { background: #bfdbfe; color: #1e40af; }
    .role-badge.anggota { background: #d1fae5; color: #065f46; }
    .role-badge.umum { background: #e5e7eb; color: #374151; }
    .role-badge.publik { background: #dbeafe; color: #1e40af; }
    .role-badge.guest { background: #e5e7eb; color: #4b5563; }
    .dark .role-badge.owner { background: #78350f; color: #fbbf24; }
    .dark .role-badge.superadmin { background: #7f1d1d; color: #fca5a5; }
    .dark .role-badge.ketua { background: #4c1d95; color: #c4b5fd; }
    .dark .role-badge.pengurus { background: #1e3a5f; color: #93c5fd; }
    .dark .role-badge.anggota { background: #064e3b; color: #6ee7b7; }
    .dark .role-badge.umum { background: #374151; color: #9ca3af; }
    .dark .role-badge.publik { background: #1e3a5f; color: #93c5fd; }
    .dark .role-badge.guest { background: #374151; color: #9ca3af; }

    /* ===== DROPDOWN OVERLAY ===== */
    .dropdown-overlay {
        position: fixed;
        z-index: 9999;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        border: 1px solid #e2e8f0;
        padding: 6px 0;
        min-width: 180px;
        display: none;
        opacity: 0;
        transition: opacity 0.15s;
        pointer-events: none;
    }
    .dropdown-overlay.open {
        display: block;
        opacity: 1;
        pointer-events: auto;
    }
    .dropdown-overlay .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        color: #1e293b;
        text-decoration: none;
        font-size: 0.875rem;
        transition: background 0.15s;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        font-family: inherit;
    }
    .dropdown-overlay .dropdown-item:hover {
        background: #f1f5f9;
    }
    .dropdown-overlay .dropdown-item.danger:hover {
        background: #fef2f2;
        color: #dc2626;
    }
    .dark .dropdown-overlay {
        background: #1e293b;
        border-color: #334155;
    }
    .dark .dropdown-overlay .dropdown-item {
        color: #e2e8f0;
    }
    .dark .dropdown-overlay .dropdown-item:hover {
        background: #334155;
    }
    .dark .dropdown-overlay .dropdown-item.danger:hover {
        background: #7f1d1d;
        color: #fca5a5;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-full px-4 md:px-8 xl:px-12 mx-auto space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 fade-in">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 flex-wrap">
                <a href="{{ route('owner.dashboard') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition flex items-center gap-1">
                    <i class="fas fa-home text-xs"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <a href="{{ route('owner.rooms.index') }}" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition flex items-center gap-1">
                    <i class="fas fa-door-open text-xs"></i> Ruangan
                </a>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <span class="text-gray-700 dark:text-gray-300 font-medium flex items-center gap-1">
                    <i class="fas fa-globe text-cyan-500 text-xs"></i> Publik QR
                </span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3 mt-2">
                <span class="bg-gradient-to-r from-cyan-500 to-blue-500 p-2 rounded-xl text-white shadow-lg shadow-cyan-500/20">
                    <i class="fas fa-globe text-xl"></i>
                </span>
                Publik QR Code
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                <i class="fas fa-info-circle text-cyan-400 text-xs"></i>
                Kelola publikasi QR ruangan, akses publik, dan role yang diizinkan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-3 md:mt-0">
            <a href="{{ route('owner.rooms.public.history') }}" class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-history"></i> Riwayat
            </a>
            <button onclick="window.location.href='{{ route('owner.rooms.public.export') }}'" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-file-export"></i> Export
            </button>
            <a href="{{ route('owner.rooms.index') }}" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
            </div>
            <div class="flex-1">{!! session('success') !!}</div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3 fade-in">
            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            </div>
            <div class="flex-1">{!! session('error') !!}</div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 fade-in">
        @php
            $statCards = [
                ['key' => 'total', 'label' => 'Total Ruangan', 'icon' => 'fa-door-open', 'color' => 'blue', 'bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400'],
                ['key' => 'public', 'label' => '🌎 Publik', 'icon' => 'fa-globe', 'color' => 'emerald', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'sub' => 'Tanpa login', 'click' => 'public'],
                ['key' => 'login', 'label' => '🔐 Login + Role', 'icon' => 'fa-lock', 'color' => 'purple', 'bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'sub' => 'Memerlukan login', 'click' => 'login'],
                ['key' => 'both', 'label' => '🌍 Publik + Login', 'icon' => 'fa-globe-asia', 'color' => 'cyan', 'bg' => 'bg-cyan-100 dark:bg-cyan-900/30', 'text' => 'text-cyan-600 dark:text-cyan-400', 'sub' => 'Keduanya aktif', 'click' => 'both'],
                ['key' => 'not_published', 'label' => '⚪ Belum Publik', 'icon' => 'fa-eye-slash', 'color' => 'gray', 'bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-500 dark:text-gray-400', 'sub' => 'Belum dipublikasikan'],
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="stat-card bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 {{ isset($card['click']) ? 'hover:shadow-md' : '' }}"
                 @if(isset($card['click'])) onclick="showPopup('{{ $card['click'] }}')" @endif>
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400 truncate">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white stat-value">{{ $stats[$card['key']] ?? 0 }}</p>
                        @if(isset($card['sub']))
                            <p class="text-[9px] text-gray-400 truncate">{{ $card['sub'] }}</p>
                        @endif
                    </div>
                    <div class="icon-wrapper w-10 h-10 {{ $card['bg'] }} rounded-xl flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                        <i class="fas {{ $card['icon'] }} text-sm"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 fade-in">
        <form method="GET" action="{{ route('owner.rooms.public.index') }}" class="flex flex-wrap items-end gap-3" id="filterForm">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    <i class="fas fa-search mr-1"></i> Cari Ruangan
                </label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama, kode, gedung..."
                       class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status Publikasi</label>
                <select name="public_status" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-w-[140px]">
                    <option value="">Semua</option>
                    <option value="public" {{ request('public_status') == 'public' ? 'selected' : '' }}>🌎 Publik</option>
                    <option value="login" {{ request('public_status') == 'login' ? 'selected' : '' }}>🔐 Login</option>
                    <option value="both" {{ request('public_status') == 'both' ? 'selected' : '' }}>🌍 Keduanya</option>
                    <option value="none" {{ request('public_status') == 'none' ? 'selected' : '' }}>⚪ Belum Publik</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sortir</label>
                <select name="sort" class="px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition min-w-[130px]">
                    <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>Terlama</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                    <option value="published_at_desc" {{ request('sort') == 'published_at_desc' ? 'selected' : '' }}>Publikasi Terbaru</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('owner.rooms.public.index') }}" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <i class="fas fa-undo-alt"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ===== BULK ACTION ===== --}}
    <div class="flex flex-wrap items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-700 fade-in">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
            <i class="fas fa-layer-group text-cyan-500"></i> Aksi Massal:
        </span>
        <button onclick="bulkAction('publish')" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
            <i class="fas fa-globe"></i> Publikasikan
        </button>
        <button onclick="bulkAction('unpublish')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
            <i class="fas fa-eye-slash"></i> Nonaktifkan
        </button>
        <button onclick="bulkAction('export')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
            <i class="fas fa-file-export"></i> Export
        </button>
        <span id="selectedCount" class="text-sm text-gray-500 ml-auto">0 ruangan dipilih</span>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                        </th>
                        <th class="px-4 py-3 min-w-[180px]">Ruangan</th>
                        <th class="px-4 py-3 w-28">Kode</th>
                        <th class="px-4 py-3 text-center w-14">QR</th>
                        <th class="px-4 py-3 min-w-[130px]">Status Publikasi</th>
                        <th class="px-4 py-3 min-w-[180px]">Akses Role</th>
                        <th class="px-4 py-3 text-center w-28">Dibuat</th>
                        <th class="px-4 py-3 text-center w-52">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rooms ?? [] as $room)
                    <tr class="table-row" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}" data-room-token="{{ $room->room_token }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="row-checkbox rounded border-gray-300 text-cyan-600 focus:ring-cyan-500" value="{{ $room->id }}">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($room->images && count($room->images) > 0)
                                    <img src="{{ asset('storage/' . $room->images[0]) }}" alt="{{ $room->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-100 to-blue-100 dark:from-cyan-900/30 dark:to-blue-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 flex-shrink-0">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white text-sm truncate max-w-[200px]">{{ $room->name }}</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">
                                        <i class="fas fa-map-pin text-[8px]"></i> {{ $room->building ?? '-' }} • Lt.{{ $room->floor ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $room->room_code }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($room->has_qr && $room->qr_code)
                                <img src="{{ asset('storage/' . $room->qr_code) }}" alt="QR" class="w-10 h-10 mx-auto object-cover rounded border border-gray-200 dark:border-gray-600 qr-preview" title="Klik untuk lihat QR" onclick="previewQr('{{ asset('storage/' . $room->qr_code) }}', '{{ $room->name }}')">
                            @else
                                <span class="text-gray-400 text-xs"><i class="fas fa-times-circle"></i></span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $status = $room->public_status ?? 'none';
                            @endphp
                            @if($status == 'public')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/30">
                                    <i class="fas fa-globe mr-1"></i> Publik
                                </span>
                            @elseif($status == 'login')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-700/30">
                                    <i class="fas fa-lock mr-1"></i> Login
                                </span>
                            @elseif($status == 'both')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700/30">
                                    <i class="fas fa-globe-asia mr-1"></i> Keduanya
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                    <i class="fas fa-eye-slash mr-1"></i> Belum Publik
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $roles = null;
                                if ($room->public_roles) {
                                    $roles = is_array($room->public_roles) ? $room->public_roles : json_decode($room->public_roles, true);
                                }
                                if ($status == 'public' || $status == 'both') {
                                    $roles = $roles ?? [];
                                    if (!in_array('Publik', $roles)) $roles[] = 'Publik';
                                }
                                if (($status == 'login' || $status == 'both') && empty($roles)) {
                                    $roles = ['Semua Role'];
                                }
                                if ($status == 'none') {
                                    $roles = ['-'];
                                }
                            @endphp
                            <div class="flex flex-wrap gap-1">
                                @if(is_array($roles))
                                    @foreach($roles as $role)
                                        @php
                                            $roleClass = 'guest';
                                            if (strtolower($role) == 'owner') $roleClass = 'owner';
                                            elseif (strtolower($role) == 'superadmin') $roleClass = 'superadmin';
                                            elseif (strtolower($role) == 'ketua') $roleClass = 'ketua';
                                            elseif (strtolower($role) == 'pengurus') $roleClass = 'pengurus';
                                            elseif (strtolower($role) == 'anggota ukm') $roleClass = 'anggota';
                                            elseif (strtolower($role) == 'anggota umum') $roleClass = 'umum';
                                            elseif (strtolower($role) == 'publik') $roleClass = 'publik';
                                            elseif (strtolower($role) == 'semua role') $roleClass = 'publik';
                                        @endphp
                                        <span class="role-badge {{ $roleClass }}">{{ $role }}</span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $room->published_at ? $room->published_at->isoFormat('DD/MM/YYYY HH:mm') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center relative">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($status == 'none')
                                    <button onclick="showPublishModal({{ $room->id }}, '{{ $room->name }}')" class="px-3 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-1.5">
                                        <i class="fas fa-globe"></i> Publikasikan
                                    </button>
                                @else
                                    <button onclick="showPublishModal({{ $room->id }}, '{{ $room->name }}')" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-1.5">
                                        <i class="fas fa-edit"></i> Kelola
                                    </button>
                                    <button onclick="togglePublic({{ $room->id }}, '{{ $room->name }}')" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow flex items-center gap-1.5">
                                        <i class="fas fa-times"></i> Nonaktifkan
                                    </button>
                                @endif
                                {{-- Tombol tiga titik --}}
                                <button class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition action-dropdown-btn" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}" data-room-token="{{ $room->room_token }}" data-status="{{ $status }}">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-4xl text-gray-300 dark:text-gray-500 mb-4">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <p class="text-base font-medium text-gray-600 dark:text-gray-300">Belum ada ruangan yang dipublikasikan</p>
                                <p class="text-sm text-gray-400 mt-1">Mulai publikasikan QR ruangan agar dapat diakses oleh pengguna.</p>
                                <a href="{{ route('owner.rooms.create') }}" class="mt-4 px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Tambah Ruangan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ ($rooms ?? collect())->firstItem() ?? 0 }} - {{ ($rooms ?? collect())->lastItem() ?? 0 }} dari {{ ($rooms ?? collect())->total() ?? 0 }} data
            </div>
            <div>
                {{ ($rooms ?? collect())->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ===== DROPDOWN OVERLAY (diluar tabel) ===== --}}
<div id="dropdownOverlay" class="dropdown-overlay" onclick="event.stopPropagation();">
    <!-- diisi oleh JavaScript -->
</div>

{{-- ===== POPUP STATISTIK ===== --}}
<div id="statsPopup" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden" onclick="if(event.target===this) closePopup()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 modal-enter">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="popupTitle">Status Publikasi</h3>
            <button onclick="closePopup()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="space-y-3" id="popupContent">
            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">Total Ruangan</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                <span class="text-emerald-600 dark:text-emerald-400">🟢 Publik</span>
                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['public'] ?? 0 }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                <span class="text-purple-600 dark:text-purple-400">🔐 Login + Role</span>
                <span class="font-bold text-purple-600 dark:text-purple-400">{{ $stats['login'] ?? 0 }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                <span class="text-cyan-600 dark:text-cyan-400">🌍 Publik + Login</span>
                <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ $stats['both'] ?? 0 }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-500">⚪ Belum Publik</span>
                <span class="font-bold text-gray-500">{{ $stats['not_published'] ?? 0 }}</span>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Persentase Publikasi:</span>
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ ($stats['total'] ?? 0) > 0 ? round((($stats['public'] ?? 0) / ($stats['total'] ?? 1)) * 100) : 0 }}%
                </span>
            </div>
            <a href="{{ route('owner.rooms.public.index') }}?public_status=public" class="mt-3 block w-full text-center px-4 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-sm font-medium rounded-xl transition shadow-md hover:shadow-lg">
                <i class="fas fa-list mr-1"></i> Lihat Ruangan Publik
            </a>
        </div>
    </div>
</div>

{{-- ===== MODAL PUBLIKASI ===== --}}
<div id="publishModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden" onclick="if(event.target===this) closePublishModal()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-6 modal-enter max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-globe text-cyan-500"></i>
                Publikasikan Ruangan
            </h3>
            <button onclick="closePublishModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="publishForm" method="POST">
            @csrf
            <div class="p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl border border-cyan-200 dark:border-cyan-700/30 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-300" id="publishRoomName">
                    <i class="fas fa-door-open text-cyan-500 mr-1"></i> Ruangan: <span class="font-semibold text-gray-800 dark:text-white">-</span>
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-key mr-1"></i> Pilih Akses
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border-2 border-transparent hover:border-cyan-200 dark:hover:border-cyan-700/30">
                            <input type="checkbox" name="allow_public" value="1" class="mt-1 text-cyan-600 focus:ring-cyan-500 rounded" onchange="updatePreview()">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">🌎 Halaman Publik</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak membutuhkan login. Siapa pun dapat melihat.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border-2 border-transparent hover:border-purple-200 dark:hover:border-purple-700/30">
                            <input type="checkbox" name="allow_login" value="1" class="mt-1 text-purple-600 focus:ring-purple-500 rounded" onchange="updatePreview()">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">🔐 Halaman Terproteksi</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Membutuhkan login dengan role tertentu.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="rolesSection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-users-cog mr-1"></i> Role yang Diizinkan
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Owner', 'Superadmin', 'Ketua', 'Pengurus', 'Anggota UKM', 'Anggota Umum', 'Pengunjung'] as $role)
                        <label class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border border-transparent hover:border-purple-200 dark:hover:border-purple-700/30">
                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="text-purple-600 focus:ring-purple-500 rounded" onchange="updatePreview()">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="document.querySelectorAll('#rolesSection input[type=checkbox]').forEach(cb => cb.checked = true); updatePreview();" 
                                class="text-xs text-blue-500 hover:text-blue-600 hover:underline transition">
                            <i class="fas fa-check-double"></i> Pilih Semua
                        </button>
                        <button type="button" onclick="document.querySelectorAll('#rolesSection input[type=checkbox]').forEach(cb => cb.checked = false); updatePreview();" 
                                class="text-xs text-red-500 hover:text-red-600 hover:underline transition">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-table mr-1"></i> Data yang Ditampilkan
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Nama Ruangan', 'Kode Ruangan', 'Foto Ruangan', 'Alamat', 'Gedung', 'Lantai', 'Kapasitas', 'Fasilitas', 'Status', 'Jadwal'] as $field)
                        <label class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/30 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border border-transparent hover:border-cyan-200 dark:hover:border-cyan-700/30">
                            <input type="checkbox" name="fields[]" value="{{ $field }}" checked class="text-cyan-600 focus:ring-cyan-500 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $field }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/10 dark:to-cyan-900/10 border border-blue-200 dark:border-blue-700/30 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-3 flex items-center gap-2">
                        <i class="fas fa-eye"></i> Preview Akses
                    </h4>
                    <div class="space-y-1 text-sm" id="previewAccess">
                        <div class="flex justify-between py-1 border-b border-blue-100 dark:border-blue-800/30">
                            <span class="text-gray-600 dark:text-gray-400">🌎 Pengunjung tanpa akun</span>
                            <span id="previewGuest" class="text-red-500 font-medium">❌ Tidak</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-blue-100 dark:border-blue-800/30">
                            <span class="text-gray-600 dark:text-gray-400">👤 Owner</span>
                            <span id="previewOwner" class="text-red-500 font-medium">❌ Tidak</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-blue-100 dark:border-blue-800/30">
                            <span class="text-gray-600 dark:text-gray-400">👤 Superadmin</span>
                            <span id="previewSuperadmin" class="text-red-500 font-medium">❌ Tidak</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-blue-100 dark:border-blue-800/30">
                            <span class="text-gray-600 dark:text-gray-400">👤 Ketua</span>
                            <span id="previewKetua" class="text-red-500 font-medium">❌ Tidak</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600 dark:text-gray-400">👤 Anggota UKM</span>
                            <span id="previewAnggota" class="text-red-500 font-medium">❌ Tidak</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Publikasi
                </button>
                <button type="button" onclick="closePublishModal()" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-xl transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL PREVIEW QR ===== --}}
<div id="qrPreviewModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 hidden" onclick="if(event.target===this) closeQrPreview()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 modal-enter max-w-sm w-full text-center">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="qrPreviewTitle">QR Code</h3>
            <button onclick="closeQrPreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <img id="qrPreviewImage" src="" alt="QR Code" class="w-48 h-48 mx-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <p id="qrPreviewName" class="mt-3 text-sm text-gray-600 dark:text-gray-300 font-medium"></p>
        <button onclick="closeQrPreview()" class="mt-4 px-5 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition">
            Tutup
        </button>
    </div>
</div>

{{-- ===== JAVASCRIPT ===== --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== SELECT ALL =====
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectedCount = document.getElementById('selectedCount');

    function updateCounter() {
        const checked = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCount.textContent = checked + ' ruangan dipilih';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateCounter();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateCounter();
            if (selectAll) {
                selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            }
        });
    });

    updateCounter();

    // ===== AUTO HIDE FLASH =====
    setTimeout(() => {
        document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s, transform 0.5s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(() => el.remove(), 500);
            }, 5000);
        });
    }, 100);
});

// ===== DROPDOWN OVERLAY =====
(function() {
    const overlay = document.getElementById('dropdownOverlay');
    let currentTarget = null;

    function closeDropdown() {
        overlay.classList.remove('open');
        if (currentTarget) {
            currentTarget.classList.remove('active');
            currentTarget = null;
        }
    }

    function openDropdown(event, roomId, roomName, roomToken, status) {
        event.stopPropagation();
        const btn = event.currentTarget;
        const rect = btn.getBoundingClientRect();

        // Tentukan konten dropdown
        const isPublished = status !== 'none';
        const items = [];

        items.push({
            icon: 'fas fa-eye',
            label: 'Detail',
            href: '/owner/rooms/' + roomId,
            className: ''
        });
        items.push({
            icon: 'fas fa-edit',
            label: 'Edit',
            href: '/owner/rooms/' + roomId + '/edit',
            className: ''
        });
        items.push({
            icon: 'fas fa-copy',
            label: 'Salin Link',
            action: 'copyLink("' + roomToken + '")',
            className: ''
        });

        if (isPublished) {
            items.push({
                icon: 'fas fa-times',
                label: 'Nonaktifkan',
                action: 'togglePublic(' + roomId + ', "' + roomName.replace(/"/g, '\\"') + '")',
                className: 'danger'
            });
        }

        // Bangun HTML
        let html = '';
        items.forEach(item => {
            if (item.href) {
                html += `<a href="${item.href}" class="dropdown-item ${item.className}"><i class="${item.icon} w-5"></i> ${item.label}</a>`;
            } else if (item.action) {
                html += `<button onclick="${item.action}; closeDropdown();" class="dropdown-item ${item.className}"><i class="${item.icon} w-5"></i> ${item.label}</button>`;
            }
        });

        overlay.innerHTML = html;
        overlay.style.left = Math.min(rect.left - 10, window.innerWidth - 200) + 'px';
        overlay.style.top = Math.min(rect.bottom + 4, window.innerHeight - overlay.offsetHeight - 10) + 'px';
        overlay.classList.add('open');
        currentTarget = btn;
    }

    // Pasang event listener ke semua tombol aksi
    document.querySelectorAll('.action-dropdown-btn').forEach(btn => {
        btn.addEventListener('click', function(event) {
            const roomId = this.dataset.roomId;
            const roomName = this.dataset.roomName;
            const roomToken = this.dataset.roomToken;
            const status = this.dataset.status;

            // Tutup dropdown yang sedang terbuka
            if (overlay.classList.contains('open') && currentTarget === this) {
                closeDropdown();
                return;
            }
            closeDropdown();
            openDropdown(event, roomId, roomName, roomToken, status);
        });
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        if (!overlay.contains(e.target) && !e.target.closest('.action-dropdown-btn')) {
            closeDropdown();
        }
    });

    // Tutup dropdown saat scroll
    window.addEventListener('scroll', closeDropdown);
    window.addEventListener('resize', closeDropdown);

    // Ekspos ke global
    window.closeDropdown = closeDropdown;
})();

// ===== POPUP STATISTIK =====
function showPopup(type) {
    const popup = document.getElementById('statsPopup');
    const title = document.getElementById('popupTitle');
    const content = document.getElementById('popupContent');

    const titles = {
        'public': '🌎 Status Publikasi - Publik',
        'login': '🔐 Status Publikasi - Login + Role',
        'both': '🌍 Status Publikasi - Keduanya'
    };
    title.textContent = titles[type] || 'Status Publikasi';

    content.querySelectorAll('.flex.justify-between').forEach(el => {
        el.style.background = 'transparent';
    });
    const idx = { 'public': 1, 'login': 2, 'both': 3 }[type];
    if (idx !== undefined) {
        const rows = content.querySelectorAll('.flex.justify-between');
        if (rows[idx]) {
            rows[idx].style.background = 'rgba(6, 182, 212, 0.08)';
            rows[idx].style.borderRadius = '8px';
            rows[idx].style.padding = '4px 8px';
        }
    }

    popup.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('statsPopup').classList.add('hidden');
    document.body.style.overflow = '';
}

// ===== MODAL PUBLIKASI =====
function showPublishModal(roomId, roomName) {
    const modal = document.getElementById('publishModal');
    document.getElementById('publishRoomName').innerHTML = '<i class="fas fa-door-open text-cyan-500 mr-1"></i> Ruangan: <span class="font-semibold text-gray-800 dark:text-white">' + roomName + '</span>';
    
    const toggleUrl = "{{ route('owner.rooms.public.toggle', ['room' => ':id']) }}".replace(':id', roomId);
    document.getElementById('publishForm').action = toggleUrl;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    updatePreview();
}

function closePublishModal() {
    document.getElementById('publishModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ===== TOGGLE PUBLIC (via AJAX) =====
function togglePublic(roomId, roomName) {
    if (!confirm('Nonaktifkan publikasi ruangan "' + roomName + '"?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('owner.rooms.public.toggle', ['room' => ':id']) }}".replace(':id', roomId);
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
}

// ===== BULK ACTION =====
function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Pilih minimal satu ruangan terlebih dahulu!');
        return;
    }

    const messages = {
        'publish': 'Publikasikan ' + selected.length + ' ruangan yang dipilih?',
        'unpublish': 'Nonaktifkan publikasi ' + selected.length + ' ruangan yang dipilih?',
        'export': 'Export data ' + selected.length + ' ruangan yang dipilih?'
    };

    if (!confirm(messages[action])) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('owner.rooms.public.bulk') }}";
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'room_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// ===== PREVIEW QR =====
function previewQr(src, name) {
    document.getElementById('qrPreviewImage').src = src;
    document.getElementById('qrPreviewName').textContent = name;
    document.getElementById('qrPreviewModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeQrPreview() {
    document.getElementById('qrPreviewModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ===== COPY LINK =====
function copyLink(token) {
    const url = '{{ url("/room/scan") }}/' + token;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            showCopyToast('✅ Link berhasil disalin!');
        }).catch(() => {
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }
    closeDropdown();
}

function fallbackCopy(text) {
    const input = document.createElement('input');
    input.value = text;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    showCopyToast('✅ Link berhasil disalin!');
}

function showCopyToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-800 dark:bg-gray-900 text-white px-6 py-3 rounded-xl shadow-2xl text-sm font-medium z-50 transition-all duration-300';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// ===== UPDATE PREVIEW =====
document.querySelectorAll('#publishModal input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', updatePreview);
});

function updatePreview() {
    const allowPublic = document.querySelector('#publishModal input[name="allow_public"]')?.checked || false;
    const allowLogin = document.querySelector('#publishModal input[name="allow_login"]')?.checked || false;
    const selectedRoles = Array.from(document.querySelectorAll('#rolesSection input[type="checkbox"]:checked')).map(el => el.value);

    const guest = document.getElementById('previewGuest');
    const owner = document.getElementById('previewOwner');
    const superadmin = document.getElementById('previewSuperadmin');
    const ketua = document.getElementById('previewKetua');
    const anggota = document.getElementById('previewAnggota');

    if (allowPublic) {
        guest.className = 'text-green-600 font-medium';
        guest.textContent = '✅ Dapat melihat';
    } else {
        guest.className = 'text-red-500 font-medium';
        guest.textContent = '❌ Tidak';
    }

    const roleMap = { 'Owner': owner, 'Superadmin': superadmin, 'Ketua': ketua, 'Anggota UKM': anggota };
    const roles = ['Owner', 'Superadmin', 'Ketua', 'Anggota UKM'];

    roles.forEach(role => {
        const el = roleMap[role];
        if (allowLogin && selectedRoles.includes(role)) {
            el.className = 'text-green-600 font-medium';
            el.textContent = '✅ Dapat melihat';
        } else if (allowLogin && selectedRoles.length === 0) {
            el.className = 'text-yellow-600 font-medium';
            el.textContent = '⚠️ Belum ada role';
        } else {
            el.className = 'text-red-500 font-medium';
            el.textContent = '❌ Tidak';
        }
    });

    const rolesSection = document.getElementById('rolesSection');
    if (rolesSection) {
        rolesSection.classList.toggle('hidden', !allowLogin);
    }
}

setTimeout(updatePreview, 200);
</script>
@endsection