@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Divisi & Tim</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola struktur divisi dan anggotanya</p>
        </div>

        @if($divisions->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
                <i class="fas fa-users-cog text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">Belum ada divisi yang terdaftar.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($divisions as $div)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ $div->members->count() ?? 0 }} anggota
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $div->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $div->description ?? 'Tidak ada deskripsi' }}</p>
                        <div class="mt-4 flex items-center gap-2">
                            <button class="flex-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-purple-600 text-white hover:bg-purple-700">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </button>
                            <button class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</main>

@include('partials.roles.ketua.footer')