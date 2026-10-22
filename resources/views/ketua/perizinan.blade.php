@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Perizinan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola izin anggota dan pengurus</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jenis Izin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($permits ?? [] as $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-6 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $p->user->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $p->type ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ optional($p->created_at)->format('d M Y') }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button class="text-green-600 hover:underline text-sm mr-2"><i class="fas fa-check"></i></button>
                                <button class="text-red-600 hover:underline text-sm"><i class="fas fa-times"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-file-signature text-3xl mb-2 block opacity-40"></i>
                                Belum ada pengajuan izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')