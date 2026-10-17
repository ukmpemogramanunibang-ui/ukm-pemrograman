@php
    $user = auth()->user();
    $isLoggedIn = (bool) $user;
    $userName = $user->name ?? 'Pengunjung';
    $userInitial = strtoupper(mb_substr($userName, 0, 1));
    $userAvatar = $user->avatar_url ?? null;
    $userAvatarUrl = $userAvatar ? (\Illuminate\Support\Str::startsWith($userAvatar, ['http://','https://']) ? $userAvatar : asset('storage/'.$userAvatar)) : null;
@endphp

<header class="sticky top-0 z-30 h-16 flex items-center justify-between gap-3 px-4 lg:px-6
               bg-white/85 dark:bg-gray-900/85 backdrop-blur-md
               border-b border-gray-200 dark:border-gray-800 shrink-0">

    <div class="flex items-center gap-3 min-w-0">
        <button id="sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl
                text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/30 active:scale-95 transition">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="hidden sm:flex items-center gap-2 text-sm min-w-0">
            <a href="{{ url('/') }}" class="text-gray-400 hover:text-amber-600 transition">
                <i class="fas fa-home text-xs"></i>
            </a>
            @php
                $rn = request()->route()?->getName() ?? 'landing';
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

        @if(!$isLoggedIn)
            <a href="{{ route('login') }}"
               class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg
                      bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm font-semibold
                      shadow-md shadow-amber-500/20 hover:shadow-lg transition">
                <i class="fas fa-sign-in-alt text-xs"></i>
                Masuk
            </a>
            <a href="{{ route('guest.register') }}"
               class="sm:hidden w-9 h-9 flex items-center justify-center rounded-lg
                      bg-amber-500 text-white hover:bg-amber-600 transition">
                <i class="fas fa-user-plus text-sm"></i>
            </a>
        @else
            <div class="relative group">
                <button class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center
                                text-white font-bold text-xs overflow-hidden ring-2 ring-white dark:ring-gray-900">
                        @if($userAvatarUrl) <img src="{{ $userAvatarUrl }}" class="w-full h-full object-cover"> @else {{ $userInitial }} @endif
                    </div>
                    <div class="hidden sm:flex flex-col items-start leading-tight">
                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-100 max-w-[110px] truncate">{{ $userName }}</span>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Pengunjung</span>
                    </div>
                    <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                </button>
                <div class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl
                            bg-white dark:bg-gray-900 shadow-xl ring-1 ring-black/5 dark:ring-white/10
                            border border-gray-200 dark:border-gray-800
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible
                            transition-all duration-200 translate-y-1 group-hover:translate-y-0 z-50">
                    <div class="p-2">
                        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                            <i class="fas fa-tachometer-alt w-4 text-amber-500"></i> Dashboard
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                            <i class="fas fa-user w-4 text-blue-500"></i> Profil
                        </a>
                    </div>
                    <div class="p-2 border-t border-gray-100 dark:border-gray-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <i class="fas fa-sign-out-alt w-4"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</header>
