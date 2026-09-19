<div class="space-y-4">
    {{-- Informasi User --}}
    <div class="flex flex-wrap justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Nama: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->name }}</span></p>
            <p class="text-sm text-gray-500 dark:text-gray-400">NIM: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->nim ?? '-' }}</span></p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Role: <span class="font-semibold text-gray-800 dark:text-white">{{ $user->level->name ?? '-' }}</span></p>
        </div>
        <div>
            <a href="{{ route('owner.announcements.index') }}" target="_blank" class="text-blue-500 hover:underline text-sm flex items-center gap-1">
                <i class="fas fa-external-link-alt"></i> Lihat Semua Pengumuman
            </a>
        </div>
    </div>

    {{-- Daftar Pengumuman --}}
    @if($announcements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Judul</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Konten</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($announcements as $ann)
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-800 dark:text-white">{{ $ann->title }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ Str::limit($ann->content, 50) }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $ann->created_at->format('d M Y H:i') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            @php
                                $isRead = $ann->pivot->is_read ?? false;
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $isRead ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ $isRead ? '✅ Dibaca' : '⏳ Belum Dibaca' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            Total pengumuman: <span class="font-semibold">{{ $announcements->count() }}</span>
        </div>
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-bullhorn text-4xl block mb-2 text-gray-300 dark:text-gray-600"></i>
            Belum ada pengumuman untuk user ini.
        </div>
    @endif
</div>