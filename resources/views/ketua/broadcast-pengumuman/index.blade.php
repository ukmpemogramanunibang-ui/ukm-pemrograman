@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Broadcast Pengumuman</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pengumuman organisasi</p>
            </div>
            <a href="{{ route('ketua.broadcast-pengumuman.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                <i class="fas fa-plus"></i> Buat Pengumuman
            </a>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($announcements ?? [] as $a)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-6 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">{{ $a->title }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ optional($a->created_at)->format('d M Y') }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Published</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('ketua.broadcast-pengumuman.show', $a->id) }}" class="text-purple-600 dark:text-purple-400 hover:underline text-sm mr-3"><i class="fas fa-eye"></i></a>
                                <form action="{{ route('ketua.broadcast-pengumuman.destroy', $a->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-bullhorn text-3xl mb-2 block opacity-40"></i>
                                Belum ada pengumuman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')