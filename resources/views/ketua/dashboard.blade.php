@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Dashboard Ketua</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pusat kendali organisasi UNICODE</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <i class="fas fa-clock"></i>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = [
                    ['label' => 'Total Anggota',  'value' => \App\Models\User::where('level_id',5)->count(), 'icon' => 'fa-users',      'color' => 'purple'],
                    ['label' => 'Total Pengurus', 'value' => \App\Models\User::where('level_id',4)->count(), 'icon' => 'fa-user-tie',   'color' => 'indigo'],
                    ['label' => 'Event Aktif',    'value' => class_exists(\App\Models\Event::class) ? \App\Models\Event::count() : 0, 'icon' => 'fa-calendar-check', 'color' => 'pink'],
                    ['label' => 'Divisi',         'value' => class_exists(\App\Models\Division::class) ? \App\Models\Division::count() : 0, 'icon' => 'fa-users-cog', 'color' => 'fuchsia'],
                ];
            @endphp
            @include('partials.dashboard.builder', ['dashboardData' => $dashboardData])

            @foreach($stats as $s)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $s['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $s['value'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-{{ $s['color'] }}-100 dark:bg-{{ $s['color'] }}-900/30 flex items-center justify-center">
                            <i class="fas {{ $s['icon'] }} text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ route('ketua.manajemen-anggota.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                    <i class="fas fa-users text-purple-500 text-xl"></i>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Kelola Anggota</span>
                </a>
                <a href="{{ route('ketua.manajemen-pengurus.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                    <i class="fas fa-user-tie text-purple-500 text-xl"></i>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Kelola Pengurus</span>
                </a>
                <a href="{{ route('ketua.broadcast-pengumuman.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                    <i class="fas fa-bullhorn text-purple-500 text-xl"></i>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Buat Pengumuman</span>
                </a>
                <a href="{{ route('ketua.laporan-analitik') }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                    <i class="fas fa-chart-line text-purple-500 text-xl"></i>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Lihat Laporan</span>
                </a>
            </div>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')