@extends('layouts.roles.owner')

@section('title', 'Semua Akun – Owner UNICODE')
@section('page-title', 'Semua Akun')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-users text-blue-600 mr-2"></i> Semua Akun
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Daftar semua pengguna terdaftar di sistem.</p>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Akun</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/20 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">Offline</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['offline'] }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">Diblokir</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['blocked'] }}</p>
            </div>
        </div>

        {{-- Filter & Search --}}
        <form method="GET" action="{{ request()->route()->getName() === 'owner.contacts.index' ? route('owner.contacts.index') : '#' }}" class="flex flex-wrap items-center gap-4 mb-4">
            <input type="text" name="search" placeholder="Cari nama, NIM, email..." value="{{ request('search') }}" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan flex-1 min-w-[200px]">
            <select name="role" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 focus:ring-2 focus:ring-cyan">
                <option value="">Status Akun</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>Dibekukan</option>
                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Diblokir</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition">Terapkan</button>
            <a href="{{ route('owner.contacts.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">Reset</a>
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Avatar</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="window.location='{{ route('owner.user', $user->user_uuid) }}'">
                        <td class="px-4 py-3">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="px-4 py-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->nim ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $user->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200 rounded-full text-xs">
                                {{ $user->level->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_active)
                                <span class="text-green-600 dark:text-green-400"><i class="fas fa-circle text-[8px] mr-1"></i> Aktif</span>
                            @else
                                <span class="text-red-600 dark:text-red-400"><i class="fas fa-circle text-[8px] mr-1"></i> Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('owner.chats.private', $user->user_uuid) }}" class="text-cyan-600 hover:text-cyan-800" title="Chat">
                                <i class="fas fa-comment"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i> Tidak ada data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection