@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Modul Belajar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Materi dan panduan untuk anggota</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($modules ?? [] as $mod)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-3">
                        <i class="fas fa-book text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $mod->title ?? 'Modul' }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $mod->description ?? 'Tanpa deskripsi' }}</p>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
                    <i class="fas fa-book-open text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada modul belajar.</p>
                </div>
            @endforelse
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')