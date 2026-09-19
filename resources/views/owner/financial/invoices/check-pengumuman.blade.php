@extends('layouts.roles.owner')

@section('title', 'Cek Valid Pengumuman Akun - UNICODE')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Cek Valid Pengumuman Akun</h1>
        <span class="text-sm text-gray-500 dark:text-gray-400 ml-auto">Double-klik pada baris untuk preview detail</span>
    </div>

    {{-- FILTER --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nama</label>
                <input type="text" id="filterNama" placeholder="Cari nama..." class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white w-40">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">NIM</label>
                <input type="text" id="filterNim" placeholder="Cari NIM..." class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white w-36">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Role</label>
                <select id="filterRole" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white w-32">
                    <option value="">Semua</option>
                    @foreach($users->pluck('level.name')->unique() as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select id="filterStatus" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white w-32">
                    <option value="">Semua</option>
                    <option value="sudah">Sudah Masuk</option>
                    <option value="belum">Belum Masuk</option>
                </select>
            </div>
            <button onclick="applyFilters()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            <button onclick="resetFilters()" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg text-sm">Reset</button>
        </div>
    </div>

    {{-- TABEL USER --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="userTable">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Poin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Pengumuman</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition cursor-pointer" 
                        data-user="{{ $user->id }}"
                        data-nama="{{ $user->name }}"
                        data-nim="{{ $user->nim ?? '' }}"
                        data-role="{{ $user->level->name ?? '' }}"
                        data-status="{{ $user->pengumuman_status }}"
                        ondblclick="showPengumumanDetail({{ $user->id }})">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white">{{ $user->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $user->nim ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $user->level->name ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $user->poin ?? 0 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->pengumuman_status == 'sudah' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $user->pengumuman_status == 'sudah' ? '✅ Sudah Masuk' : '❌ Belum Masuk' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-users text-2xl block mb-2 text-gray-300 dark:text-gray-600"></i>
                            Tidak ada user ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PENGUMUMAN USER --}}
<div id="pengumumanModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Preview Notifikasi Pengumuman</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            <div class="text-center text-gray-500 py-8">Memuat data...</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function applyFilters() {
        const nama = document.getElementById('filterNama').value.toLowerCase().trim();
        const nim = document.getElementById('filterNim').value.toLowerCase().trim();
        const role = document.getElementById('filterRole').value.toLowerCase();
        const status = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('#userTable tbody tr');
        rows.forEach(row => {
            const rowNama = (row.dataset.nama || '').toLowerCase();
            const rowNim = (row.dataset.nim || '').toLowerCase();
            const rowRole = (row.dataset.role || '').toLowerCase();
            const rowStatus = row.dataset.status || '';

            let show = true;
            if (nama && !rowNama.includes(nama)) show = false;
            if (nim && !rowNim.includes(nim)) show = false;
            if (role && rowRole !== role) show = false;
            if (status && rowStatus !== status) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    function resetFilters() {
        document.getElementById('filterNama').value = '';
        document.getElementById('filterNim').value = '';
        document.getElementById('filterRole').value = '';
        document.getElementById('filterStatus').value = '';
        applyFilters();
    }

    function showPengumumanDetail(userId) {
        const modal = document.getElementById('pengumumanModal');
        const content = document.getElementById('modalContent');
        content.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin text-2xl"></i> Memuat data...</div>';
        modal.classList.remove('hidden');

        fetch(`/owner/financial/invoices/user-pengumuman/${userId}`)
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat data');
                return res.text();
            })
            .then(html => {
                content.innerHTML = html;
            })
            .catch(err => {
                content.innerHTML = `<div class="text-center text-red-500 py-8">Gagal memuat data: ${err.message}</div>`;
            });
    }

    function closeModal() {
        document.getElementById('pengumumanModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    document.getElementById('pengumumanModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
@endsection