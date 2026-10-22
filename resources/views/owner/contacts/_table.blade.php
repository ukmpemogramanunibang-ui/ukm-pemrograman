<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-4 py-3">#</th>
                <th class="px-4 py-3">Avatar</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">NIM</th>
                <th class="px-4 py-3">Telepon</th>
                @if(!isset($showRole) || $showRole !== false)
                <th class="px-4 py-3">Role</th>
                @endif
                <th class="px-4 py-3">Status Akun</th>
                <th class="px-4 py-3">Status Online</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="window.location='{{ route('owner.contacts.user', $user->user_uuid) }}'">
                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        @if($user->avatar_url)
                            <img src="{{ asset('storage/' . $user->avatar_url) }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->nim ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $user->phone ?? '-' }}</td>
                    @if(!isset($showRole) || $showRole !== false)
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200 rounded-full text-xs">{{ $user->level->name ?? 'N/A' }}</span>
                    </td>
                    @endif
                    <td class="px-4 py-3">
                        @if($user->is_active)
                            <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle"></i> Aktif</span>
                        @else
                            <span class="text-red-600 dark:text-red-400"><i class="fas fa-times-circle"></i> Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($user->is_online)
                            <span class="text-green-500"><i class="fas fa-circle"></i> Online</span>
                        @else
                            <span class="text-gray-400"><i class="fas fa-circle"></i> Offline</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('owner.chats.private', $user->user_uuid) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" onclick="event.stopPropagation()">
                            <i class="fas fa-comment"></i> Chat
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-400">Tidak ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
</div>