@extends('layouts.roles.owner')

@section('title', 'Manajemen Generasi - UNICODE')

@section('content')
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                <i class="fas fa-layer-group text-amber-500"></i> Manajemen Generasi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola cohort anggota dan histori keanggotaan</p>
        </div>
        <a href="{{ route('owner.financial.generations.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 transition shadow-md shadow-amber-500/20">
            <i class="fas fa-plus"></i> Tambah Generasi
        </a>
    </div>

    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl p-4 mb-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 dark:hover:text-green-300">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-4 mb-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <span class="text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">&times;</button>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Generasi</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $generations->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $generations->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Anggota</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $generations->sum('active_memberships_count') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
            <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $generations->where('is_active', false)->count() }}</p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Generasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Anggota</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($generations as $gen)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $gen->code }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $gen->name }}</div>
                            @if($gen->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($gen->description, 40) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                            {{ $gen->period_label }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ $gen->active_memberships_count ?? 0 }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            {!! $gen->status_label !!}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('owner.financial.generations.show', $gen) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('owner.financial.generations.target', $gen) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300" title="Pilih Target">
                                    <i class="fas fa-bullseye"></i>
                                </a>
                                <a href="{{ route('owner.financial.generations.edit', $gen) }}" class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 py-1">
                                        <a href="{{ route('owner.financial.generations.show', $gen) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Lihat Anggota</a>
                                        <a href="{{ route('owner.financial.generations.target', $gen) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Pilih Target</a>
                                        <a href="{{ route('owner.financial.generations.edit', $gen) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit Generasi</a>
                                        <hr class="border-gray-200 dark:border-gray-700">
                                        <form action="{{ route('owner.financial.generations.destroy', $gen) }}" method="POST" class="block" onsubmit="return confirm('Yakin ingin menghapus generasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-700">
                                                <i class="fas fa-trash mr-2"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-layer-group text-5xl block mb-4 text-gray-300 dark:text-gray-600"></i>
                            <p class="text-lg font-medium">Belum ada generasi</p>
                            <p class="text-sm">Klik "Tambah Generasi" untuk membuat generasi pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
@endpush
@endsection