@extends('layouts.roles.owner')

@section('title', 'Cek Valid Tagihan Akun - UNICODE')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400" aria-label="Kembali">
            <i class="fas fa-arrow-left text-xl" aria-hidden="true"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Cek Valid Tagihan Akun</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Status pembuatan & pengiriman tagihan — <strong>bukan</strong> status pembayaran keuangan.
            </p>
        </div>
    </div>

    {{-- Info banner untuk memperjelas makna status --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-3 mb-4 text-sm text-blue-800 dark:text-blue-300">
        <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
        Kolom <strong>Status Tagihan</strong> menyatakan apakah invoice sudah dibuat & dikirim.
        Untuk status pelunasan (lunas/belum), gunakan halaman
        <a href="{{ route('owner.financial.invoices.index') }}" class="underline font-medium">Manajemen Tagihan</a>.
    </div>

    {{-- Filter server-side --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="q" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nama / NIM</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
            </div>
            <div>
                <label for="role" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</label>
                <select id="role" name="role" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <option value="">Semua</option>
                    @foreach($roles ?? [] as $r)
                        <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ ucfirst($r->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tagihan_status" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status Tagihan</label>
                <select id="tagihan_status" name="tagihan_status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <option value="">Semua</option>
                    <option value="sudah" @selected(request('tagihan_status') === 'sudah')>Sudah Dibuat</option>
                    <option value="belum" @selected(request('tagihan_status') === 'belum')>Belum Dibuat</option>
                </select>
            </div>
            <div>
                <label for="generation_id" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Generasi</label>
                <select id="generation_id" name="generation_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <option value="">Semua</option>
                    @foreach($generations ?? [] as $g)
                        <option value="{{ $g->id }}" @selected((string) request('generation_id') === (string) $g->id)>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium">Filter</button>
                <a href="{{ route('owner.financial.invoices.check-tagihan') }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white px-3 py-2 rounded-lg text-sm font-medium">Reset</a>
            </div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">NIM</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status Tagihan</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ $user->nim ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->level->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($user->tagihan_status === 'sudah')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <i class="fas fa-check-circle mr-1" aria-hidden="true"></i> Sudah Dibuat
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <i class="fas fa-times-circle mr-1" aria-hidden="true"></i> Belum Dibuat
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button"
                                        onclick="showTagihanDetail({{ $user->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                        aria-label="Lihat detail tagihan {{ $user->name }}">
                                    <i class="fas fa-eye" aria-hidden="true"></i> Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-users text-3xl mb-2 text-gray-300 dark:text-gray-600" aria-hidden="true"></i>
                                <p>Tidak ada user yang cocok dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL --}}
<div id="tagihanModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 flex items-center justify-center"
     role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Preview Notifikasi Tagihan</h3>
            <button type="button" onclick="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400" aria-label="Tutup">
                <i class="fas fa-times text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <div class="p-6" id="modalContent">
            <div class="text-center text-gray-500 py-8">Memuat data…</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const TAGIHAN_MODAL_URL = @json(route('owner.financial.invoices.user-tagihan', ['user' => '__ID__']));

    function showTagihanDetail(userId) {
        const modal = document.getElementById('tagihanModal');
        const content = document.getElementById('modalContent');
        content.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin text-2xl"></i> Memuat data…</div>';
        modal.classList.remove('hidden');

        fetch(TAGIHAN_MODAL_URL.replace('__ID__', userId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(html => { content.innerHTML = html; })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-500 py-8">Gagal memuat data: ' + err.message + '</div>';
        });
    }

    function closeModal() {
        document.getElementById('tagihanModal').classList.add('hidden');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    document.getElementById('tagihanModal').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeModal();
    });
</script>
@endpush
@endsection