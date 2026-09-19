@extends('layouts.owner')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <div class="container mx-auto p-4 md:p-6 space-y-6">

        {{-- ============================================================ --}}
        {{-- HEADER --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <span>{{ $game->icon ?? '🎮' }}</span>
                    {{ $game->name }}
                </h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Detail lengkap game yang terintegrasi dengan Game Center UNICODE.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('owner.game.games.index') }}"
                   class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('owner.game.games.edit', $game) }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition flex items-center gap-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- NOTIFIKASI --}}
        {{-- ============================================================ --}}
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

        {{-- ============================================================ --}}
        {{-- CARD UTAMA: GAMBAR + INFORMASI --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- ============================================================ --}}
                {{-- KOLOM GAMBAR (Carousel) --}}
                {{-- ============================================================ --}}
                <div class="md:col-span-1 flex flex-col items-center">
                    @php
                        // Ambil semua gambar game
                        $gameImages = is_array($game->game_images) ? $game->game_images : (json_decode($game->game_images, true) ?? []);
                        if (empty($gameImages) && !empty($game->cover_images)) {
                            $gameImages = is_array($game->cover_images) ? $game->cover_images : (json_decode($game->cover_images, true) ?? []);
                        }
                        $hasImages = count($gameImages) > 0 && !empty($gameImages[0]) && Storage::disk('public')->exists($gameImages[0]);
                    @endphp

                    @if($hasImages)
                        {{-- Carousel Container --}}
                        <div class="relative w-full max-h-64 rounded-2xl overflow-hidden shadow-md carousel-container-show bg-slate-100 dark:bg-slate-800">
                            <div class="relative w-full h-64">
                                @foreach($gameImages as $idx => $img)
                                    <div class="carousel-slide-show absolute inset-0 transition-opacity duration-700 {{ $idx === 0 ? 'opacity-100' : 'opacity-0' }}"
                                         data-index="{{ $idx }}">
                                        <img src="{{ asset('storage/' . $img) }}"
                                             alt="{{ $game->name }} - Gambar {{ $idx+1 }}"
                                             class="w-full h-full object-contain cursor-pointer lightbox-trigger-show"
                                             draggable="false"
                                             oncontextmenu="return false;"
                                             loading="lazy">
                                    </div>
                                @endforeach
                            </div>

                            {{-- Dot Indikator --}}
                            @if(count($gameImages) > 1)
                                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-1.5 z-10">
                                    @foreach($gameImages as $idx => $img)
                                        <span class="carousel-dot-show w-2 h-2 rounded-full bg-white/50 transition-all duration-300 {{ $idx === 0 ? 'bg-white scale-125' : '' }}"
                                              data-index="{{ $idx }}"></span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Badge jumlah gambar --}}
                            <span class="absolute top-2 left-2 text-[10px] px-2 py-0.5 rounded-full bg-black/60 text-white font-bold z-10">
                                📷 {{ count($gameImages) }} gambar
                            </span>
                        </div>

                        {{-- Thumbnail kecil --}}
                        @if(count($gameImages) > 1)
                            <div class="flex gap-2 mt-3 flex-wrap justify-center">
                                @foreach(array_slice($gameImages, 0, 4) as $idx => $img)
                                    <img src="{{ asset('storage/' . $img) }}"
                                         alt="Thumbnail {{ $idx+1 }}"
                                         class="w-12 h-12 object-cover rounded-lg border-2 border-transparent hover:border-indigo-500 transition cursor-pointer thumbnail-show"
                                         data-index="{{ $idx }}"
                                         loading="lazy">
                                @endforeach
                                @if(count($gameImages) > 4)
                                    <span class="w-12 h-12 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                        +{{ count($gameImages) - 4 }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    @else
                        {{-- Fallback icon --}}
                        <div class="w-full max-h-64 bg-gradient-to-br from-indigo-600/20 via-purple-600/20 to-cyan-500/20 rounded-2xl flex items-center justify-center shadow-md">
                            <span class="text-8xl">{{ $game->icon ?? '🎮' }}</span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Tidak ada gambar</p>
                    @endif
                </div>

                {{-- ============================================================ --}}
                {{-- KOLOM INFORMASI --}}
                {{-- ============================================================ --}}
                <div class="md:col-span-2 space-y-3">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $game->source_type === 'native' ? 'bg-purple-500/20 text-purple-700 dark:text-purple-300' : 'bg-cyan-500/20 text-cyan-700 dark:text-cyan-300' }}">
                            <i class="fas {{ $game->source_type === 'native' ? 'fa-code' : 'fa-globe' }} mr-1"></i>
                            {{ $game->source_type === 'native' ? 'UNICODE' : 'External' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            {{ $game->status === 'published' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' :
                               ($game->status === 'testing' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' :
                               ($game->status === 'maintenance' ? 'bg-red-500/20 text-red-700 dark:text-red-300' :
                               'bg-slate-500/20 text-slate-700 dark:text-slate-300')) }}">
                            {{ ucfirst($game->status) }}
                        </span>
                        @if($game->game_type)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">
                                {{ ucfirst($game->game_type) }}
                            </span>
                        @endif
                        @if($game->difficulty)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-700 dark:text-yellow-300">
                                🏷️ {{ ucfirst($game->difficulty) }}
                            </span>
                        @endif
                        @if($game->source_type === 'native')
                            @php
                                $diagStatus = $game->diagnostic_status ?? 'pending';
                                $diagLabel = [
                                    'passed'  => '✅ Siap Dimainkan',
                                    'warning' => '⚠️ Perlu Perhatian',
                                    'failed'  => '❌ Tidak Siap',
                                    'pending' => '⏳ Belum Diperiksa',
                                ][$diagStatus] ?? '⏳ Unknown';
                                $diagColor = $diagStatus === 'passed' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' :
                                             ($diagStatus === 'warning' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300' :
                                             ($diagStatus === 'failed' ? 'bg-red-500/20 text-red-700 dark:text-red-300' :
                                             'bg-slate-500/20 text-slate-700 dark:text-slate-300'));
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $diagColor }}">
                                {{ $diagLabel }}
                            </span>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">📝 Deskripsi</h2>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $game->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    {{-- Detail info --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Developer</p>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $game->developer ?? 'UNICODE' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Versi</p>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $game->version ?? '1.0.0' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pemain</p>
                            <p class="font-bold text-slate-800 dark:text-white">{{ number_format($game->player_count ?? 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Total Match</p>
                            <p class="font-bold text-slate-800 dark:text-white">{{ number_format($game->total_matches ?? 0) }}</p>
                        </div>
                    </div>

                    {{-- Eksternal URL --}}
                    @if($game->source_type === 'external' && $game->url)
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">🔗 URL</p>
                            <a href="{{ $game->url }}" target="_blank" rel="noopener noreferrer"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                                {{ $game->url }}
                            </a>
                            <span class="ml-2 text-xs bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 px-2 py-0.5 rounded-full">
                                Integrasi: {{ ucfirst($game->integration_level ?? 'link') }}
                            </span>
                        </div>
                    @endif

                    {{-- Fitur Native --}}
                    @if($game->source_type === 'native')
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($game->support_rating)
                                <span class="px-2 py-1 rounded-full bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-700/50 text-xs">
                                    ⭐ Rating
                                </span>
                            @endif
                            @if($game->support_matchmaking)
                                <span class="px-2 py-1 rounded-full bg-cyan-500/10 text-cyan-700 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-700/50 text-xs">
                                    ⚡ Matchmaking
                                </span>
                            @endif
                            @if($game->support_challenge)
                                <span class="px-2 py-1 rounded-full bg-green-500/10 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-700/50 text-xs">
                                    🏆 Challenge
                                </span>
                            @endif
                            @if($game->show_in_leaderboard)
                                <span class="px-2 py-1 rounded-full bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-700/50 text-xs">
                                    📊 Leaderboard
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- KONFIGURASI GAME (Native) --}}
        {{-- ============================================================ --}}
        @if($game->source_type === 'native' && $game->game_type)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
                <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-sliders-h text-indigo-500"></i> Konfigurasi Game
                </h2>

                @if($game->game_type === 'typing' && isset($game->typing_settings))
                    @include('owner.game-center.games.partials.show-typing', ['settings' => $game->typing_settings])
                @elseif($game->game_type === 'quiz' && isset($game->quiz_settings))
                    @include('owner.game-center.games.partials.show-quiz', [
                        'settings' => $game->quiz_settings,
                        'questions' => $game->quiz_questions ?? []
                    ])
                @elseif(in_array($game->game_type, ['tebak', 'logic', 'coding', 'custom', 'reaction', 'puzzle', 'geometry_dash']) && isset($game->game_settings))
                    @include('owner.game-center.games.partials.show-generic', ['settings' => $game->game_settings])
                @else
                    <div class="text-center py-8 text-slate-500 dark:text-slate-400">
                        <i class="fas fa-cog text-4xl mb-3 block"></i>
                        <p>Belum ada konfigurasi yang tersimpan untuk game ini.</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- STATISTIK TAMBAHAN --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Rating Rata-rata</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ number_format($game->avg_rating ?? 0, 1) }}
                    <span class="text-sm font-normal text-slate-400">/ 5</span>
                </p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Sesi</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ number_format($game->sessions_count ?? 0) }}
                </p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Kompetisi</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ number_format($game->competitions_count ?? 0) }}
                </p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">Challenge</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ number_format($game->challenges_count ?? 0) }}
                </p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- AKTIVITAS TERBARU --}}
        {{-- ============================================================ --}}
        @if(isset($recentSessions) && $recentSessions->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
                <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-history text-amber-500"></i> Aktivitas Terbaru
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="text-left px-4 py-2 text-xs text-slate-500 dark:text-slate-400">Pemain</th>
                                <th class="text-left px-4 py-2 text-xs text-slate-500 dark:text-slate-400">Skor</th>
                                <th class="text-left px-4 py-2 text-xs text-slate-500 dark:text-slate-400">Mode</th>
                                <th class="text-left px-4 py-2 text-xs text-slate-500 dark:text-slate-400">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSessions as $session)
                                <tr class="border-t border-slate-200 dark:border-slate-700">
                                    <td class="px-4 py-2 font-medium text-slate-800 dark:text-white">
                                        {{ $session->player->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-4 py-2 font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($session->score) }}
                                    </td>
                                    <td class="px-4 py-2 text-slate-600 dark:text-slate-400">
                                        {{ ucfirst($session->mode ?? 'practice') }}
                                    </td>
                                    <td class="px-4 py-2 text-slate-500 dark:text-slate-400 text-xs">
                                        {{ $session->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- TOMBOL AKSI --}}
        {{-- ============================================================ --}}
        <div class="flex flex-wrap gap-3 justify-center">
            @if($game->source_type === 'native')
                @php
    $isPlayable = in_array($game->diagnostic_status ?? 'pending', ['passed', 'warning']);
    $playToken = md5($game->id . auth()->id() . 'secret');
    $playUrl = route('owner.game.games.gameplay', [
        'game'  => $game->id,
        'type'  => $game->game_type ?? 'quiz',   // ← tambahkan ini
        'token' => $playToken
    ]);
    $btnClass = $isPlayable
        ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-500/30'
        : 'bg-red-500 hover:bg-red-600 text-white shadow-red-500/30 cursor-not-allowed opacity-60';
    $disabled = $isPlayable ? '' : 'disabled';
    $btnText = $isPlayable ? '🎮 Mainkan' : '❌ Tidak Siap';
@endphp
                <a href="{{ $isPlayable ? $playUrl : '#' }}"
                   class="px-8 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2 {{ $btnClass }}"
                   {{ $disabled }}>
                    <i class="fas fa-play"></i> {{ $btnText }}
                </a>
                <a href="{{ route('owner.game.games.diagnostic', $game) }}"
                   class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition shadow-lg shadow-amber-500/30 flex items-center gap-2">
                    <i class="fas fa-flask"></i> Diagnostic
                </a>
                <a href="{{ route('owner.game.games.qr', $game) }}"
                   class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl font-bold transition shadow-lg shadow-cyan-500/30 flex items-center gap-2">
                    <i class="fas fa-qrcode"></i> QR Code
                </a>
                <a href="{{ route('owner.game.games.settings', $game) }}"
                   class="px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold transition shadow-lg shadow-purple-500/30 flex items-center gap-2">
                    <i class="fas fa-sliders-h"></i> Settings
                </a>
            @else
                <a href="{{ $game->url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="px-8 py-3 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl font-bold transition shadow-lg shadow-cyan-500/30 flex items-center gap-2">
                    <i class="fas fa-external-link-alt"></i> Buka Game
                </a>
            @endif

            <a href="{{ route('owner.game.games.edit', $game) }}"
               class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                <i class="fas fa-cog"></i> Kelola Game
            </a>

            <form action="{{ route('owner.game.games.destroy', $game) }}" method="POST"
                  onsubmit="return confirm('⚠️ Yakin ingin menghapus game ini? Data akan dipindahkan ke tempat sampah.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-8 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition shadow-lg shadow-red-500/30 flex items-center gap-2">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- FOOTER INFO --}}
        {{-- ============================================================ --}}
        <div class="text-center text-xs text-slate-400 dark:text-slate-500 pt-2 border-t border-slate-200 dark:border-slate-700">
            <p>
                🆔 ID: {{ $game->id }}
                &nbsp;·&nbsp;
                📅 Dibuat: {{ $game->created_at ? $game->created_at->format('d M Y, H:i') : 'N/A' }}
                &nbsp;·&nbsp;
                🔄 Terakhir diperbarui: {{ $game->updated_at ? $game->updated_at->diffForHumans() : 'N/A' }}
                @if($game->deleted_at)
                    &nbsp;·&nbsp;
                    <span class="text-red-500">🗑️ Dihapus: {{ $game->deleted_at->diffForHumans() }}</span>
                @endif
            </p>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- LIGHTBOX MODAL (sama seperti di index) --}}
{{-- ============================================================ --}}
<div id="lightbox-overlay-show" class="fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center hidden pointer-events-none">
    <div id="lightbox-content-show" class="relative max-w-5xl max-h-[90vh] pointer-events-auto">
        <img id="lightbox-image-show" src="" alt="Lightbox" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
             draggable="false"
             oncontextmenu="return false;"
             style="user-select: none; -webkit-user-drag: none;">
        <button id="lightbox-close-show" class="absolute -top-12 right-0 text-white text-4xl hover:text-indigo-400 transition font-light">&times;</button>
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white/70 text-sm bg-black/50 px-4 py-1 rounded-full">
            Klik di luar gambar atau tekan ESC untuk tutup
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT: CAROUSEL + LIGHTBOX + THUMBNAIL --}}
{{-- ============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // 1. CAROUSEL SHOW
    // ============================================================
    const container = document.querySelector('.carousel-container-show');
    if (container) {
        const slides = container.querySelectorAll('.carousel-slide-show');
        const dots = container.querySelectorAll('.carousel-dot-show');
        const total = slides.length;

        if (total > 1) {
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

            // Thumbnail click
            document.querySelectorAll('.thumbnail-show').forEach(function(thumb) {
                thumb.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    if (!isNaN(idx) && idx < total) {
                        stopAutoplay();
                        goTo(idx);
                        startAutoplay();
                    }
                });
            });

            startAutoplay();
        }
    }

    // ============================================================
    // 2. LIGHTBOX
    // ============================================================
    const overlay = document.getElementById('lightbox-overlay-show');
    const lightboxImg = document.getElementById('lightbox-image-show');
    const closeBtn = document.getElementById('lightbox-close-show');

    function showLightbox(src) {
        lightboxImg.src = src;
        overlay.classList.remove('hidden');
        overlay.style.pointerEvents = 'auto';
        document.body.style.overflow = 'hidden';
    }

    function hideLightbox() {
        overlay.classList.add('hidden');
        overlay.style.pointerEvents = 'none';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.lightbox-trigger-show').forEach(function(img) {
        img.addEventListener('dblclick', function(e) {
            e.stopPropagation();
            showLightbox(this.src);
        });
    });

    closeBtn.addEventListener('click', hideLightbox);
    overlay.addEventListener('click', function(e) {
        if (e.target === this) hideLightbox();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideLightbox();
    });

    // Cegah download
    lightboxImg.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
    lightboxImg.addEventListener('dragstart', function(e) { e.preventDefault(); return false; });

    console.log('🚀 Show page siap! Carousel dan lightbox aktif.');
});
</script>
@endsection