@extends('layouts.roles.owner')

@section('title', 'Daftar Ketua & Pengurus – Owner UNICODE')
@section('page-title', 'Daftar Ketua & Pengurus')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Daftar Ketua & Pengurus</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Menampilkan akun dengan role <strong>Ketua</strong> dan <strong>Pengurus</strong> saja. Owner & Superadmin tidak ditampilkan.
                </p>
            </div>
            <a href="{{ route('owner.officials.create') }}"
               class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 transition">
                <i class="fas fa-plus mr-2"></i> Tambah Pengurus
            </a>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-5 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-800/30 flex items-center justify-center text-2xl text-purple-600 dark:text-purple-400">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ketua</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $officials->where('level_id', 3)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/30 flex items-center justify-center text-2xl text-green-600 dark:text-green-400">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pengurus</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $officials->where('level_id', 4)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800/30 flex items-center justify-center text-2xl text-blue-600 dark:text-blue-400">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Universitas</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $officials->pluck('university.name')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5 border border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-800/30 flex items-center justify-center text-2xl text-indigo-600 dark:text-indigo-400">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Jurusan</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $officials->pluck('major.name')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">No. Telepon</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Universitas</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Poin</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($officials as $official)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($official->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">{{ $official->name }}</p>
                                        @if($official->is_active)
                                            <span class="text-xs text-green-500"><i class="fas fa-circle"></i> Aktif</span>
                                        @else
                                            <span class="text-xs text-red-500"><i class="fas fa-circle"></i> Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $official->nim ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $official->phone ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $official->email ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [3 => 'bg-purple-100 text-purple-800', 4 => 'bg-green-100 text-green-800'];
                                    $roleNames = [3 => 'Ketua', 4 => 'Pengurus'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $roleColors[$official->level_id] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $roleNames[$official->level_id] ?? $official->level->name ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $jabatanUtama = $official->jabatans->where('pivot.is_primary', true)->first();
                                    if (!$jabatanUtama) {
                                        $jabatanUtama = $official->jabatans->first();
                                    }
                                @endphp
                                @if($jabatanUtama)
                                    <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200 text-xs">
                                        <i class="fas fa-briefcase mr-1"></i> {{ $jabatanUtama->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $official->university->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $official->major->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $official->total_points ?? 0 }}</span>
                                <span class="text-gray-400 text-xs">poin</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('owner.officials.show', $official->id) }}"
                                       class="text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('owner.officials.edit', $official->id) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('owner.officials.destroy', $official->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin hapus pengurus ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-users-slash text-4xl mb-3"></i>
                                <p>Tidak ada ketua atau pengurus yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $officials->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection