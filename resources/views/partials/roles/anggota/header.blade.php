@php
    $user = auth()->user();
    $userName = $user->name ?? 'Anggota';
    $userInitial = strtoupper(mb_substr($userName, 0, 1));
    $userAvatar = $user->avatar_url ?? null;
    $userAvatarUrl = $userAvatar ? (\Illuminate\Support\Str::startsWith($userAvatar, ['http://','https://']) ? $userAvatar : asset('storage/'.$userAvatar)) : null;
    $points = $user->total_points ?? 0;
    $notifCount = 0;
    try { $notifCount = $user?->unreadNotifications()->count() ?? 0; } catch (\Throwable $e) {}
@endphp

<header class="sticky top-0 z-30 h-16 flex items-center justify-between gap-3 px-4 lg:px-6
               bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
               border-b border-gray-200 dark:border-gray-800 shrink-0">

    <div class="flex items-center gap-3 min-w-0">
        <button id="sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl
                text-gray-600 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 active:scale-95 transition">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="hidden sm:flex items-center gap-2 text-sm min-w-0">
            <a href="{{ url('/') }}" class="text-gray-400 hover:text-blue-600 transition">
                <i class="fas fa-home text-xs"></i>
            </a>
            @php
                $rn = request()->route()?->getName() ?? 'anggota.dashboard';
                $cleaned = preg_replace('/^[a-z]+\./', '', $rn);
                $parts = array_filter(explode('.', $cleaned));
            @endphp
            @foreach($parts as $p)
                <i class="fas fa-chevron-right text-[8px] text-gray-300 dark:text-gray-600"></i>
                <span class="text-gray-600 dark:text-gray-300 font-medium truncate">{{ ucwords(str_replace(['-','_'], ' ', $p)) }}</span>
            @endforeach
        </nav>
    </div>

    <div class="flex items-center gap-1.5 shrink-0">
        <button id="dark-toggle" class="w-9 h-9 flex items-center justify-center rounded-lg
                text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-moon text-sm dark:hidden"></i>
            <i class="fas fa-sun text-sm hidden dark:inline"></i>
        </button>

        <a href="{{ route('anggota.pengumuman') }}" class="relative w-9 h-9 flex items-center justify-center rounded-lg
                text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <i class="fas fa-bell text-sm"></i>
            @if($notifCount > 0)
                <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
            @endif
        </a>

        <div class="relative group">
            <button class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center
                            text-white font-bold text-xs overflow-hidden ring-2 ring-white dark:ring-gray-900">
                    @if($userAvatarUrl) <img src="{{ $userAvatarUrl }}" class="w-full h-full object-cover"> @else {{ $userInitial }} @endif
                </div>
                <div class="hidden sm:flex flex-col items-start leading-tight">
                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-100 max-w-[110px] truncate">{{ $userName }}</span>
                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-medium">Anggota</span>
                </div>
                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
            </button>
            <div class="absolute right-0 mt-2 w-64 origin-top-right rounded-2xl
                        bg-white dark:bg-gray-900 shadow-xl ring-1 ring-black/5 dark:ring-white/10
                        border border-gray-200 dark:border-gray-800
                        opacity-0 invisible group-hover:opacity-100 group-hover:visible
                        transition-all duration-200 translate-y-1 group-hover:translate-y-0 z-50">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold overflow-hidden">
                            @if($userAvatarUrl) <img src="{{ $userAvatarUrl }}" class="w-full h-full object-cover"> @else {{ $userInitial }} @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">{{ $userName }}</p>
                            <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium">Anggota • Aktif</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-1 text-[11px] text-gray-500 dark:text-gray-400">
                        <i class="fas fa-star text-amber-400"></i>
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ number_format($points, 0, ',', '.') }}</span>
                        <span>LeaderPoint</span>
                    </div>
                </div>
                <div class="p-2">
                    <a href="{{ route('anggota.profil') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-user w-4 text-blue-500"></i> Profil
                    </a>
                    <a href="{{ route('anggota.leaderpoint') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-trophy w-4 text-amber-500"></i> Leaderboard
                    </a>
                    <a href="{{ route('anggota.jadwal') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-calendar w-4 text-blue-500"></i> Jadwal
                    </a>
                    <a href="{{ route('anggota.game') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-gamepad w-4 text-purple-500"></i> Game
                    </a>
                    <a href="{{ route('anggota.chatting') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-comments w-4 text-green-500"></i> Chatting
                    </a>
                    <a href="{{ route('anggota.misi') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <i class="fas fa-crosshairs w-4 text-pink-500"></i> Misi
                    </a>
                </div>
                <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" onclick="showLogoutModal(event)"
                            class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fas fa-sign-out-alt w-4"></i> Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
