@extends('layouts.roles.owner')

@section('title', 'Hak Akses Kelompok')

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('owner.kelompok.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
            <i class="fas fa-key text-red-500"></i>
            Hak Akses Kelompok
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
            {{-- Form Pilih Kelompok --}}
            <form method="GET" action="{{ route('owner.kelompok.akses') }}" class="mb-6 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Kelompok</label>
                    <select name="group_id" onchange="this.form.submit()" class="mt-1 rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 focus:ring-2 focus:ring-red-500">
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}" {{ $selectedGroupId == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                        Tampilkan
                    </button>
                </div>
            </form>

            @if($selectedGroup)
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Kelompok: <strong>{{ $selectedGroup->name }}</strong>
                        <span class="ml-4 text-xs text-gray-400">(Total anggota: {{ $selectedGroup->members->count() }})</span>
                    </p>
                    <a href="{{ route('owner.kelompok.invite', ['group_id' => $selectedGroup->id]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Tambah Anggota
                    </a>
                </div>

                <form action="{{ route('owner.kelompok.update-permissions', $selectedGroup) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="p-3 text-left">No</th>
                                    <th class="p-3 text-left">Nama</th>
                                    <th class="p-3 text-left">Role</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-center">Lihat</th>
                                    <th class="p-3 text-center">Pengumuman</th>
                                    <th class="p-3 text-center">Pesan</th>
                                    <th class="p-3 text-center">Kelola Anggota</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $u)
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="p-3">{{ $loop->iteration }}</td>
                                        <td class="p-3">{{ $u->name }}</td>
                                        <td class="p-3">{{ $u->level->name ?? '-' }}</td>
                                        <td class="p-3">
                                            @if($u->is_member)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                                    {{ ucfirst($u->position ?? 'Anggota') }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">Belum Anggota</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $index }}][can_view]" value="1"
                                                {{ $u->is_member && $u->can_view ? 'checked' : '' }}
                                                {{ $u->is_member ? '' : 'disabled' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $index }}][can_announcement]" value="1"
                                                {{ $u->is_member && $u->can_announcement ? 'checked' : '' }}
                                                {{ $u->is_member ? '' : 'disabled' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $index }}][can_message]" value="1"
                                                {{ $u->is_member && $u->can_message ? 'checked' : '' }}
                                                {{ $u->is_member ? '' : 'disabled' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="checkbox" name="permissions[{{ $index }}][can_manage_members]" value="1"
                                                {{ $u->is_member && $u->can_manage_members ? 'checked' : '' }}
                                                {{ $u->is_member ? '' : 'disabled' }}>
                                        </td>
                                        <td class="p-3 text-center">
                                            <input type="hidden" name="permissions[{{ $index }}][user_id]" value="{{ $u->id }}">
                                            @if($u->is_member)
                                                @if(!in_array($u->level_id, [1, 2])) {{-- Owner dan Superadmin tidak bisa dikeluarkan --}}
                                                    <button type="button" onclick="confirmRemove({{ $selectedGroup->id }}, {{ $u->id }}, '{{ $u->name }}')"
                                                        class="text-red-500 hover:text-red-700 text-sm">
                                                        <i class="fas fa-user-minus"></i> Keluarkan
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">Tidak bisa dikeluarkan</span>
                                                @endif
                                            @else
                                                <a href="{{ route('owner.kelompok.invite', ['group_id' => $selectedGroup->id, 'user_id' => $u->id]) }}"
                                                   class="text-blue-500 hover:text-blue-700 text-sm">
                                                    <i class="fas fa-user-plus"></i> Undang
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="p-4 text-center text-gray-500">Tidak ada user yang eligible.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('owner.kelompok.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition">
                            Kembali
                        </a>
                    </div>
                </form>

                {{-- Form untuk hapus anggota (hidden) --}}
                <form id="removeForm" action="" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>

                <script>
                    function confirmRemove(groupId, userId, name) {
                        if (confirm('Yakin ingin mengeluarkan ' + name + ' dari kelompok ini?')) {
                            const form = document.getElementById('removeForm');
                            form.action = '/owner/kelompok/' + groupId + '/remove-member/' + userId;
                            form.submit();
                        }
                    }
                </script>

            @else
                <p class="text-gray-500">Silakan pilih kelompok dari dropdown di atas.</p>
            @endif
        @endif
    </div>
</div>
@endsection