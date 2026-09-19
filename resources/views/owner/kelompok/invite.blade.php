@extends('layouts.roles.owner')

@section('title', 'Invite Anggota Kelompok')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-user-plus text-green-500"></i>
            Invite Anggota Kelompok
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">

        @if($groups->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-layer-group text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Belum ada kelompok.</p>
                <a href="{{ route('owner.kelompok.custom') }}" class="text-purple-600 hover:underline mt-2 inline-block">
                    Buat kelompok sekarang
                </a>
            </div>
        @else
            <form method="GET" action="{{ route('owner.kelompok.invite') }}" class="mb-6 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kelompok</label>
                    <select name="group_id" onchange="this.form.submit()" class="mt-1 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-green-500">
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        Tampilkan
                    </button>
                </div>
            </form>

            @if($selectedGroup)
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Kelompok: <strong>{{ $selectedGroup->name }}</strong>
                    <span class="ml-4 text-xs text-gray-400">(Total anggota: {{ $selectedGroup->members->count() }})</span>
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Nama</th>
                                <th class="p-3 text-left">Role</th>
                                <th class="p-3 text-left">Level</th>
                                <th class="p-3 text-left">Poin</th>
                                <th class="p-3 text-left">Kelompok</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="p-3">{{ $loop->iteration }}</td>
                                    <td class="p-3">{{ $user->name }}</td>
                                    <td class="p-3">{{ $user->level->name ?? '-' }}</td>
                                    <td class="p-3">{{ $user->level_id }}</td>
                                    <td class="p-3">{{ $user->total_points ?? 0 }}</td>
                                    <td class="p-3">
                                        @if($user->is_member)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                                {{ ucfirst($user->member_position ?? 'Anggota') }}
                                            </span>
                                            <span class="text-xs text-gray-400 block">{{ $selectedGroup->name }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($user->is_member)
                                            <form action="{{ route('owner.kelompok.remove-member', [$selectedGroup, $user->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-lg transition" onclick="return confirm('Yakin ingin mengeluarkan {{ $user->name }} dari kelompok ini?')">
                                                    Keluarkan
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('owner.kelompok.add-member', $selectedGroup) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <input type="hidden" name="position" value="anggota">
                                                <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg transition">
                                                    Invite
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="p-4 text-center text-gray-500">Tidak ada user yang dapat diundang.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Silakan pilih kelompok dari dropdown di atas.</p>
            @endif
        @endif
    </div>
</div>

{{-- ⚠️ SCRIPT AUTO-REFRESH DIHAPUS — sekarang stabil --}}
@endsection