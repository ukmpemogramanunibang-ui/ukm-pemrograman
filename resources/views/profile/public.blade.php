<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <title>{{ $user->name }} – Profile UNICODE</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* =========================================================
           RESET & BASE
           ========================================================= */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem 1rem 3rem;
            background:
                radial-gradient(circle at 15% 8%, #dbeafe 0%, transparent 40%),
                radial-gradient(circle at 85% 92%, #ede9fe 0%, transparent 45%),
                #f3f4f6;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            transition: background .35s ease, color .35s ease;
        }
        .dark body {
            background:
                radial-gradient(circle at 15% 8%, #1e293b 0%, transparent 40%),
                radial-gradient(circle at 85% 92%, #3b0764 0%, transparent 45%),
                #0b1220;
            color: #e2e8f0;
        }

        /* =========================================================
           LEBAR TERPUSAT (card + topbar selalu sama)
           ========================================================= */
        :root {
            --shell: 640px;
        }
        @media (min-width: 768px)  { :root { --shell: 720px; } }
        @media (min-width: 1024px) { :root { --shell: 900px; } }
        @media (min-width: 1440px) { :root { --shell: 1000px; } }

        /* =========================================================
           TOP BAR — BACK BUTTON
           ========================================================= */
        .topbar {
            width: 100%;
            max-width: var(--shell);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeIn .5s .1s ease forwards;
        }
        @keyframes fadeIn { to { opacity: 1; } }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .6rem 1.1rem;
            border-radius: 9999px;
            background: rgba(255,255,255,.9);
            border: 1px solid rgba(148,163,184,.25);
            color: #334155;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 6px 18px -8px rgba(15,23,42,.25);
            backdrop-filter: blur(10px);
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            background: #fff;
            box-shadow: 0 12px 26px -10px rgba(15,23,42,.35);
            color: #0f172a;
        }
        .back-btn i { font-size: .95rem; color: #06b6d4; }

        .dark .back-btn {
            background: rgba(15,23,42,.85);
            border-color: rgba(148,163,184,.2);
            color: #cbd5e1;
        }
        .dark .back-btn:hover { background: rgba(15,23,42,1); color: #f8fafc; }

        .topbar-right {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .78rem;
            color: #64748b;
            font-weight: 600;
        }
        .dark .topbar-right { color: #94a3b8; }

        @media (max-width: 480px) {
            .back-btn .label-text { display: none; }
            .back-btn { padding: .55rem; width: 40px; height: 40px; justify-content: center; }
        }

        /* =========================================================
           CARD
           ========================================================= */
        .profile-card {
            position: relative;
            width: 100%;
            max-width: var(--shell);
            border-radius: 1.75rem;
            overflow: hidden;
            background: #ffffff;
            box-shadow:
                0 1px 2px rgba(15,23,42,.06),
                0 24px 60px -24px rgba(15,23,42,.35);
            opacity: 0;
            transform: translateY(24px);
            animation: cardIn .65s cubic-bezier(.22,.61,.36,1) forwards;
        }
        @keyframes cardIn { to { opacity: 1; transform: translateY(0); } }

        .dark .profile-card {
            background: #0f172a;
            box-shadow:
                0 1px 2px rgba(0,0,0,.4),
                0 28px 70px -24px rgba(0,0,0,.75);
        }

        /* =========================================================
           HEADER (banner)
           ========================================================= */
        .card-header {
            position: relative;
            height: 170px;
            background:
                radial-gradient(circle at 15% 20%, rgba(34,211,238,.55), transparent 55%),
                radial-gradient(circle at 85% 80%, rgba(139,92,246,.55), transparent 55%),
                linear-gradient(135deg, #1e293b, #0f172a 60%, #1e3a8a);
            overflow: hidden;
        }
        @media (min-width: 1024px) { .card-header { height: 190px; } }

        .card-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(circle at 50% 100%, black, transparent 75%);
            -webkit-mask-image: radial-gradient(circle at 50% 100%, black, transparent 75%);
            pointer-events: none;
        }

        /* =========================================================
           AVATAR
           ========================================================= */
        .avatar-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            margin-top: -68px;
            z-index: 3;
        }

        .avatar-ring {
            position: relative;
            width: 136px;
            height: 136px;
            border-radius: 9999px;
            padding: 4px;
            background: conic-gradient(from 180deg, #22d3ee, #8b5cf6, #ec4899, #22d3ee);
            animation: ringSpin 8s linear infinite;
            box-shadow: 0 12px 32px -8px rgba(34,211,238,.55);
        }
        @media (min-width: 1024px) { .avatar-ring { width: 148px; height: 148px; } }
        @keyframes ringSpin { to { transform: rotate(360deg); } }

        .avatar {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 9999px;
            overflow: hidden;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: ringSpinReverse 8s linear infinite;
        }
        @keyframes ringSpinReverse { to { transform: rotate(-360deg); } }

        .avatar-initials {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.6rem;
            font-weight: 800;
            letter-spacing: .04em;
            color: #fff;
            background: linear-gradient(135deg, #06b6d4, #3b82f6 45%, #8b5cf6);
            text-shadow: 0 2px 12px rgba(0,0,0,.3);
        }
        @media (min-width: 1024px) { .avatar-initials { font-size: 2.9rem; } }

        .avatar img {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .verified-mark {
            position: absolute;
            top: 4px; right: 4px;
            width: 26px; height: 26px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 900;
            border: 3px solid #fff;
            z-index: 4;
            box-shadow: 0 4px 12px rgba(6,182,212,.55);
        }
        .dark .verified-mark { border-color: #0f172a; }

        /* =========================================================
           BODY
           ========================================================= */
        .card-body {
            padding: 1.25rem 1.5rem 2rem;
            text-align: center;
        }
        @media (min-width: 1024px) { .card-body { padding: 1.5rem 2.25rem 2.25rem; } }

        .user-name {
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: -.02em;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: .35rem;
            text-transform: capitalize;
        }
        @media (min-width: 1024px) { .user-name { font-size: 1.95rem; } }
        .dark .user-name { color: #f8fafc; }

        .user-meta {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .82rem;
            color: #64748b;
            margin-bottom: .85rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .dark .user-meta { color: #94a3b8; }
        .user-meta i { color: #06b6d4; }

        /* =========================================================
           ROLE BADGE
           ========================================================= */
        .role-row {
            display: flex;
            justify-content: center;
            margin-bottom: 1.1rem;
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem 1.1rem;
            border-radius: 9999px;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #fff;
            box-shadow: 0 10px 26px -10px rgba(0,0,0,.45);
            position: relative;
            overflow: hidden;
        }
        .role-badge::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            animation: shine 3.5s ease-in-out infinite;
        }
        @keyframes shine {
            0%, 100% { transform: translateX(-100%); }
            50%      { transform: translateX(100%); }
        }
        .role-badge .dot {
            width: 7px; height: 7px;
            border-radius: 9999px;
            background: #fff;
            display: inline-block;
            box-shadow: 0 0 8px rgba(255,255,255,.9);
            position: relative; z-index: 1;
        }
        .role-badge span { position: relative; z-index: 1; }

        /* =========================================================
           STATS PILLS
           ========================================================= */
        .stats {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: 1.25rem;
        }

        .stat {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .48rem .95rem;
            border-radius: 9999px;
            background: rgba(148,163,184,.12);
            border: 1px solid rgba(148,163,184,.22);
            font-size: .85rem;
            transition: transform .2s ease, background .2s ease, border-color .2s ease;
        }
        @media (min-width: 1024px) { .stat { padding: .55rem 1.05rem; } }
        .dark .stat {
            background: rgba(148,163,184,.08);
            border-color: rgba(148,163,184,.16);
        }
        .stat:hover {
            transform: translateY(-2px);
            background: rgba(148,163,184,.2);
        }
        .stat .num { font-weight: 800; color: #0f172a; }
        .dark .stat .num { color: #f8fafc; }
        .stat .lbl { color: #64748b; font-size: .78rem; }
        .dark .stat .lbl { color: #94a3b8; }

        .stat.interactive { cursor: pointer; user-select: none; }
        .stat.interactive:hover { border-color: rgba(236,72,153,.4); }
        .stat.interactive.loved {
            background: linear-gradient(135deg, rgba(244,63,94,.15), rgba(236,72,153,.15));
            border-color: rgba(236,72,153,.45);
        }
        .stat.interactive.followed {
            background: linear-gradient(135deg, rgba(59,130,246,.15), rgba(99,102,241,.15));
            border-color: rgba(99,102,241,.45);
        }
        .stat.disabled { opacity: .55; cursor: not-allowed; }

        /* =========================================================
           DIVIDER
           ========================================================= */
        .divider {
            border: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(148,163,184,.35), transparent);
            margin: 1.15rem 0;
        }

        /* =========================================================
           PORTFOLIO — full width row
           ========================================================= */
        .portfolio-row {
            display: flex;
            justify-content: center;
            margin-bottom: 1.15rem;
        }
        .portfolio {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .6rem 1.25rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(99,102,241,.1), rgba(139,92,246,.1));
            border: 1px solid rgba(99,102,241,.25);
            color: #6366f1;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none;
            max-width: 100%;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .portfolio:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, rgba(99,102,241,.18), rgba(139,92,246,.18));
            box-shadow: 0 10px 24px -10px rgba(99,102,241,.6);
        }
        .dark .portfolio { color: #a5b4fc; }
        .portfolio span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* =========================================================
           GRID KONTEN — simetris 3 kolom di desktop
           ========================================================= */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .85rem;
            align-items: stretch;
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            /* Tablet: 2 kolom, bio+skills kiri, social kanan */
            .content-grid { grid-template-columns: 1fr 1fr; }
            .card-bio      { grid-column: 1 / 2; }
            .card-skills   { grid-column: 1 / 2; }
            .card-social   { grid-column: 2 / 3; grid-row: 1 / 3; }
        }

        @media (min-width: 1024px) {
            /* Desktop: 3 kolom simetris */
            .content-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .content-card {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            text-align: left;
            padding: 1.15rem 1.25rem;
            border-radius: 1rem;
            background: rgba(148,163,184,.08);
            border: 1px solid rgba(148,163,184,.16);
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
            min-height: 100%;
        }
        .dark .content-card {
            background: rgba(148,163,184,.05);
            border-color: rgba(148,163,184,.12);
        }
        .content-card:hover {
            transform: translateY(-2px);
            border-color: rgba(148,163,184,.3);
            background: rgba(148,163,184,.12);
        }

        .content-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #06b6d4;
        }
        .content-label i { font-size: .8rem; }

        .content-value {
            font-size: .9rem;
            line-height: 1.55;
            color: #334155;
            word-break: break-word;
            flex: 1;
        }
        .dark .content-value { color: #cbd5e1; }
        .content-empty {
            color: #94a3b8;
            font-style: italic;
            font-size: .88rem;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .content-empty i { color: #94a3b8; font-size: .85rem; }

        /* Skill tag */
        .skill-tag {
            display: inline-block;
            padding: .22rem .75rem;
            margin: .15rem .25rem .15rem 0;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 600;
            background: linear-gradient(135deg, rgba(6,182,212,.15), rgba(139,92,246,.15));
            color: #0891b2;
            border: 1px solid rgba(6,182,212,.28);
        }
        .dark .skill-tag {
            color: #67e8f9;
            border-color: rgba(34,211,238,.35);
        }

        /* Social list — vertikal di dalam card */
        .social-list {
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }
        .social-item {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .5rem .75rem;
            border-radius: .6rem;
            background: rgba(255,255,255,.5);
            border: 1px solid rgba(148,163,184,.18);
            color: #334155;
            text-decoration: none;
            font-size: .84rem;
            font-weight: 600;
            transition: transform .18s ease, background .18s ease, border-color .18s ease;
            overflow: hidden;
        }
        .dark .social-item {
            background: rgba(148,163,184,.05);
            color: #cbd5e1;
            border-color: rgba(148,163,184,.14);
        }
        .social-item:hover {
            transform: translateX(3px);
            background: rgba(6,182,212,.1);
            border-color: rgba(6,182,212,.35);
        }
        .social-item i {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
            color: #06b6d4;
            flex-shrink: 0;
        }
        .social-item .name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* =========================================================
           FOOTER
           ========================================================= */
        .card-footer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(148,163,184,.2);
            font-size: .72rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .4rem;
        }
        .card-footer i { color: #06b6d4; }

        /* =========================================================
           THEME TOGGLE
           ========================================================= */
        .theme-toggle {
            position: fixed;
            top: 1rem; right: 1rem;
            z-index: 100;
            width: 42px; height: 42px;
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 9999px;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(10px);
            color: #1f2937;
            font-size: 1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 22px rgba(15,23,42,.12);
            transition: transform .2s ease, background .2s ease;
        }
        .theme-toggle:hover { transform: scale(1.05); }
        .dark .theme-toggle {
            background: rgba(15,23,42,.85);
            border-color: rgba(148,163,184,.2);
            color: #f8fafc;
            box-shadow: 0 8px 22px rgba(0,0,0,.4);
        }
        @media (max-width: 480px) {
            .theme-toggle { top: .75rem; right: .75rem; width: 38px; height: 38px; }
        }

        /* =========================================================
           TOAST
           ========================================================= */
        .toast {
            position: fixed;
            bottom: 2rem; left: 50%;
            transform: translate(-50%, 20px);
            background: #0f172a;
            color: #f8fafc;
            padding: .7rem 1.4rem;
            border-radius: 9999px;
            font-size: .85rem;
            font-weight: 600;
            box-shadow: 0 20px 40px -12px rgba(0,0,0,.5);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity .28s ease, transform .28s ease;
        }
        .toast.show { opacity: 1; transform: translate(-50%, 0); }
        .dark .toast { background: #f8fafc; color: #0f172a; }

        /* =========================================================
           MOBILE OPTIMIZATION
           ========================================================= */
        @media (max-width: 640px) {
            body { padding: .75rem .75rem 2rem; }
            .card-header { height: 140px; }
            .avatar-wrapper { margin-top: -56px; }
            .avatar-ring { width: 112px; height: 112px; animation: none; }
            .avatar { animation: none; }
            .avatar-initials { font-size: 2rem; }
            .card-body { padding: 1rem 1.1rem 1.5rem; }
            .user-name { font-size: 1.35rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.001ms !important;
                transition-duration: 0.001ms !important;
            }
            .profile-card { opacity: 1; transform: none; }
            .topbar { opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Theme toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Ganti tema">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <!-- Toast -->
    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    @php
        /* =========================================================
           AVATAR: handle absolute URL & storage path
           ========================================================= */
         $rawAvatar = trim($user->avatar_url ?? '');
    $avatarSrc = null;

    if ($rawAvatar !== '') {
        // 1. URL absolut (CDN / http / data-uri) → pakai langsung
        if (str_starts_with($rawAvatar, 'http://')
            || str_starts_with($rawAvatar, 'https://')
            || str_starts_with($rawAvatar, 'data:')) {
            $avatarSrc = $rawAvatar;
        }
        // 2. Sudah pakai prefix "storage/..." → jangan dobel
        elseif (str_starts_with($rawAvatar, 'storage/')) {
            $avatarSrc = asset($rawAvatar);
        }
        // 3. Absolute path dari root ("/...") → langsung asset
        elseif (str_starts_with($rawAvatar, '/')) {
            $avatarSrc = asset(ltrim($rawAvatar, '/'));
        }
        // 4. Path relatif dari disk 'public' (paling umum: "avatars/xxx.jpg" / "xxx.jpg")
        else {
            $avatarSrc = asset('storage/' . ltrim($rawAvatar, '/'));
        }
    }

    // Fallback kalau kosong → pakai gif default
    if (!$avatarSrc) {
        $avatarSrc = asset('images/gif.gif');
    }

        /* Inisial */
        $nameParts = preg_split('/\s+/', trim($user->name));
        $initials  = '';
        foreach (array_slice($nameParts, 0, 2) as $p) {
            $initials .= mb_strtoupper(mb_substr($p, 0, 1));
        }
        $initials = $initials ?: '?';

        /* Role Map */
        $roleMap = [
            1 => ['label' => 'Owner',        'grad' => 'linear-gradient(135deg,#f59e0b,#dc2626)'],
            2 => ['label' => 'Super Admin',  'grad' => 'linear-gradient(135deg,#f43f5e,#f97316)'],
            3 => ['label' => 'Ketua',        'grad' => 'linear-gradient(135deg,#fbbf24,#f59e0b)'],
            4 => ['label' => 'Pengurus',     'grad' => 'linear-gradient(135deg,#8b5cf6,#d946ef)'],
            5 => ['label' => 'Anggota UKM',  'grad' => 'linear-gradient(135deg,#06b6d4,#3b82f6)'],
            6 => ['label' => 'Anggota Umum', 'grad' => 'linear-gradient(135deg,#64748b,#334155)'],
        ];

        $levelId = (int) ($user->level_id ?? 0);
        $role    = $roleMap[$levelId] ?? ['label' => 'Guest', 'grad' => 'linear-gradient(135deg,#94a3b8,#475569)'];

        /* Statistik love & follow */
        $isOwnProfile    = Auth::check() && Auth::id() == $user->id;
        $isLoggedIn      = Auth::check();
        $userLoveCount   = $user->loves_count ?? 0;
        $userFollowCount = $user->followers_count ?? 0;
        $hasLoved        = $user->hasLoved ?? false;
        $hasFollowed     = $user->hasFollowed ?? false;

        /* Skills */
        $skills = is_array($user->skills)
            ? $user->skills
            : array_filter(array_map('trim', explode(',', $user->skills ?? '')));

        $socials = is_array($user->social_media) ? $user->social_media : [];
    @endphp

    {{-- TOPBAR --}}
    <div class="topbar">
        <a href="{{ route('explore') }}" class="back-btn" aria-label="Kembali ke Explore">
            <i class="fas fa-arrow-left"></i>
            <span class="label-text">Kembali ke Explore</span>
        </a>

        <span class="topbar-right">
            <i class="fas fa-user-circle"></i> Profile
        </span>
    </div>

    <!-- CARD -->
    <div class="profile-card">

        <!-- Header -->
        <div class="card-header"></div>

        <!-- Avatar -->
        <div class="avatar-wrapper">
            <div class="avatar-ring">
                <div class="avatar">
                    <span class="avatar-initials">{{ $initials }}</span>
                    @if($avatarSrc)
                        <img
                            src="{{ $avatarSrc }}"
                            alt="Avatar {{ $user->name }}"
                            loading="lazy"
                            decoding="async"
                            onerror="this.remove()"
                        >
                    @endif
                </div>
                @if($user->is_verified ?? false)
                    <span class="verified-mark" title="Terverifikasi">✓</span>
                @endif
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">

            <h1 class="user-name">{{ $user->name }}</h1>

            @if($user->nim || $user->username)
                <p class="user-meta">
                    <i class="fas fa-id-badge"></i>
                    {{ $user->nim ? 'NIM ' . $user->nim : '' }}
                    @if($user->nim && $user->username) · @endif
                    {{ $user->username ? '@' . $user->username : '' }}
                </p>
            @endif

            {{-- Role Badge --}}
            <div class="role-row">
                <span class="role-badge" style="background: {{ $role['grad'] }};">
                    <span class="dot"></span>
                    <span>{{ $role['label'] }}</span>
                </span>
            </div>

            <!-- Stats -->
            <div class="stats">
                <div class="stat">
                    <i class="fas fa-bolt" style="color:#facc15"></i>
                    <span class="num">{{ number_format($user->total_points ?? 0, 0, ',', '.') }}</span>
                    <span class="lbl">poin</span>
                </div>

                @if($user->rank)
                    <div class="stat" style="background: linear-gradient(135deg, rgba(99,102,241,.15), rgba(139,92,246,.15)); border-color: rgba(139,92,246,.35); color:#6366f1; font-weight:700;">
                        <i class="fas fa-medal"></i>
                        <span>{{ $user->rank }}</span>
                    </div>
                @endif

                <div
                    class="stat interactive {{ $hasLoved ? 'loved' : '' }} {{ $isOwnProfile ? 'disabled' : '' }}"
                    id="love-btn"
                    data-user-id="{{ $user->id }}"
                    data-logged-in="{{ $isLoggedIn ? '1' : '0' }}"
                    data-own-profile="{{ $isOwnProfile ? '1' : '0' }}"
                    role="button" tabindex="0" aria-label="Love"
                >
                    <i class="fas fa-heart" style="color:#f43f5e"></i>
                    <span class="num" id="love-count">{{ $userLoveCount }}</span>
                    <span class="lbl">Love</span>
                </div>

                <div
                    class="stat interactive {{ $hasFollowed ? 'followed' : '' }} {{ $isOwnProfile ? 'disabled' : '' }}"
                    id="follow-btn"
                    data-user-id="{{ $user->id }}"
                    data-logged-in="{{ $isLoggedIn ? '1' : '0' }}"
                    data-own-profile="{{ $isOwnProfile ? '1' : '0' }}"
                    role="button" tabindex="0" aria-label="Ikuti"
                >
                    <i class="fas fa-user-plus" style="color:#6366f1"></i>
                    <span class="num" id="follow-count">{{ $userFollowCount }}</span>
                    <span class="lbl">Ikuti</span>
                </div>
            </div>

            {{-- Portfolio — full width, pusat --}}
            @if($user->portfolio_url)
                <div class="portfolio-row">
                    <a href="{{ $user->portfolio_url }}" target="_blank" rel="noopener noreferrer" class="portfolio">
                        <i class="fas fa-briefcase"></i>
                        <span>{{ Str::limit(str_replace(['https://', 'http://'], '', $user->portfolio_url), 45) }}</span>
                    </a>
                </div>
            @endif

            <hr class="divider">

            {{-- GRID KONTEN — 3 kolom simetris di desktop --}}
            <div class="content-grid">

                {{-- Bio --}}
                <div class="content-card card-bio">
                    <div class="content-label">
                        <i class="fas fa-quote-left"></i> Bio
                    </div>
                    <div class="content-value">
                        @if($user->bio)
                            {{ $user->bio }}
                        @else
                            <span class="content-empty">
                                <i class="fas fa-minus-circle"></i> Belum ada bio.
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Keahlian --}}
                <div class="content-card card-skills">
                    <div class="content-label">
                        <i class="fas fa-code"></i> Keahlian
                    </div>
                    <div class="content-value">
                        @if(count($skills))
                            @foreach($skills as $skill)
                                <span class="skill-tag">{{ $skill }}</span>
                            @endforeach
                        @else
                            <span class="content-empty">
                                <i class="fas fa-minus-circle"></i> Belum diisi.
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="content-card card-social">
                    <div class="content-label">
                        <i class="fas fa-share-nodes"></i> Sosial Media
                    </div>
                    <div class="content-value">
                        @if(!empty($socials))
                            <div class="social-list">
                                @foreach($socials as $soc)
                                    @php
                                        $platform = $soc['platform'] ?? 'link';
                                        $url      = $soc['url'] ?? '#';
                                        $icon = match($platform) {
                                            'instagram'    => 'fab fa-instagram',
                                            'facebook'     => 'fab fa-facebook-f',
                                            'telegram'     => 'fab fa-telegram-plane',
                                            'x', 'twitter' => 'fab fa-x-twitter',
                                            'tiktok'       => 'fab fa-tiktok',
                                            'youtube'      => 'fab fa-youtube',
                                            'linkedin'     => 'fab fa-linkedin-in',
                                            default        => 'fas fa-link',
                                        };
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="social-item">
                                        <i class="{{ $icon }}"></i>
                                        <span class="name">{{ ucfirst($platform) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="content-empty">
                                <i class="fas fa-minus-circle"></i> Belum ada sosial media.
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer">
                <i class="fas fa-code"></i>
                <span>UNICODE — Dibangun dengan baris kode</span>
            </div>

        </div>
    </div>

    <script>
    (function () {
        'use strict';

        /* 1. DARK / LIGHT TOGGLE */
        const html      = document.documentElement;
        const toggle    = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        const applyTheme = (mode) => {
            if (mode === 'dark') {
                html.classList.add('dark');
                themeIcon.className = 'fas fa-sun';
            } else {
                html.classList.remove('dark');
                themeIcon.className = 'fas fa-moon';
            }
        };

        applyTheme(localStorage.getItem('theme') || 'light');

        toggle?.addEventListener('click', () => {
            const next = html.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next);
        });

        /* 2. TOAST */
        const toastEl = document.getElementById('toast');
        let toastTimer;
        function showToast(message, duration = 2800) {
            toastEl.textContent = message;
            toastEl.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toastEl.classList.remove('show'), duration);
        }

        /* 3. LOVE & FOLLOW */
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        async function toggleAction(btn, countEl, type) {
            const isLoggedIn   = btn.dataset.loggedIn === '1';
            const isOwnProfile = btn.dataset.ownProfile === '1';
            const userId       = btn.dataset.userId;

            if (!isLoggedIn)   return showToast('Silakan login terlebih dahulu.');
            if (isOwnProfile)  return showToast('Tidak bisa ' + type + ' diri sendiri.');

            const endpoint = `/profile/${type}/${userId}`;
            const wasActive = btn.classList.contains('loved') || btn.classList.contains('followed');
            const currentCount = parseInt(countEl.textContent, 10) || 0;

            const activeClass = type === 'love' ? 'loved' : 'followed';
            if (wasActive) {
                btn.classList.remove(activeClass);
                countEl.textContent = Math.max(0, currentCount - 1);
            } else {
                btn.classList.add(activeClass);
                countEl.textContent = currentCount + 1;
            }

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: '{}',
                });
                const data = await res.json();

                if (data.success) {
                    countEl.textContent = data.count;
                    const isActive = type === 'love' ? data.isLoved : data.isFollowed;
                    btn.classList.toggle(activeClass, isActive);
                } else {
                    btn.classList.toggle(activeClass, wasActive);
                    countEl.textContent = currentCount;
                    showToast('Gagal, coba lagi.');
                }
            } catch (err) {
                btn.classList.toggle(activeClass, wasActive);
                countEl.textContent = currentCount;
                showToast('Koneksi bermasalah.');
            }
        }

        const loveBtn     = document.getElementById('love-btn');
        const followBtn   = document.getElementById('follow-btn');
        const loveCount   = document.getElementById('love-count');
        const followCount = document.getElementById('follow-count');

        const bindClick = (btn, countEl, type) => {
            if (!btn) return;
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                if (btn.classList.contains('disabled')) return;
                toggleAction(btn, countEl, type);
            });
            btn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    if (btn.classList.contains('disabled')) return;
                    toggleAction(btn, countEl, type);
                }
            });
        };

        bindClick(loveBtn, loveCount, 'love');
        bindClick(followBtn, followCount, 'follow');

    })();
    </script>

</body>
</html>