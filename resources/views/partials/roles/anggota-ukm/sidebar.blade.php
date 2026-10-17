{{-- resources/views/partials/roles/anggota-ukm/sidebar.blade.php --}}
@php
    $user = auth()->user();

    $activeGroups    = method_exists($user, 'activeGroups')    ? $user->activeGroups()->get()    : collect();
    $activeDivisions = method_exists($user, 'activeDivisions') ? $user->activeDivisions()->get() : collect();

    $isGroupActive = function (array $routes) {
        foreach ($routes as $r) {
            if (\Illuminate\Support\Facades\Route::has($r)
                && (request()->routeIs($r) || request()->routeIs($r . '.*'))) {
                return true;
            }
        }
        return false;
    };

    $safeRoute = fn ($name, $params = []) => \Illuminate\Support\Facades\Route::has($name)
        ? route($name, $params) : '#';

    $menuItems = [
        ['type'=>'link','route'=>'anggota.dashboard','icon'=>'fa-home','label'=>'Dashboard'],
        ['type'=>'link','route'=>'anggota.chatting','icon'=>'fa-comments','label'=>'Chatting'],
        ['type'=>'group','label'=>'Presensi','icon'=>'fa-clipboard-check','route'=>'anggota.presensi','children'=>[
            ['route'=>'anggota.presensi','label'=>'Presensi Saya'],
            ['route'=>'anggota.presensi.riwayat','label'=>'Riwayat Presensi'],
            ['route'=>'anggota.presensi.izin','label'=>'Izin'],
        ]],
        ['type'=>'group','label'=>'Pengumuman & Notifikasi','icon'=>'fa-bell','route'=>'anggota.pengumuman','children'=>[
            ['route'=>'anggota.notifikasi','label'=>'Notification Center'],
            ['route'=>'anggota.pengumuman','label'=>'Pengumuman'],
            ['route'=>'anggota.pengumuman.penting','label'=>'Pengumuman Penting'],
        ]],
        // ▼ index 4 → titik sisip Kelompok & Divisi
        ['type'=>'group','label'=>'Misi','icon'=>'fa-flag','route'=>'anggota.misi','children'=>[
            ['route'=>'anggota.misi','label'=>'Misi Saya'],
            ['route'=>'anggota.misi.aktif','label'=>'Misi Aktif'],
            ['route'=>'anggota.misi.selesai','label'=>'Misi Selesai'],
        ]],
        ['type'=>'group','label'=>'LeaderPoint','icon'=>'fa-trophy','route'=>'anggota.leaderpoint','children'=>[
            ['route'=>'anggota.poin','label'=>'Poin Saya'],
            ['route'=>'anggota.leaderpoint.leaderboard','label'=>'Leaderboard'],
            ['route'=>'anggota.leaderpoint.prestasi','label'=>'Prestasi'],
            ['route'=>'anggota.leaderpoint.riwayat','label'=>'Riwayat Poin'],
        ]],
        ['type'=>'group','label'=>'Game','icon'=>'fa-gamepad','route'=>'anggota.game','children'=>[
            ['route'=>'anggota.game','label'=>'Game'],
            ['route'=>'anggota.game.challenge','label'=>'Challenge'],
            ['route'=>'anggota.game.kompetisi','label'=>'Kompetisi'],
            ['route'=>'anggota.game.achievement','label'=>'Achievement'],
            ['route'=>'anggota.game.riwayat','label'=>'Riwayat Game'],
        ]],
        ['type'=>'group','label'=>'Ruangan','icon'=>'fa-door-open','route'=>'anggota.ruangan','children'=>[
            ['route'=>'anggota.ruangan','label'=>'Lihat Ruangan'],
            ['route'=>'anggota.ruangan.booking','label'=>'Booking'],
            ['route'=>'anggota.ruangan.jadwal','label'=>'Jadwal Ruangan'],
        ]],
        ['type'=>'group','label'=>'Jadwal','icon'=>'fa-calendar-alt','route'=>'anggota.jadwal','children'=>[
            ['route'=>'anggota.jadwal','label'=>'Jadwal Saya'],
            ['route'=>'anggota.jadwal.organisasi','label'=>'Jadwal Organisasi'],
            ['route'=>'anggota.jadwal.kalender','label'=>'Kalender'],
        ]],
        ['type'=>'group','label'=>'Uang Kas','icon'=>'fa-wallet','route'=>'anggota.uangkas','children'=>[
            ['route'=>'anggota.uangkas.tagihan','label'=>'Tagihan/Iuran'],
            ['route'=>'anggota.uangkas.status','label'=>'Status Pembayaran'],
            ['route'=>'anggota.uangkas.riwayat','label'=>'Riwayat Pembayaran'],
        ]],
        ['type'=>'group','label'=>'Event','icon'=>'fa-calendar-check','route'=>'anggota.event','children'=>[
            ['route'=>'anggota.event','label'=>'Semua Event'],
            ['route'=>'anggota.event.saya','label'=>'Event Saya'],
            ['route'=>'anggota.event.pendaftaran','label'=>'Pendaftaran'],
            ['route'=>'anggota.event.kehadiran','label'=>'Kehadiran'],
            ['route'=>'anggota.event.sertifikat','label'=>'Sertifikat Event'],
        ]],
        ['type'=>'group','label'=>'Tugas & Materi','icon'=>'fa-book','route'=>'anggota.tugas','children'=>[
            ['route'=>'anggota.tugas','label'=>'Tugas Saya'],
            ['route'=>'anggota.materi','label'=>'Materi'],
            ['route'=>'anggota.tugas.pengumpulan','label'=>'Pengumpulan'],
            ['route'=>'anggota.tugas.riwayat','label'=>'Riwayat'],
        ]],
        ['type'=>'group','label'=>'Prestasi','icon'=>'fa-award','route'=>'anggota.prestasi','children'=>[
            ['route'=>'anggota.prestasi','label'=>'Prestasi Saya'],
            ['route'=>'anggota.sertifikat','label'=>'Sertifikat'],
            ['route'=>'anggota.portofolio','label'=>'Portofolio'],
        ]],
        ['type'=>'group','label'=>'Pengaturan','icon'=>'fa-cog','route'=>'anggota.pengaturan','children'=>[
            ['route'=>'anggota.profil','label'=>'Profil'],
            ['route'=>'anggota.pengaturan.data-diri','label'=>'Data Diri'],
            ['route'=>'anggota.portofolio','label'=>'Portofolio'],
            ['route'=>'anggota.sertifikat','label'=>'Sertifikat'],
            ['route'=>'anggota.pengaturan.riwayat-jabatan','label'=>'Riwayat Jabatan'],
            ['route'=>'anggota.sanksi','label'=>'Sanksi'],
            ['route'=>'anggota.pengaturan.keamanan','label'=>'Keamanan'],
            ['route'=>'anggota.pengaturan.password','label'=>'Ganti Password'],
        ]],
    ];

    $groupChildren = [
        ['route'=>'anggota.kelompok.dashboard','label'=>'Dashboard'],
        ['route'=>'anggota.kelompok.anggota','label'=>'Anggota'],
        ['route'=>'anggota.kelompok.pengumuman','label'=>'Pengumuman'],
        ['route'=>'anggota.kelompok.tugas','label'=>'Tugas'],
        ['route'=>'anggota.kelompok.materi','label'=>'Materi'],
        ['route'=>'anggota.kelompok.chat','label'=>'Chat'],
        ['route'=>'anggota.kelompok.jadwal','label'=>'Jadwal'],
    ];
    $divisionChildren = [
        ['route'=>'anggota.divisi.dashboard','label'=>'Dashboard'],
        ['route'=>'anggota.divisi.anggota','label'=>'Anggota'],
        ['route'=>'anggota.divisi.pengumuman','label'=>'Pengumuman'],
        ['route'=>'anggota.divisi.tugas','label'=>'Tugas'],
        ['route'=>'anggota.divisi.misi','label'=>'Misi'],
        ['route'=>'anggota.divisi.chat','label'=>'Chat'],
        ['route'=>'anggota.divisi.jadwal','label'=>'Jadwal'],
    ];
@endphp

{{-- ================= SIDEBAR ================= --}}
<aside
    x-data
    role="navigation"
    aria-label="Sidebar navigasi anggota"
    class="fixed inset-y-0 left-0 z-40 flex flex-col
           w-80 max-w-[85vw]
           bg-white dark:bg-gray-900
           border-r border-gray-200 dark:border-gray-800
           shadow-xl shadow-black/5
           transition-transform duration-300 ease-in-out
           will-change-transform"
    :class="{
        'translate-x-0':          $store.sidebar.mobileOpen,
        '-translate-x-full':     !$store.sidebar.mobileOpen,
        'md:translate-x-0':       $store.sidebar.desktopOpen,
        'md:-translate-x-full':  !$store.sidebar.desktopOpen
    }">

    {{-- ============ HEADER SIDEBAR ============ --}}
    <div class="h-16 flex items-center justify-between px-4
                border-b border-gray-200 dark:border-gray-800 flex-shrink-0">

        <a href="{{ $safeRoute('anggota.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-indigo-600
                         text-white font-bold flex items-center justify-center flex-shrink-0 shadow-lg">
                U
            </span>
            <span class="font-bold text-lg text-gray-800 dark:text-white whitespace-nowrap">
                UNICODE
            </span>
        </a>

        {{-- Tombol X: tutup total (desktop & mobile) --}}
        <button type="button"
                @click="$store.sidebar.close()"
                title="Tutup sidebar (Ctrl+B)"
                aria-label="Tutup sidebar"
                class="w-9 h-9 rounded-xl flex items-center justify-center
                       text-gray-500 dark:text-gray-400
                       hover:bg-red-50 dark:hover:bg-red-900/20
                       hover:text-red-500 dark:hover:text-red-400
                       active:scale-90
                       transition-all duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- ============ MENU SCROLL AREA ============ --}}
    <nav class="flex-1 overflow-y-auto p-3 space-y-1
                scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">

        @foreach($menuItems as $index => $item)

            {{-- ▼▼ Sisipkan Kelompok & Divisi setelah Pengumuman ▼▼ --}}
            @if($index === 4)

                {{-- ============ KELOMPOK (conditional) ============ --}}
                @if($activeGroups->isNotEmpty())
                    <div class="pt-3 mt-2 border-t border-gray-200 dark:border-gray-800">
                        <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                            Organisasi Saya
                        </div>
                    </div>

                    @foreach($activeGroups as $group)
                        @php
                            $groupRoutes = collect($groupChildren)->pluck('route')->all();
                            $groupActive = $isGroupActive($groupRoutes);
                        @endphp
                        <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                            <a href="{{ $safeRoute('anggota.kelompok.dashboard', $group) }}"
                               @click.prevent="
                                   if (open) {
                                       window.location.href = '{{ $safeRoute('anggota.kelompok.dashboard', $group) }}';
                                   } else {
                                       open = true;
                                   }
                               "
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                                      transition-all duration-200
                                      {{ $groupActive
                                            ? 'bg-gradient-to-r from-cyan-500 to-indigo-600 text-white shadow-lg shadow-cyan-500/20'
                                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-cyan-600 dark:hover:text-cyan-400' }}">
                                <i class="fas fa-users w-5 text-center flex-shrink-0"></i>
                                <span class="whitespace-nowrap flex-1 truncate">{{ $group->nama ?? 'Kelompok' }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                   :class="open ? 'rotate-180' : ''"></i>
                            </a>
                            <div x-show="open" x-transition.duration.200ms
                                 class="mt-1 ml-4 pl-3 border-l border-gray-200 dark:border-gray-700 space-y-0.5">
                                @foreach($groupChildren as $child)
                                    @php
                                        $childActive = \Illuminate\Support\Facades\Route::has($child['route'])
                                            && (request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'));
                                    @endphp
                                    <a href="{{ $safeRoute($child['route'], $group) }}"
                                       class="block px-3 py-2 rounded-lg text-[13px] transition
                                              {{ $childActive
                                                    ? 'text-cyan-600 dark:text-cyan-400 font-semibold bg-cyan-50 dark:bg-cyan-900/20'
                                                    : 'text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- ============ DIVISI (conditional) ============ --}}
                @if($activeDivisions->isNotEmpty())
                    <div class="pt-3 mt-2 border-t border-gray-200 dark:border-gray-800">
                        <div class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                            Divisi Saya
                        </div>
                    </div>

                    @foreach($activeDivisions as $division)
                        @php
                            $divisionRoutes = collect($divisionChildren)->pluck('route')->all();
                            $divisionActive = $isGroupActive($divisionRoutes);
                        @endphp
                        <div x-data="{ open: {{ $divisionActive ? 'true' : 'false' }} }">
                            <a href="{{ $safeRoute('anggota.divisi.dashboard', $division) }}"
                               @click.prevent="
                                   if (open) {
                                       window.location.href = '{{ $safeRoute('anggota.divisi.dashboard', $division) }}';
                                   } else {
                                       open = true;
                                   }
                               "
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                                      transition-all duration-200
                                      {{ $divisionActive
                                            ? 'bg-gradient-to-r from-cyan-500 to-indigo-600 text-white shadow-lg shadow-cyan-500/20'
                                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-cyan-600 dark:hover:text-cyan-400' }}">
                                <i class="fas fa-sitemap w-5 text-center flex-shrink-0"></i>
                                <span class="whitespace-nowrap flex-1 truncate">{{ $division->nama ?? 'Divisi' }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                   :class="open ? 'rotate-180' : ''"></i>
                            </a>
                            <div x-show="open" x-transition.duration.200ms
                                 class="mt-1 ml-4 pl-3 border-l border-gray-200 dark:border-gray-700 space-y-0.5">
                                @foreach($divisionChildren as $child)
                                    @php
                                        $childActive = \Illuminate\Support\Facades\Route::has($child['route'])
                                            && (request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'));
                                    @endphp
                                    <a href="{{ $safeRoute($child['route'], $division) }}"
                                       class="block px-3 py-2 rounded-lg text-[13px] transition
                                              {{ $childActive
                                                    ? 'text-cyan-600 dark:text-cyan-400 font-semibold bg-cyan-50 dark:bg-cyan-900/20'
                                                    : 'text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                        {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif

            {{-- ============ ITEM MENU UTAMA ============ --}}
            @if($item['type'] === 'link')
                @php
                    $isActive = \Illuminate\Support\Facades\Route::has($item['route'])
                        && (request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'));
                @endphp
                <a href="{{ $safeRoute($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          transition-all duration-200
                          {{ $isActive
                                ? 'bg-gradient-to-r from-cyan-500 to-indigo-600 text-white shadow-lg shadow-cyan-500/20'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-cyan-600 dark:hover:text-cyan-400' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                </a>

            @elseif($item['type'] === 'group')
                @php
                    $childRoutes = collect($item['children'])->pluck('route')->all();
                    $groupActive = $isGroupActive($childRoutes);
                @endphp
                <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }">
                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                                   transition-all duration-200 cursor-pointer
                                   {{ $groupActive
                                        ? 'text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-900/20'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-cyan-600 dark:hover:text-cyan-400' }}">
                        <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                        <span class="whitespace-nowrap flex-1 text-left">{{ $item['label'] }}</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-transition.duration.200ms
                         class="mt-1 ml-4 pl-3 border-l border-gray-200 dark:border-gray-700 space-y-0.5">
                        @foreach($item['children'] as $child)
                            @php
                                $childActive = \Illuminate\Support\Facades\Route::has($child['route'])
                                    && (request()->routeIs($child['route']) || request()->routeIs($child['route'].'.*'));
                            @endphp
                            <a href="{{ $safeRoute($child['route']) }}"
                               class="block px-3 py-2 rounded-lg text-[13px] transition
                                      {{ $childActive
                                            ? 'text-cyan-600 dark:text-cyan-400 font-semibold bg-cyan-50 dark:bg-cyan-900/20'
                                            : 'text-gray-500 dark:text-gray-400 hover:text-cyan-600 dark:hover:text-cyan-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endforeach
    </nav>

    {{-- ============ FOOTER ============ --}}
    <div class="p-3 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
        <a href="{{ $safeRoute('logout') }}"
           onclick="event.preventDefault(); window.showLogoutModal(event);"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                  text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
            <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap">Logout</span>
        </a>
    </div>
</aside>

{{-- ============ OVERLAY MOBILE ============ --}}
<div x-show="$store.sidebar.mobileOpen"
     x-transition.opacity.duration.250ms
     @click="$store.sidebar.close()"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden"
     style="display: none;"></div>