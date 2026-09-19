@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>🎮</span> Daftar Game
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Pusat seluruh game yang terintegrasi dengan Game Center UNICODE.
                    Total <strong>{{ $totalGames }}</strong> game terdaftar.
                </p>
            </div>

            <a href="{{ route('owner.game.games.create') }}"
               class="inline-flex items-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-500/20">
                <i class="fas fa-plus-circle"></i> Tambah Game
            </a>
        </div>

        {{-- FILTER & PENCARIAN --}}
        <form method="GET" action="{{ route('owner.game.games.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text"
                       name="search"
                       placeholder="Cari game berdasarkan nama..."
                       value="{{ request('search') }}"
                       class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500">
            </div>

            <select name="status"
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition text-slate-800 dark:text-white">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="testing" {{ request('status') == 'testing' ? 'selected' : '' }}>Testing</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <select name="source_type"
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition text-slate-800 dark:text-white">
                <option value="">Semua Sumber</option>
                <option value="native" {{ request('source_type') == 'native' ? 'selected' : '' }}>UNICODE (Native)</option>
                <option value="external" {{ request('source_type') == 'external' ? 'selected' : '' }}>External</option>
            </select>

            <select name="game_type"
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 transition text-slate-800 dark:text-white">
                <option value="">Semua Tipe</option>
                <option value="typing" {{ request('game_type') == 'typing' ? 'selected' : '' }}>Typing</option>
                <option value="quiz" {{ request('game_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                <option value="coding" {{ request('game_type') == 'coding' ? 'selected' : '' }}>Coding</option>
                <option value="logic" {{ request('game_type') == 'logic' ? 'selected' : '' }}>Logic</option>
                <option value="tebak" {{ request('game_type') == 'tebak' ? 'selected' : '' }}>Tebak</option>
                <option value="reaction" {{ request('game_type') == 'reaction' ? 'selected' : '' }}>Reaction</option>
                <option value="custom" {{ request('game_type') == 'custom' ? 'selected' : '' }}>Custom</option>
            </select>

            <button type="submit"
                    class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center justify-center gap-2">
                <i class="fas fa-search"></i> Filter
            </button>

            @if(request()->anyFilled(['search', 'status', 'source_type', 'game_type']))
                <a href="{{ route('owner.game.games.index') }}"
                   class="px-5 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl">
                <p class="text-emerald-700 dark:text-emerald-300 font-bold">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl">
                <p class="text-red-700 dark:text-red-300 font-bold">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </p>
                @if(session('error_details'))
                    <pre class="mt-2 text-xs text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 p-3 rounded-lg overflow-auto max-h-40">{{ session('error_details') }}</pre>
                @endif
            </div>
        @endif

        {{-- GRID GAME --}}
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($games as $game)
                @php
                    $gameImages = [];
                    if (!empty($game->game_images)) {
                        $gameImages = is_array($game->game_images) ? $game->game_images : (json_decode($game->game_images, true) ?? []);
                    }
                    if (empty($gameImages) && !empty($game->cover_images)) {
                        $gameImages = is_array($game->cover_images) ? $game->cover_images : (json_decode($game->cover_images, true) ?? []);
                    }

                    $imageCount = count($gameImages);
                    $validImages = [];
                    foreach ($gameImages as $img) {
                        if ($img && Storage::disk('public')->exists($img)) {
                            $validImages[] = $img;
                        }
                    }
                    $hasValidImage = count($validImages) > 0;
                    $imagesToDisplay = $hasValidImage ? $validImages : $gameImages;
                    $imageCount = count($imagesToDisplay);

                    $coverImageUrl = '';
                    if ($hasValidImage && count($validImages) > 0) {
                        $coverImageUrl = asset('storage/' . $validImages[0]);
                    } elseif (count($gameImages) > 0) {
                        $coverImageUrl = asset('storage/' . $gameImages[0]);
                    }

                    $diagStatus = $game->diagnostic_status ?? 'pending';
                    $isPlayable = in_array($diagStatus, ['passed', 'warning']);

                    $playUrl = '#';
                    if ($game->source_type === 'native') {
                        $user = auth()->user();
                        $token = md5($game->id . $user->id . 'secret');
                        $playUrl = url("/owner/game/games/{$game->id}/gameplay/{$game->game_type}/{$token}");
                    }

                    $statusColors = [
                        'published'   => 'bg-emerald-500/80 text-white dark:bg-emerald-600/90',
                        'testing'     => 'bg-amber-500/80 text-white dark:bg-amber-600/90',
                        'maintenance' => 'bg-red-500/80 text-white dark:bg-red-600/90',
                        'archived'    => 'bg-slate-500/80 text-white dark:bg-slate-600/90',
                        'draft'       => 'bg-slate-500/80 text-white dark:bg-slate-600/90',
                    ];
                    $statusColor = $statusColors[$game->status] ?? 'bg-slate-500/80 text-white';

                    $diagColors = [
                        'passed'  => 'bg-emerald-500/80 text-white',
                        'warning' => 'bg-amber-500/80 text-white',
                        'failed'  => 'bg-red-500/80 text-white',
                        'pending' => 'bg-slate-500/80 text-white',
                    ];
                    $diagColor = $diagColors[$diagStatus] ?? 'bg-slate-500/80 text-white';

                    $externalUrl = $game->url ?? '#';
                @endphp

                {{-- Card Game --}}
                <div class="game-card group bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 hover:-translate-y-1 hover:shadow-xl hover:z-50 transition-all duration-300 relative cursor-pointer overflow-visible"
                     data-id="{{ $game->id }}"
                     data-name="{{ $game->name }}"
                     data-description="{{ $game->description ?? '' }}"
                     data-status="{{ $game->status ?? '' }}"
                     data-source_type="{{ $game->source_type ?? '' }}"
                     data-game_type="{{ $game->game_type ?? '' }}"
                     data-cover="{{ $coverImageUrl }}"
                     data-url="{{ $externalUrl }}"
                     data-version="{{ $game->version ?? '1.0.0' }}"
                     data-player_count="{{ $game->player_count ?? 0 }}"
                     data-total_matches="{{ $game->total_matches ?? 0 }}"
                     data-icon="{{ $game->icon ?? '🎮' }}">

                    {{-- BANNER / CAROUSEL --}}
                    <div class="relative h-40 bg-gradient-to-br from-indigo-600/20 via-purple-600/20 to-cyan-500/20 flex items-center justify-center overflow-hidden rounded-t-3xl carousel-container">

                        @if($hasValidImage && $imageCount > 0)
                            <div class="relative w-full h-full carousel-slides-wrapper">
                                @foreach($imagesToDisplay as $index => $img)
                                    <div class="carousel-slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                                         data-index="{{ $index }}">
                                        <img src="{{ asset('storage/' . $img) }}"
                                             alt="Cover game {{ $game->name }} - {{ $index+1 }}"
                                             class="w-full h-full object-cover cursor-pointer lightbox-trigger"
                                             draggable="false"
                                             oncontextmenu="return false;"
                                             loading="lazy">
                                    </div>
                                @endforeach
                            </div>

                            @if($imageCount > 1)
                                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-1.5 z-10">
                                    @foreach($imagesToDisplay as $idx => $img)
                                        <span class="carousel-dot w-1.5 h-1.5 rounded-full bg-white/50 transition-all duration-300 {{ $idx === 0 ? 'bg-white scale-125' : '' }}"
                                              data-index="{{ $idx }}"></span>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800">
                                <span class="text-6xl" aria-hidden="true">{{ $game->icon ?? '🎮' }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tidak ada cover</span>
                            </div>
                        @endif

                        {{-- Badge Sumber --}}
                        <span class="absolute top-3 right-3 text-[10px] px-2.5 py-1 rounded-full font-bold shadow-sm
                            {{ $game->source_type === 'native'
                                ? 'bg-purple-500/80 text-white dark:bg-purple-600/90'
                                : 'bg-cyan-500/80 text-white dark:bg-cyan-600/90' }}">
                            <i class="fas {{ $game->source_type === 'native' ? 'fa-code' : 'fa-globe' }} mr-1"></i>
                            {{ $game->source_type === 'native' ? 'UNICODE' : 'External' }}
                        </span>

                        {{-- Badge Status --}}
                        <span class="absolute bottom-3 right-3 text-[10px] px-2.5 py-1 rounded-full font-bold shadow-sm {{ $statusColor }}">
                            {{ ucfirst($game->status) }}
                        </span>

                        @if($game->source_type === 'native')
                            <span class="absolute top-3 left-3 text-[8px] px-1.5 py-0.5 rounded-full font-bold shadow-sm {{ $diagColor }}">
                                {{ strtoupper($diagStatus) }}
                            </span>
                        @endif
                    </div>

                    {{-- KONTEN CARD --}}
                    <div class="p-5 pointer-events-none">
                        <div class="flex justify-between items-start gap-3">
                            <div class="min-w-0 flex-1">
                                <h2 class="font-black text-lg text-slate-800 dark:text-white truncate">{{ $game->name }}</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $game->description ?? 'Tidak ada deskripsi' }}
                                </p>
                            </div>
                        </div>

                        @if($game->source_type === 'native' && $game->game_type)
                            <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700/50">
                                {{ ucfirst($game->game_type) }}
                                @if($game->difficulty)
                                    · <span class="capitalize">{{ $game->difficulty }}</span>
                                @endif
                            </span>
                        @endif

                        @if($game->source_type === 'external' && $game->url)
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate">
                                <i class="fas fa-link mr-1"></i>
                                {{ $game->url }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Integrasi: {{ ucfirst($game->integration_level ?? 'link') }}
                            </p>
                        @endif

                        {{-- Statistik --}}
                        <div class="grid grid-cols-3 gap-2 mt-4">
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2.5 text-center">
                                <div class="flex items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-users text-[10px]"></i>
                                    <p class="text-[10px] font-medium">Pemain</p>
                                </div>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ number_format($game->player_count ?? 0) }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2.5 text-center">
                                <div class="flex items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-trophy text-[10px]"></i>
                                    <p class="text-[10px] font-medium">Match</p>
                                </div>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ number_format($game->total_matches ?? 0) }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-100 dark:bg-slate-800 p-2.5 text-center">
                                <div class="flex items-center justify-center gap-1 text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-code-branch text-[10px]"></i>
                                    <p class="text-[10px] font-medium">Versi</p>
                                </div>
                                <p class="font-bold text-sm text-slate-800 dark:text-white">{{ $game->version ?? '1.0.0' }}</p>
                            </div>
                        </div>

                        @if($game->source_type === 'native')
                            <div class="flex flex-wrap gap-2 mt-3 text-[10px]">
                                @if($game->support_rating)
                                    <span class="px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-700/50">⭐ Rating</span>
                                @endif
                                @if($game->support_matchmaking)
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-700/50">⚡ Matchmaking</span>
                                @endif
                                @if($game->support_challenge)
                                    <span class="px-2 py-0.5 rounded-full bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-700/50">🏆 Challenge</span>
                                @endif
                                @if($game->show_in_leaderboard)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-700/50">📊 Leaderboard</span>
                                @endif
                            </div>
                        @endif

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-wrap items-center gap-2 mt-4 pointer-events-auto">
                            
                            <button type="button"
                                    class="view-game-btn flex-1 min-w-[60px] py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold text-center transition"
                                    data-id="{{ $game->id }}"
                                    data-name="{{ $game->name }}"
                                    data-description="{{ $game->description ?? '' }}"
                                    data-status="{{ $game->status ?? '' }}"
                                    data-source_type="{{ $game->source_type ?? '' }}"
                                    data-game_type="{{ $game->game_type ?? '' }}"
                                    data-cover="{{ $coverImageUrl }}"
                                    data-url="{{ $externalUrl }}"
                                    data-version="{{ $game->version ?? '1.0.0' }}"
                                    data-player_count="{{ $game->player_count ?? 0 }}"
                                    data-total_matches="{{ $game->total_matches ?? 0 }}"
                                    data-icon="{{ $game->icon ?? '🎮' }}"
                                    aria-label="Lihat detail game {{ $game->name }}">
                                <i class="fas fa-eye mr-1"></i> Lihat
                            </button>

                            @if($game->source_type === 'native')
                                @php
                                    $buttonClass = $isPlayable
                                        ? 'bg-emerald-500 hover:bg-emerald-600 text-white'
                                        : 'bg-red-500 hover:bg-red-600 text-white cursor-not-allowed opacity-60';
                                    $disabledAttr = $isPlayable ? '' : 'disabled';
                                    $buttonText = $isPlayable ? 'Mainkan' : 'Tidak Siap';
                                @endphp
                                <a href="{{ $isPlayable ? $playUrl : '#' }}"
                                   class="flex-1 min-w-[60px] py-2 rounded-xl text-sm font-bold text-center transition shadow-sm {{ $buttonClass }}"
                                   aria-label="Mainkan game {{ $game->name }}"
                                   {{ $disabledAttr }}>
                                    <i class="fas fa-play mr-1"></i> {{ $buttonText }}
                                </a>
                            @else
                                <a href="{{ $externalUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="flex-1 min-w-[60px] py-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-bold text-center transition shadow-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i> Buka
                                </a>
                            @endif

                            {{-- ACTION MENU (DIPERBAIKI: Ganti mt-3 menjadi pt-3 agar tidak ada celah kosong) --}}
                            <div class="relative dropdown-container pointer-events-auto z-50">
                                <button type="button"
                                        class="dropdown-toggle flex-none w-10 h-10 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition shadow-sm flex items-center justify-center"
                                        aria-label="Buka menu aksi {{ $game->name }}"
                                        aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>

                                <div class="dropdown-menu hidden absolute right-0 top-full pt-3 w-[320px] max-w-[calc(100vw-2rem)]">
                                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden">
                                    
                                    <div class="px-4 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                                <i class="fas fa-gamepad"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-black text-slate-800 dark:text-white truncate">{{ $game->name }}</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Menu Aksi Game</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3">
                                        <div class="grid grid-cols-2 gap-2">
                                            
                                            <button type="button"
                                                    class="view-game-btn flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition"
                                                    data-id="{{ $game->id }}"
                                                    data-name="{{ $game->name }}"
                                                    data-description="{{ $game->description ?? '' }}"
                                                    data-status="{{ $game->status ?? '' }}"
                                                    data-source_type="{{ $game->source_type ?? '' }}"
                                                    data-game_type="{{ $game->game_type ?? '' }}"
                                                    data-cover="{{ $coverImageUrl }}"
                                                    data-url="{{ $externalUrl }}"
                                                    data-version="{{ $game->version ?? '1.0.0' }}"
                                                    data-player_count="{{ $game->player_count ?? 0 }}"
                                                    data-total_matches="{{ $game->total_matches ?? 0 }}"
                                                    data-icon="{{ $game->icon ?? '🎮' }}">
                                                <i class="fas fa-eye text-lg text-slate-500"></i>
                                                <span class="text-xs font-bold">Lihat</span>
                                            </button>

                                            <a href="{{ route('owner.game.games.edit', $game) }}"
                                               class="flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 transition">
                                                <i class="fas fa-cog text-lg"></i>
                                                <span class="text-xs font-bold">Kelola</span>
                                            </a>

                                            @if($game->source_type === 'native')
                                                <a href="{{ route('owner.game.games.diagnostic', $game) }}"
                                                   class="flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 transition">
                                                    <i class="fas fa-flask text-lg"></i>
                                                    <span class="text-xs font-bold">Diagnostic</span>
                                                </a>
                                            @endif

                                            <a href="{{ route('owner.game.games.settings', $game) }}"
                                               class="flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-purple-50 dark:bg-purple-500/10 hover:bg-purple-100 dark:hover:bg-purple-500/20 text-purple-700 dark:text-purple-300 transition">
                                                <i class="fas fa-sliders-h text-lg"></i>
                                                <span class="text-xs font-bold">Settings</span>
                                            </a>

                                            <a href="{{ route('owner.game.games.qr', $game) }}"
                                               class="flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-cyan-50 dark:bg-cyan-500/10 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 transition">
                                                <i class="fas fa-qrcode text-lg"></i>
                                                <span class="text-xs font-bold">QR Code</span>
                                            </a>

                                            @if($game->source_type === 'native' && in_array(auth()->user()->level->name ?? '', ['owner', 'superadmin', 'ketua']))
                                                @php
                                                    $testToken = md5($game->id . auth()->id() . 'secret');
                                                    $testUrl = route('owner.game.games.testing.token', ['game' => $game->id, 'token' => $testToken]);
                                                @endphp
                                                <a href="{{ $testUrl }}"
                                                   class="flex flex-col items-center justify-center gap-2 min-h-[76px] rounded-xl bg-pink-50 dark:bg-pink-500/10 hover:bg-pink-100 dark:hover:bg-pink-500/20 text-pink-700 dark:text-pink-300 transition">
                                                    <i class="fas fa-vial text-lg"></i>
                                                    <span class="text-xs font-bold">Testing</span>
                                                </a>
                                            @endif

                                        </div>

                                        @if($game->status === 'published')
                                            <form action="{{ route('owner.game.games.unpublish', $game) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 transition text-left">
                                                    <i class="fas fa-eye-slash w-5 text-center"></i>
                                                    <div>
                                                        <p class="text-xs font-black">Unpublish Game</p>
                                                        <p class="text-[10px] opacity-70">Sembunyikan game dari publik</p>
                                                    </div>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('owner.game.games.publish', $game) }}" method="POST" class="mt-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 transition text-left">
                                                    <i class="fas fa-eye w-5 text-center"></i>
                                                    <div>
                                                        <p class="text-xs font-black">Publish Game</p>
                                                        <p class="text-[10px] opacity-70">Tampilkan game ke publik</p>
                                                    </div>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('owner.game.games.destroy', $game) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin ingin menghapus game {{ addslashes($game->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 transition text-left">
                                                <i class="fas fa-trash w-5 text-center"></i>
                                                <div>
                                                    <p class="text-xs font-black">Hapus Game</p>
                                                    <p class="text-[10px] opacity-70">Pindahkan game ke tempat sampah</p>
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-slate-500 dark:text-slate-400">
                    <div class="text-7xl mb-4 opacity-50">🎮</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-700 dark:text-slate-300">Belum Ada Game</h3>
                    <p class="text-sm">Tambahkan game pertama Anda ke Game Center UNICODE.</p>
                    <a href="{{ route('owner.game.games.create') }}"
                       class="mt-4 inline-block px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Game
                    </a>
                </div>
            @endforelse
        </div>

        {{-- PAGINASI --}}
        <div class="mt-6">
            {{ $games->withQueryString()->links() }}
        </div>

        {{-- STATISTIK CEPAT & LINK TRASH --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Game</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalGames }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Game UNICODE</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalNative }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Game External</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalExternal }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Published</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalPublished }}</p>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('owner.game.games.trash') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-sm text-slate-600 dark:text-slate-400 transition">
                <i class="fas fa-trash-alt"></i>
                Tempat Sampah
                <span class="bg-red-500/20 text-red-500 dark:text-red-400 px-2 py-0.5 rounded-full text-xs font-bold">
                    {{ \App\Models\Game::onlyTrashed()->count() }}
                </span>
            </a>
        </div>

    </div>
</div>

{{-- LIGHTBOX MODAL --}}
<div id="lightbox-overlay" class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center hidden pointer-events-none">
    <div id="lightbox-content" class="relative max-w-5xl max-h-[90vh] pointer-events-auto">
        <img id="lightbox-image" src="" alt="Lightbox" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
             draggable="false"
             oncontextmenu="return false;"
             style="user-select: none; -webkit-user-drag: none;">
        <button id="lightbox-close" class="absolute -top-12 right-0 text-white text-4xl hover:text-indigo-400 transition font-light">&times;</button>
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white/70 text-sm bg-black/50 px-4 py-1 rounded-full">
            Klik di luar gambar atau tekan ESC untuk tutup
        </div>
    </div>
</div>

{{-- MODAL PREVIEW GAME --}}
<div id="game-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700">
        <button id="modal-close" class="absolute top-3 right-3 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 text-xl font-bold transition z-10">
            &times;
        </button>
        <div id="modal-content" class="p-6">
        </div>
    </div>
</div>

{{-- SCRIPT UTAMA --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // 1. DROPDOWN TOGGLE (DIPERBAIKI: Timer Jeda Anti Glitch)
    // ============================================================
    let dropdownCloseTimers = new Map();

    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const container = this.closest('.dropdown-container');
            const menu = container.querySelector('.dropdown-menu');
            const isOpen = !menu.classList.contains('hidden');

            // Tutup semua menu lain dan batalkan timer mereka
            document.querySelectorAll('.dropdown-menu').forEach(function(otherMenu) {
                otherMenu.classList.add('hidden');
                clearTimeout(dropdownCloseTimers.get(otherMenu));
            });
            document.querySelectorAll('.dropdown-toggle').forEach(function(otherToggle) {
                otherToggle.setAttribute('aria-expanded', 'false');
            });

            // Buka menu yang dipilih
            if (!isOpen) {
                menu.classList.remove('hidden');
                this.setAttribute('aria-expanded', 'true');

                const rect = menu.getBoundingClientRect();
                if (rect.right > window.innerWidth - 12) {
                    menu.style.right = '0';
                    menu.style.left = 'auto';
                }
                if (rect.left < 12) {
                    menu.style.left = '0';
                    menu.style.right = 'auto';
                }
            }
        });
    });

    // ============================================================
    // 2. ANTI GLITCH: Tahan menu tetap terbuka saat cursor berpindah
    // ============================================================
    document.querySelectorAll('.dropdown-container').forEach(function(container) {
        const menu = container.querySelector('.dropdown-menu');

        // Masuk ke area container -> Batalkan timer penutupan
        container.addEventListener('mouseenter', function() {
            clearTimeout(dropdownCloseTimers.get(menu));
        });

        // Keluar dari area container -> Mulai timer untuk menutup (beri jeda 300ms)
        container.addEventListener('mouseleave', function() {
            const timer = setTimeout(() => {
                menu.classList.add('hidden');
                const toggle = container.querySelector('.dropdown-toggle');
                toggle.setAttribute('aria-expanded', 'false');
            }, 300); 

            dropdownCloseTimers.set(menu, timer);
        });
    });

    // Klik di luar area dropdown menutup semua menu
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                menu.classList.add('hidden');
                clearTimeout(dropdownCloseTimers.get(menu));
            });
            document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // 3. CAROUSEL OTOMATIS
    document.querySelectorAll('.carousel-container').forEach(function(container) {
        const slides = container.querySelectorAll('.carousel-slide');
        const dots = container.querySelectorAll('.carousel-dot');
        const total = slides.length;
        if (total <= 1) return;

        let currentIndex = 0;
        let interval = null;

        function goTo(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('opacity-100', i === index);
                slide.classList.toggle('opacity-0', i !== index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === index);
                dot.classList.toggle('bg-white/50', i !== index);
                dot.classList.toggle('scale-125', i === index);
            });
            currentIndex = index;
        }

        function nextSlide() {
            goTo((currentIndex + 1) % total);
        }

        function startAutoplay() {
            if (interval) clearInterval(interval);
            interval = setInterval(nextSlide, 2500);
        }

        function stopAutoplay() {
            if (interval) {
                clearInterval(interval);
                interval = null;
            }
        }

        container.addEventListener('mouseenter', stopAutoplay);
        container.addEventListener('mouseleave', startAutoplay);

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', function(e) {
                e.stopPropagation();
                stopAutoplay();
                goTo(idx);
                startAutoplay();
            });
        });

        startAutoplay();
    });

    // 4. KLIK CARD -> MEMBUKA MODAL
    const gameModal = document.getElementById('game-modal');
    const modalContent = document.getElementById('modal-content');
    const modalClose = document.getElementById('modal-close');

    function openModal(gameData) {
        let html = `
            <div class="flex flex-col gap-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white">${gameData.name}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">${gameData.description || 'Tidak ada deskripsi'}</p>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 h-52 flex items-center justify-center">
                    ${gameData.cover ? `<img src="${gameData.cover}" alt="Cover" class="w-full h-full object-cover">` : `<span class="text-6xl">${gameData.icon || '🎮'}</span>`}
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center bg-slate-50 dark:bg-slate-800 p-3 rounded-xl">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="font-bold text-sm capitalize">${gameData.status}</p>
                    </div>
                    <div class="text-center bg-slate-50 dark:bg-slate-800 p-3 rounded-xl">
                        <p class="text-xs text-slate-500">Tipe</p>
                        <p class="font-bold text-sm capitalize">${gameData.game_type || '-'}</p>
                    </div>
                    <div class="text-center bg-slate-50 dark:bg-slate-800 p-3 rounded-xl">
                        <p class="text-xs text-slate-500">Versi</p>
                        <p class="font-bold text-sm">${gameData.version || '1.0.0'}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i class="fas fa-users text-indigo-500"></i> Pemain: ${gameData.player_count}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <i class="fas fa-trophy text-yellow-500"></i> Match: ${gameData.total_matches}
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <a href="${gameData.source_type === 'external' ? gameData.url : '#'}" 
                       class="flex-1 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-center font-bold transition"
                       ${gameData.source_type !== 'external' ? 'disabled' : 'target="_blank" rel="noopener"'}>
                        Mainkan
                    </a>
                    <a href="/owner/game/games/${gameData.id}/edit" 
                       class="flex-1 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-center font-bold transition">
                        Edit
                    </a>
                </div>
            </div>
        `;

        modalContent.innerHTML = html;
        gameModal.classList.remove('hidden');
        gameModal.classList.add('flex');
    }

    function closeModal() {
        gameModal.classList.add('hidden');
        gameModal.classList.remove('flex');
        modalContent.innerHTML = '';
    }

    document.querySelectorAll('.game-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.closest('a, button, .dropdown-container, .carousel-dot')) return;
            e.preventDefault();
            e.stopPropagation();

            const data = {
                id: this.dataset.id,
                name: this.dataset.name,
                description: this.dataset.description,
                status: this.dataset.status,
                source_type: this.dataset.source_type,
                game_type: this.dataset.game_type,
                cover: this.dataset.cover,
                url: this.dataset.url,
                version: this.dataset.version,
                player_count: this.dataset.player_count,
                total_matches: this.dataset.total_matches,
                icon: this.dataset.icon
            };
            openModal(data);
        });
    });

    document.querySelectorAll('.view-game-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const data = {
                id: this.dataset.id,
                name: this.dataset.name,
                description: this.dataset.description,
                status: this.dataset.status,
                source_type: this.dataset.source_type,
                game_type: this.dataset.game_type,
                cover: this.dataset.cover,
                url: this.dataset.url,
                version: this.dataset.version,
                player_count: this.dataset.player_count,
                total_matches: this.dataset.total_matches,
                icon: this.dataset.icon
            };
            openModal(data);
        });
    });

    modalClose.addEventListener('click', closeModal);
    gameModal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // 5. LIGHTBOX
    const lightboxOverlay = document.getElementById('lightbox-overlay');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');

    function showLightbox(src) {
        lightboxImage.src = src;
        lightboxOverlay.classList.remove('hidden');
        lightboxOverlay.style.pointerEvents = 'auto';
        document.body.style.overflow = 'hidden';
    }

    function hideLightbox() {
        lightboxOverlay.classList.add('hidden');
        lightboxOverlay.style.pointerEvents = 'none';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.lightbox-trigger').forEach(function(img) {
        img.addEventListener('dblclick', function(e) {
            e.stopPropagation();
            showLightbox(this.src);
        });
    });

    lightboxClose.addEventListener('click', hideLightbox);
    lightboxOverlay.addEventListener('click', function(e) {
        if (e.target === this) hideLightbox();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideLightbox();
            closeModal();
        }
    });

    lightboxImage.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
    lightboxImage.addEventListener('dragstart', function(e) { e.preventDefault(); return false; });

    console.log('🚀 Game Center UNICODE siap! Dropdown anti tertutup sendiri.');
});
</script>

<style>
    .carousel-slide {
        transition: opacity 0.7s ease-in-out;
    }
    .game-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: visible !important; 
    }
    .game-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.2);
        z-index: 50;
    }
    .dropdown-menu {
        transform-origin: top right;
        animation: menuPopup 0.18s ease-out;
        z-index: 10000;
    }
    @keyframes menuPopup {
        from {
            opacity: 0;
            transform: translateY(-6px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    #game-modal {
        z-index: 99999 !important;
    }
    #game-modal > div {
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
@endsection