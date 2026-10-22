@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Jadwal</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola jadwal kegiatan</p>
            </div>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                <i class="fas fa-plus"></i> Buat Jadwal
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                    <div>{{ $d }}</div>
                @endforeach
            </div>
            <div class="grid grid-cols-7 gap-1">
                @for($i = 1; $i <= 35; $i++)
                    <div class="aspect-square rounded-lg border border-gray-200 dark:border-gray-700 p-1 text-xs text-gray-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 cursor-pointer">
                        {{ $i <= 30 ? $i : '' }}
                    </div>
                @endfor
            </div>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')