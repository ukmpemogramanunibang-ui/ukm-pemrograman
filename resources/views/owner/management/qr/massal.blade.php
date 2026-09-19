@extends('layouts.roles.owner')

@section('title', 'Generate QR Massal – Owner')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Generate QR Massal</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih banyak user sekaligus untuk dibuatkan QR.</p>
        </div>
        <a href="{{ route('owner.management.qr.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form action="{{ route('owner.management.qr.massal.store') }}" method="POST">
            @csrf
            <div class="flex flex-wrap items-center gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filter Role</label>
                    <select name="filter_role" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status QR</label>
                    <select name="qr_status" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                        <option value="">Semua</option>
                        <option value="none">Belum punya QR</option>
                        <option value="active">Aktif</option>
                        <option value="blocked">Diblokir</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari</label>
                    <input type="text" name="search" placeholder="Nama / NIM / email" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                </div>
                <button type="submit" class="mt-5 px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">Filter</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-3"><input type="checkbox" id="selectAll"></th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">NIM</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status QR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox">
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->nim ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs">
                                    {{ $user->level->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php $qr = $user->qrCodes->first(); @endphp
                                @if($qr)
                                    <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> Aktif</span>
                                @else
                                    <span class="text-gray-400"><i class="fas fa-minus-circle"></i> Belum</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada user ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <span id="selectedCount" class="text-sm text-gray-600 dark:text-gray-400">0 user dipilih</span>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-qrcode mr-2"></i> Generate QR untuk yang dipilih
                </button>
            </div>

            {{ $users->withQueryString()->links() }}
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = e.target.checked);
        updateSelectedCount();
    });
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    function updateSelectedCount() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count + ' user dipilih';
    }
</script>
@endpush
@endsection