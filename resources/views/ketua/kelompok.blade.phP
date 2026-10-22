@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Kelompok</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manajemen kelompok anggota</p>
            </div>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                <i class="fas fa-plus"></i> Buat Kelompok
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($groups ?? [] as $g)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            {{ $g->members_count ?? 0 }} anggota
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $g->name ?? 'Kelompok' }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $g->description ?? '-' }}</p>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
                    <i class="fas fa-layer-group text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada kelompok.</p>
                </div>
            @endforelse
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')