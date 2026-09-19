@extends('layouts.roles.owner')

@section('title', 'Preview Target - ' . $generation->name)

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.financial.generations.target', $generation) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition text-xl">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i> Preview Target
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $generation->name }} · {{ $users->count() }} anggota ditemukan</p>
        </div>
    </div>

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-4 mb-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <span class="text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">&times;</button>
        </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Ditemukan</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $users->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Sudah di Generasi Ini</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $users->filter(fn($u) => $u->membership_status == 'active')->count() }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Belum di Generasi Ini</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $users->filter(fn($u) => $u->membership_status == 'none')->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Keanggotaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800 dark:text-white">{{ $user->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $user->nim ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                @if($user->level_id == 3) Ketua
                                @elseif($user->level_id == 4) Pengurus
                                @elseif($user->level_id == 5) Anggota UKM
                                @else {{ $user->level_id }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
    @if($user->membership_status == 'active')
        <div class="flex items-center gap-2">
            <span class="text-yellow-500 text-xs font-medium">✅ Aktif di generasi ini</span>
            <span class="text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">
                {{ $user->current_generation_name ?? $generation->name }}
            </span>
        </div>
    @elseif($user->membership_status == 'other')
        <div class="flex items-center gap-2">
            <span class="text-orange-500 text-xs font-medium">🔄 Bergabung di generasi lain</span>
            <span class="text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full">
                {{ $user->current_generation_name ?? 'Generasi lain' }}
            </span>
        </div>
    @else
        <span class="text-green-500 text-xs font-medium">⬜ Belum tergabung</span>
    @endif
</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada anggota yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TINDAKAN --}}
    @if($users->count() > 0)
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="{{ route('owner.financial.generations.target-apply', $generation) }}" method="POST">
            @csrf
            <input type="hidden" name="mode" value="{{ $mode }}">
            @if($mode == 'simple')
                @foreach(request('role_ids', []) as $roleId)
                    <input type="hidden" name="role_ids[]" value="{{ $roleId }}">
                @endforeach
            @else
                @foreach(request('user_ids', []) as $userId)
                    <input type="hidden" name="user_ids[]" value="{{ $userId }}">
                @endforeach
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tindakan</label>
                    <select name="action" class="form-control" id="actionSelect">
                        <option value="add">Tambahkan ke generasi ini</option>
                        <option value="remove">Keluarkan dari generasi ini</option>
                        <option value="complete">Selesaikan masa keanggotaan</option>
                        <option value="transfer">Pindahkan ke generasi lain</option>
                    </select>
                </div>
                <div id="targetGenerationWrapper" style="{{ old('action') == 'transfer' ? '' : 'display: none;' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Generasi Tujuan (untuk Pindah)</label>
                    <select name="target_generation_id" class="form-control">
                        <option value="">Pilih generasi tujuan</option>
                        @foreach(\App\Models\Financial\Generation::where('id', '!=', $generation->id)->get() as $gen)
                            <option value="{{ $gen->id }}">{{ $gen->name }} ({{ $gen->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan (opsional)</label>
                    <input type="text" name="reason" class="form-control" placeholder="Misal: Perubahan cohort, Selesai masa, dll." value="{{ old('reason') }}">
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-xl font-medium transition flex items-center gap-2">
                    <i class="fas fa-check"></i> Eksekusi
                </button>
                <a href="{{ route('owner.financial.generations.target', $generation) }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white px-6 py-2.5 rounded-xl font-medium transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionSelect = document.getElementById('actionSelect');
    const targetWrapper = document.getElementById('targetGenerationWrapper');

    actionSelect.addEventListener('change', function() {
        if (this.value === 'transfer') {
            targetWrapper.style.display = 'block';
        } else {
            targetWrapper.style.display = 'none';
        }
    });
    if (actionSelect.value === 'transfer') {
        targetWrapper.style.display = 'block';
    }
});
</script>
@endpush

@push('styles')
<style>
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.95rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dark .form-control {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        outline: none;
    }
    .dark .form-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96,165,250,0.25);
    }
</style>
@endpush
@endsection