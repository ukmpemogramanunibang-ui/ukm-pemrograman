@extends('layouts.roles.owner')

@section('title', 'Manajemen QR Code – Owner UNICODE')
@section('page-title', 'Manajemen QR Code')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">

        {{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">QR Code Management</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola QR Code anggota dan pengurus.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('owner.management.qr.generate') }}"
           class="px-4 py-2 bg-gradient-to-r from-cyan to-blue-600 text-white font-bold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i> Generate QR
        </a>
        <a href="{{ route('owner.management.qr.recovery') }}"
           class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-undo-alt mr-2"></i> Pulihkan QR
        </a>
        {{-- TOMBOL BARU --}}
        <a href="{{ route('owner.identity-vault.index') }}"
           class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-vault mr-2"></i> Identity Vault
        </a>
        <a href="{{ route('owner.identity-vault.backup') }}?user_id=..."
           onclick="event.preventDefault(); document.getElementById('backup-form').submit();"
           class="px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 text-white font-bold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-database mr-2"></i> Backup Identitas
        </a>
        {{-- Form tersembunyi untuk backup --}}
        {{-- Tombol Backup Identitas --}}
<form action="{{ route('owner.identity-vault.backup') }}" method="POST" class="inline">
    @csrf
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 text-white font-bold rounded-lg hover:shadow-lg transition">
        <i class="fas fa-database mr-2"></i> Backup Identitas
    </button>
</form>
    </div>
</div>
        {{-- 4 Card Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-800/30 flex items-center justify-center text-2xl text-orange-600 dark:text-orange-400">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Belum QR</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $usersWithoutQr }}</p>
                        <p class="text-xs text-gray-400">User belum punya QR</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/30 flex items-center justify-center text-2xl text-green-600 dark:text-green-400">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sudah QR</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $usersWithQr }}</p>
                        <p class="text-xs text-gray-400">User sudah punya QR</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-5 border border-red-200 dark:border-red-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-800/30 flex items-center justify-center text-2xl text-red-600 dark:text-red-400">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">QR Diblokir</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $qrBlocked }}</p>
                        <p class="text-xs text-gray-400">QR status blocked</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-5 border border-yellow-200 dark:border-yellow-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-800/30 flex items-center justify-center text-2xl text-yellow-600 dark:text-yellow-400">
                        <i class="fas fa-user-lock"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Akun Dibekukan</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $usersBlocked }}</p>
                        <p class="text-xs text-gray-400">User tidak aktif</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Role --}}
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <form method="GET" action="{{ route('owner.management.qr.index') }}" class="flex items-center gap-2">
                <label for="role" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Role:</label>
                <select name="role" id="role" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <noscript>
                    <button type="submit" class="px-4 py-2 bg-cyan-600 text-white rounded-lg">Filter</button>
                </noscript>
            </form>
            <a href="{{ route('owner.management.qr.index') }}" class="text-sm text-cyan-600 hover:underline">Reset</a>
        </div>

        {{-- Tabel User QR --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status QR</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($qrUsers as $user)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs">
                                    {{ $user->level->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->qrCodes->where('is_active', 1)->count() > 0)
                                    <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> Aktif</span>
                                @elseif($user->qrCodes->where('is_active', 0)->count() > 0)
                                    <span class="text-red-600 dark:text-red-400"><i class="fas fa-ban"></i> Diblokir</span>
                                @else
                                    <span class="text-gray-400"><i class="fas fa-minus-circle"></i> Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('owner.management.qr.detail', $user->id) }}" class="text-cyan-600 hover:text-cyan-800"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">Tidak ada user dengan QR Code.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $qrUsers->withQueryString()->links() }}
        </div>

        {{-- Informasi --}}
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 text-center">
            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
            <p class="text-gray-700 dark:text-gray-300">
                Halaman ini menampilkan daftar user yang sudah memiliki QR Code beserta statusnya.
                Gunakan filter untuk melihat berdasarkan role.
            </p>
        </div>
    </div>
</div>
@endsection