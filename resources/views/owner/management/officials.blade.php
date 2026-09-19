@extends('layouts.roles.owner')

@section('title', 'Manajemen Pengurus – Owner UNICODE')
@section('page-title', 'Manajemen Pengurus')

@push('styles')
<style>
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-container table {
        min-width: 800px;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }
    .table-container th {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .table-container th {
        color: #9ca3af;
        border-bottom-color: #374151;
    }
    .table-container td {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: background 0.2s;
    }
    .dark .table-container td {
        background: #1f2937;
        color: #e5e7eb;
    }
    .table-container tr:hover td {
        background: #f9fafb;
    }
    .dark .table-container tr:hover td {
        background: #374151;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-status.active {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    .badge-status.inactive {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .badge-status .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-status.active .dot { background: #22c55e; }
    .badge-status.inactive .dot { background: #ef4444; }

    .badge-role {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
        background: rgba(99, 102, 241, 0.12);
        color: #6366f1;
    }
    .dark .badge-role {
        background: rgba(99, 102, 241, 0.2);
        color: #818cf8;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .btn-action.view {
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.08);
    }
    .btn-action.view:hover {
        background: rgba(59, 130, 246, 0.16);
    }
    .btn-action.edit {
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.08);
    }
    .btn-action.edit:hover {
        background: rgba(245, 158, 11, 0.16);
    }
    .btn-action.delete {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.08);
    }
    .btn-action.delete:hover {
        background: rgba(239, 68, 68, 0.16);
    }
    .dark .btn-action.view { color: #60a5fa; background: rgba(96, 165, 250, 0.12); }
    .dark .btn-action.edit { color: #fbbf24; background: rgba(251, 191, 36, 0.12); }
    .dark .btn-action.delete { color: #f87171; background: rgba(248, 113, 113, 0.12); }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    .dark .empty-state i {
        color: #4b5563;
    }

    .search-box {
        transition: all 0.3s;
    }
    .search-box:focus-within {
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
        border-color: #06b6d4;
    }

    .stat-mini {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 0.8rem;
        border-radius: 9999px;
        background: rgba(6, 182, 212, 0.08);
        color: #06b6d4;
        font-size: 0.7rem;
        font-weight: 500;
    }
    .dark .stat-mini {
        background: rgba(6, 182, 212, 0.15);
    }
</style>
@endpush

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 md:p-6 border border-gray-200/60 dark:border-gray-700/60">
    {{-- Header & Search --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-tie text-indigo-500"></i>
                Daftar Pengurus
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-3">
                Total {{ $officials->total() }} pengurus terdaftar
                <span class="stat-mini">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    {{ $officials->where('is_active', true)->count() }} aktif
                </span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search Form --}}
            <form action="{{ route('owner.management.officials') }}" method="GET" class="flex items-center gap-2">
                <div class="relative search-box rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus-within:border-indigo-500 dark:focus-within:border-indigo-400 transition-all duration-300">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIM..." class="pl-9 pr-4 py-2 bg-transparent w-48 sm:w-64 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 outline-none">
                </div>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-sm font-medium rounded-lg hover:shadow-md transition-all">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('owner.management.officials') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
            <a href="{{ route('owner.management.officials.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-medium rounded-lg hover:shadow-md transition-all flex items-center gap-1.5">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    {{-- Tabel --}}
    @if($officials->count() > 0)
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role / Level</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($officials as $official)
                <tr>
                    <td class="font-mono text-sm">{{ $official->nim ?? '-' }}</td>
                    <td class="font-medium">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($official->name) }}&background=6366F1&color=fff&size=32" alt="Avatar" class="h-7 w-7 rounded-full border border-gray-200 dark:border-gray-600 flex-shrink-0">
                            <span>{{ $official->name }}</span>
                        </div>
                    </td>
                    <td>{{ $official->email }}</td>
                    <td>
                        <span class="badge-role">
                            <i class="fas fa-{{ $official->level_id == 4 ? 'user-tie' : 'user' }} mr-1 text-[10px]"></i>
                            {{ optional($official->level)->name ?? 'Pengurus' }}
                        </span>
                        @if($official->hasRole('ketua'))
                            <span class="badge-role ml-1" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                                <i class="fas fa-crown mr-1 text-[10px]"></i> Ketua
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status {{ $official->is_active ? 'active' : 'inactive' }}">
                            <span class="dot"></span>
                            {{ $official->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('owner.management.officials.show', $official->id) }}" class="btn-action view" title="Lihat Detail">
                                <i class="fas fa-eye"></i> <span class="hidden sm:inline">Detail</span>
                            </a>
                            <a href="{{ route('owner.management.officials.edit', $official->id) }}" class="btn-action edit" title="Edit">
                                <i class="fas fa-pen"></i> <span class="hidden sm:inline">Edit</span>
                            </a>
                           <button onclick="confirmDelete('{{ route('owner.management.officials.destroy', $official->id) }}', '{{ addslashes($official->name) }}')" 
        class="btn-action delete" title="Hapus">
    <i class="fas fa-trash"></i> <span class="hidden sm:inline">Hapus</span>
</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan {{ $officials->firstItem() ?? 0 }} - {{ $officials->lastItem() ?? 0 }} dari {{ $officials->total() }} pengurus
        </div>
        <div>
            {{ $officials->appends(request()->query())->links() }}
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada pengurus terdaftar.</p>
        <a href="{{ route('owner.management.officials.create') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition">
            <i class="fas fa-plus mr-1"></i> Tambah Pengurus
        </a>
    </div>
    @endif
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="delete-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 w-full max-w-md p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 text-red-600 dark:text-red-400 text-3xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Apakah Anda yakin ingin menghapus pengurus <strong id="delete-name"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex w-full gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 py-2.5 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium">Batal</button>
                <form id="delete-form" method="POST" action="" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md shadow-red-500/20 transition">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(deleteUrl, name) {
        const modal = document.getElementById('delete-modal');
        document.getElementById('delete-name').textContent = name;
        document.getElementById('delete-form').action = deleteUrl;
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
            modal.querySelector('.transform').classList.add('scale-100');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-100');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('delete-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush