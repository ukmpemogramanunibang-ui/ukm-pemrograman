@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="Informasi ruangan publik UNICODE – cek ketersediaan, jadwal, dan booking ruangan secara realtime.">
    <title>Room Publik – UNICODE</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        /* ============================================================
           DESIGN TOKENS
        ============================================================ */
        :root {
            --bg-body:        #f1f5f9;
            --bg-card:        #ffffff;
            --bg-soft:        #f1f5f9;
            --bg-hover:       #e2e8f0;
            --bg-elevated:    #ffffff;
            --text-primary:   #0f172a;
            --text-secondary: #64748b;
            --text-tertiary:  #94a3b8;
            --border-color:   #e2e8f0;
            --border-strong:  #cbd5e1;
            --header-bg:      rgba(255,255,255,.94);
            --primary:        #2563eb;
            --primary-dark:   #1d4ed8;
            --cyan:           #06b6d4;
            --success:        #10b981;
            --warning:        #f59e0b;
            --danger:         #ef4444;
            --indigo:         #4f46e5;
            --green:          #16a34a;
            --shadow-xs:      0 1px 2px rgba(15,23,42,.05);
            --shadow-sm:      0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
            --shadow-md:      0 10px 30px rgba(15,23,42,.08);
            --shadow-lg:      0 20px 60px rgba(15,23,42,.12);
            --shadow-xl:      0 40px 90px rgba(15,23,42,.20);
            --sidebar-w:      280px;
            --radius-sm:      8px;
            --radius-md:      12px;
            --radius-lg:      16px;
            --radius-xl:      22px;
            --easing:         cubic-bezier(.4, 0, .2, 1);
            --easing-bounce:  cubic-bezier(.34, 1.56, .64, 1);
            --dur-fast:       .15s;
            --dur-base:       .25s;
            --dur-slow:       .35s;
        }

        .dark {
            --bg-body:        #0f172a;
            --bg-card:        #1e293b;
            --bg-soft:        #273449;
            --bg-hover:       #334155;
            --bg-elevated:    #1e293b;
            --text-primary:   #f8fafc;
            --text-secondary: #94a3b8;
            --text-tertiary:  #64748b;
            --border-color:   #334155;
            --border-strong:  #475569;
            --header-bg:      rgba(15,23,42,.94);
            --shadow-xs:      0 1px 2px rgba(0,0,0,.3);
            --shadow-sm:      0 1px 3px rgba(0,0,0,.25);
            --shadow-md:      0 10px 30px rgba(0,0,0,.25);
            --shadow-lg:      0 20px 60px rgba(0,0,0,.35);
            --shadow-xl:      0 40px 90px rgba(0,0,0,.45);
        }

        /* ============================================================
           RESET
        ============================================================ */
        *, *::before, *::after {
            box-sizing: border-box;
            transition:
                background-color var(--dur-base) var(--easing),
                border-color var(--dur-base) var(--easing),
                color var(--dur-base) var(--easing),
                box-shadow var(--dur-base) var(--easing);
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-primary);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        body.no-scroll { overflow: hidden; }

        button, input, select, textarea { font: inherit; color: inherit; }
        button { cursor: pointer; background: none; border: none; padding: 0; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; height: auto; display: block; }

        ::selection {
            background: rgba(37, 99, 235, .2);
            color: var(--text-primary);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--border-strong);
            border-radius: 99px;
            border: 2px solid var(--bg-body);
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-tertiary); }

        /* ============================================================
           APP SHELL
        ============================================================ */
        .app-shell { min-height: 100vh; }

        /* ============================================================
           SIDEBAR
        ============================================================ */
        .app-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            z-index: 900;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transform: translateX(0);
            transition: transform var(--dur-slow) var(--easing);
            box-shadow: 4px 0 24px rgba(0,0,0,.04);
            will-change: transform;
        }
        body.sidebar-closed .app-sidebar { transform: translateX(-100%); }

        .sidebar-header {
            flex-shrink: 0;
            padding: 1rem 1rem .85rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: .65rem;
            min-width: 0;
        }
        .sidebar-brand-icon {
            width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #06b6d4, #2563eb, #4f46e5);
            color: #fff; font-size: 1rem;
            box-shadow: 0 6px 18px rgba(37,99,235,.25);
        }
        .sidebar-brand-text { min-width: 0; }
        .sidebar-brand-title {
            margin: 0; font-size: .92rem; font-weight: 900;
            color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-brand-sub {
            margin: .1rem 0 0; font-size: .65rem;
            color: var(--text-secondary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-close {
            width: 34px; height: 34px; border-radius: 50%;
            border: 1px solid var(--border-color);
            background: var(--bg-soft);
            color: var(--text-primary);
            display: flex; align-items: center; justify-content: center;
            transition: background var(--dur-fast), transform var(--dur-fast);
        }
        .sidebar-close:hover { background: var(--bg-hover); }
        .sidebar-close:active { transform: scale(.92); }

        .sidebar-body {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: .75rem .65rem;
            overscroll-behavior: contain;
        }
        .sidebar-body::-webkit-scrollbar { width: 6px; }
        .sidebar-body::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 99px;
            border: none;
        }

        .sidebar-section-title {
            padding: .5rem .65rem .35rem;
            font-size: .62rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: var(--text-secondary);
        }

        /* Sidebar Item */
        .sidebar-item {
            display: flex; align-items: center; gap: .65rem;
            width: 100%; padding: .55rem .65rem;
            border-radius: 10px;
            color: var(--text-primary);
            font-size: .82rem; font-weight: 600;
            text-align: left; min-width: 0;
            transition: background var(--dur-fast), color var(--dur-fast);
        }
        .sidebar-item:hover { background: var(--bg-soft); }
        .sidebar-item.active {
            background: #dbeafe;
            color: var(--primary-dark);
            font-weight: 800;
        }
        .dark .sidebar-item.active { background: #172554; color: #93c5fd; }
        .sidebar-item i.leading {
            width: 16px; text-align: center; flex-shrink: 0;
            color: var(--text-secondary);
        }
        .sidebar-item.active i.leading { color: inherit; }
        .sidebar-item .label {
            flex: 1;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .sidebar-item .count {
            padding: 0 .45rem; min-width: 22px; text-align: center;
            border-radius: 99px;
            background: var(--bg-soft);
            font-size: .65rem; font-weight: 800;
            color: var(--text-secondary);
        }
        .sidebar-item.active .count {
            background: rgba(29,78,216,.15);
            color: var(--primary-dark);
        }
        .dark .sidebar-item.active .count {
            background: rgba(147,197,253,.2);
            color: #93c5fd;
        }

        /* Floor Group */
        .floor-group { margin-bottom: .15rem; }
        .floor-header {
            display: flex; align-items: center; gap: .55rem;
            width: 100%; padding: .55rem .65rem;
            border-radius: 10px;
            color: var(--text-primary);
            font-size: .82rem; font-weight: 800;
            text-align: left;
            transition: background var(--dur-fast);
        }
        .floor-header:hover { background: var(--bg-soft); }
        .floor-header .chev {
            width: 14px; font-size: .7rem;
            color: var(--text-secondary);
            transition: transform var(--dur-base) var(--easing);
        }
        .floor-group.open .floor-header .chev { transform: rotate(90deg); }
        .floor-header .label {
            flex: 1;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .floor-header .count {
            padding: 0 .45rem; min-width: 22px; text-align: center;
            border-radius: 99px;
            background: var(--bg-soft);
            font-size: .65rem; font-weight: 800;
            color: var(--text-secondary);
        }
        .floor-rooms {
            display: grid; gap: .1rem;
            max-height: 0;
            overflow: hidden;
            padding-left: .85rem;
            border-left: 2px solid var(--border-color);
            margin-left: 1rem;
            transition:
                max-height var(--dur-slow) var(--easing),
                padding var(--dur-slow) var(--easing);
        }
        .floor-group.open .floor-rooms {
            max-height: 2000px;
            padding-top: .2rem;
            padding-bottom: .3rem;
        }
        .room-link {
            display: flex; align-items: center; gap: .55rem;
            padding: .38rem .6rem;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            color: var(--text-primary);
            min-width: 0;
            transition: background var(--dur-fast);
        }
        .room-link:hover { background: var(--bg-soft); }
        .room-link.active {
            background: #dbeafe;
            color: var(--primary-dark);
            font-weight: 800;
        }
        .dark .room-link.active { background: #172554; color: #93c5fd; }
        .room-link i {
            font-size: .68rem;
            color: var(--text-secondary);
            flex-shrink: 0;
        }
        .room-link.active i { color: inherit; }
        .room-link .label {
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .sidebar-empty {
            padding: 1.5rem 1rem; text-align: center;
            color: var(--text-secondary); font-size: .78rem;
        }
        .sidebar-empty i {
            display: block; font-size: 1.8rem;
            margin-bottom: .5rem; opacity: .5;
        }

        .sidebar-footer {
            flex-shrink: 0;
            padding: .7rem .85rem;
            border-top: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            font-size: .68rem;
            color: var(--text-secondary);
            gap: .5rem;
        }
        .kbd {
            display: inline-flex; align-items: center; gap: .15rem;
            padding: .12rem .4rem;
            border-radius: 6px;
            background: var(--bg-soft);
            border: 1px solid var(--border-color);
            font-family: ui-monospace, SFMono-Regular, monospace;
            font-size: .62rem; font-weight: 800;
        }

        /* Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed; inset: 0; z-index: 850;
            background: rgba(2,6,23,.55);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0; pointer-events: none;
            transition: opacity var(--dur-base) var(--easing);
        }

        /* ============================================================
           APP BODY
        ============================================================ */
        .app-body {
            margin-left: var(--sidebar-w);
            transition: margin-left var(--dur-slow) var(--easing);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        body.sidebar-closed .app-body { margin-left: 0; }

        /* ============================================================
           HEADER
        ============================================================ */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 500;
            background: var(--header-bg);
            backdrop-filter: saturate(180%) blur(14px);
            -webkit-backdrop-filter: saturate(180%) blur(14px);
            border-bottom: 1px solid var(--border-color);
            padding: .7rem 1.1rem;
            transition: padding var(--dur-base) var(--easing);
        }
        .header-inner {
            max-width: 1800px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
        }
        .header-left {
            display: flex; align-items: center; gap: .65rem;
            min-width: 0;
        }
        .header-toggle {
            width: 42px; height: 42px; flex-shrink: 0;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--bg-card);
            color: var(--text-primary);
            display: flex; align-items: center; justify-content: center;
            box-shadow: var(--shadow-sm);
            transition: transform var(--dur-fast), box-shadow var(--dur-fast), background var(--dur-fast);
        }
        .header-toggle:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            background: var(--bg-soft);
        }
        .header-toggle:active { transform: scale(.94); }

        .brand {
            display: flex; align-items: center; gap: .65rem;
            min-width: 0;
        }
        .brand-logo {
            width: 38px; height: 38px; border-radius: var(--radius-md); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #06b6d4, #2563eb, #4f46e5);
            color: #fff; font-size: 1rem;
            box-shadow: 0 6px 18px rgba(37,99,235,.25);
        }
        .brand-title {
            margin: 0; font-weight: 900; font-size: 1rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .brand-subtitle {
            margin: .05rem 0 0;
            font-size: .68rem;
            color: var(--text-secondary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .header-actions {
            display: flex; align-items: center; gap: .5rem;
        }
        .header-btn {
            min-height: 40px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--bg-card);
            color: var(--text-primary);
            padding: .5rem .85rem;
            display: inline-flex; align-items: center; justify-content: center;
            gap: .4rem;
            box-shadow: var(--shadow-sm);
            transition: transform var(--dur-fast), box-shadow var(--dur-fast), background var(--dur-fast);
        }
        .header-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        .header-btn:active { transform: scale(.94); }
        .header-btn.icon-only {
            width: 40px; padding: 0;
            border-radius: 50%;
        }
        .header-btn.primary {
            background: #0f172a;
            border-color: #0f172a;
            color: #fff;
        }
        .dark .header-btn.primary {
            background: #38bdf8;
            border-color: #38bdf8;
            color: #082f49;
        }

        /* ============================================================
           MAIN CONTENT
        ============================================================ */
        .main-content {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
            padding: 1.25rem;
            flex: 1;
        }

        /* ============================================================
           WELCOME CARD
        ============================================================ */
        .welcome-card {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-xl);
            padding: 1.2rem 1.35rem;
            margin-bottom: 1.2rem;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 30%),
                linear-gradient(135deg, #06b6d4, #2563eb 55%, #4f46e5);
            box-shadow: 0 18px 45px rgba(37,99,235,.22);
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
            animation: fadeSlideIn .6s var(--easing) both;
        }
        .welcome-card::after {
            content: "";
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            right: -80px; top: -100px;
            background: rgba(255,255,255,.08);
            pointer-events: none;
        }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .welcome-left {
            display: flex; align-items: center; gap: .85rem;
            flex: 1; min-width: 220px;
            position: relative; z-index: 2;
        }
        .welcome-icon {
            width: 56px; height: 56px; flex-shrink: 0;
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.22);
            font-size: 1.5rem;
            backdrop-filter: blur(6px);
        }
        .welcome-text h2 {
            margin: 0;
            font-size: clamp(1.05rem, 2vw, 1.4rem);
            font-weight: 950;
            line-height: 1.15;
        }
        .welcome-text p {
            margin: .25rem 0 0;
            color: rgba(255,255,255,.82);
            font-size: .8rem;
            line-height: 1.5;
            max-width: 60ch;
        }
        .welcome-actions {
            display: flex; align-items: center; gap: .55rem; flex-wrap: wrap;
            position: relative; z-index: 2;
        }

        .sidebar-toggle-hero {
            min-height: 48px;
            padding: .65rem 1.05rem;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 14px;
            background: rgba(255,255,255,.14);
            color: #fff;
            display: inline-flex; align-items: center; gap: .55rem;
            font-weight: 800; font-size: .85rem;
            backdrop-filter: blur(6px);
            transition: background var(--dur-base), transform var(--dur-fast);
        }
        .sidebar-toggle-hero:hover { background: rgba(255,255,255,.24); transform: translateY(-1px); }
        .sidebar-toggle-hero:active { transform: scale(.97); }
        .sidebar-toggle-hero i { font-size: 1rem; }
        .sidebar-toggle-hero .kbd-inline {
            padding: .1rem .35rem;
            border-radius: 6px;
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.25);
            font-family: ui-monospace, monospace;
            font-size: .6rem; font-weight: 800;
            letter-spacing: .05em;
        }

        .nim-inline {
            display: flex; gap: .4rem;
            min-width: 220px; flex: 1;
            max-width: 360px;
        }
        .nim-inline .nim-input-wrap { position: relative; flex: 1; }
        .nim-inline .nim-input-wrap i {
            position: absolute;
            left: .8rem; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.6);
            font-size: .85rem;
            pointer-events: none;
        }
        .nim-inline input {
            width: 100%; min-height: 48px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.22);
            padding: .65rem 1rem .65rem 2.4rem;
            background: rgba(255,255,255,.12);
            color: #fff;
            outline: none;
            font-size: .85rem;
            transition: background var(--dur-fast), border-color var(--dur-fast);
        }
        .nim-inline input::placeholder { color: rgba(255,255,255,.55); }
        .nim-inline input:focus {
            border-color: rgba(255,255,255,.7);
            background: rgba(255,255,255,.18);
        }
        .nim-inline button {
            min-height: 48px;
            border-radius: 14px;
            padding: .65rem 1rem;
            background: #fff;
            color: #0f172a;
            font-weight: 800;
            font-size: .85rem;
            transition: transform var(--dur-fast), box-shadow var(--dur-fast);
        }
        .nim-inline button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0,0,0,.15);
        }
        .nim-inline button:active { transform: scale(.96); }

        #nimMessage {
            width: 100%;
            margin: .35rem 0 0;
            padding: 0 1.35rem;
            font-size: .78rem;
            color: rgba(255,255,255,.85);
            display: none;
            position: relative; z-index: 2;
        }

        /* ============================================================
           QR FOCUS BANNER
        ============================================================ */
        .qr-focus {
            display: none;
            margin-bottom: 1.2rem;
            padding: .85rem 1rem;
            border-radius: var(--radius-lg);
            border: 1px solid #bfdbfe;
            background: linear-gradient(135deg, rgba(239,246,255,.96), rgba(236,254,255,.96));
            box-shadow: var(--shadow-sm);
            animation: fadeSlideIn .4s var(--easing) both;
        }
        .dark .qr-focus {
            border-color: #164e63;
            background: linear-gradient(135deg, rgba(15,23,42,.92), rgba(8,47,73,.92));
        }
        .qr-focus.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .qr-focus-left { display: flex; align-items: center; gap: .75rem; }
        .qr-focus-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            background: #dbeafe; color: var(--primary);
            flex-shrink: 0;
        }
        .dark .qr-focus-icon { background: #082f49; color: #67e8f9; }
        .qr-focus-title {
            margin: 0;
            font-size: .72rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 800;
        }
        .qr-focus-room {
            margin: .1rem 0 0;
            font-weight: 900;
            font-size: .9rem;
        }
        .qr-focus-close {
            border: 0; background: transparent;
            color: var(--text-secondary);
            font-size: 1rem;
            padding: .35rem;
            border-radius: 50%;
            transition: background var(--dur-fast);
        }
        .qr-focus-close:hover { background: rgba(0,0,0,.06); }

        /* ============================================================
           FILTER (Collapsible)
        ============================================================ */
        .filter-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 0;
            margin-bottom: 1.2rem;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: box-shadow var(--dur-base);
        }
        .filter-section:hover { box-shadow: var(--shadow-md); }

        .filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .85rem 1rem;
            cursor: pointer;
            user-select: none;
            transition: background var(--dur-fast);
        }
        .filter-header:hover { background: var(--bg-soft); }
        .filter-header-left {
            display: flex; align-items: center; gap: .55rem;
            font-size: .88rem; font-weight: 900;
        }
        .filter-header-left i.filter-icon { color: var(--cyan); }
        .filter-badge {
            display: none;
            padding: .08rem .45rem;
            border-radius: 999px;
            background: #dbeafe;
            color: var(--primary-dark);
            font-size: .62rem; font-weight: 900;
        }
        .dark .filter-badge { background: #172554; color: #93c5fd; }
        .filter-badge.show { display: inline-flex; }
        .filter-header-right {
            display: flex; align-items: center; gap: .55rem;
        }
        .filter-chevron {
            width: 28px; height: 28px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .75rem;
            transition: transform var(--dur-slow) var(--easing), background var(--dur-fast);
        }
        .filter-section.open .filter-chevron { transform: rotate(180deg); }
        .filter-reset {
            color: var(--text-secondary);
            font-size: .72rem; font-weight: 700;
            padding: .3rem .55rem;
            border-radius: var(--radius-sm);
            transition: background var(--dur-fast), color var(--dur-fast);
        }
        .filter-reset:hover {
            color: var(--primary);
            background: var(--bg-soft);
        }

        .filter-body {
            max-height: 0;
            overflow: hidden;
            padding: 0 1rem;
            opacity: 0;
            transition:
                max-height var(--dur-slow) var(--easing),
                padding var(--dur-slow) var(--easing),
                opacity var(--dur-base) var(--easing);
        }
        .filter-section.open .filter-body {
            max-height: 900px;
            padding: 0 1rem 1rem;
            opacity: 1;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .55rem;
        }
        .filter-form select,
        .filter-form .checkbox-group {
            min-height: 40px;
            width: 100%;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--bg-soft);
            color: var(--text-primary);
            padding: .55rem .7rem;
            outline: none;
            font-size: .82rem;
        }
        .filter-form select:focus,
        .filter-form .checkbox-group:focus-within {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(6,182,212,.15);
        }
        .filter-form .checkbox-group {
            display: flex; align-items: center; gap: .45rem;
            cursor: pointer;
        }
        .filter-form input[type="checkbox"] { accent-color: var(--primary); }
        .btn-filter {
            min-height: 40px;
            border: 0;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #fff;
            font-weight: 800;
            font-size: .82rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            transition: transform var(--dur-fast), box-shadow var(--dur-fast);
        }
        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37,99,235,.25);
        }
        .btn-filter:active { transform: scale(.98); }

        /* ============================================================
           ROOMS HEADING
        ============================================================ */
        .rooms-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: .85rem;
        }
        .rooms-title { margin: 0; font-size: 1.25rem; font-weight: 950; }
        .rooms-subtitle {
            margin: .15rem 0 0;
            color: var(--text-secondary);
            font-size: .78rem;
        }
        .realtime-indicator {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .38rem .65rem;
            border-radius: 999px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: .68rem; font-weight: 700;
            box-shadow: var(--shadow-sm);
        }
        .realtime-indicator-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 4px rgba(16,185,129,.12);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%      { opacity: .5; }
        }

        /* ============================================================
           ROOM GRID
        ============================================================ */
        .room-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .9rem;
        }

        .room-card {
            position: relative;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            scroll-margin-top: 90px;
            animation: fadeSlideIn .5s var(--easing) both;
            transition: transform var(--dur-base), box-shadow var(--dur-base);
        }
        .room-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .room-card.qr-focused {
            border-color: var(--cyan);
            box-shadow:
                0 0 0 2px rgba(6,182,212,.15),
                0 20px 55px rgba(6,182,212,.15);
        }
        .room-card.qr-focused::before {
            content: "RUANGAN QR";
            position: absolute;
            right: .65rem; top: 3.05rem;
            z-index: 10;
            padding: .28rem .5rem;
            border-radius: 999px;
            font-size: .55rem; font-weight: 900;
            letter-spacing: .07em;
            color: #0f172a;
            background: #67e8f9;
            box-shadow: 0 4px 12px rgba(6,182,212,.3);
        }

        .room-card-header {
            padding: .75rem .9rem;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            gap: .5rem;
        }
        .room-card-header.status-inuse { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .room-card-header.status-booked { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .room-card-header.status-other { background: linear-gradient(135deg, #64748b, #94a3b8); }
        .room-code-badge {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 900;
            font-size: .9rem;
            letter-spacing: .04em;
        }
        .room-status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.9);
            box-shadow: 0 0 0 3px rgba(255,255,255,.25);
            animation: pulseDot 1.8s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .6; transform: scale(.82); }
        }

        .card-image {
            position: relative;
            height: 160px;
            overflow: hidden;
            background: var(--bg-soft);
        }
        .card-image img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform var(--dur-slow);
        }
        .room-card:hover .card-image img { transform: scale(1.04); }
        .placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary);
            font-size: 2.6rem;
        }

        .card-body {
            padding: .9rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .room-title {
            margin: 0;
            font-size: .95rem;
            font-weight: 950;
            line-height: 1.25;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .room-category {
            display: inline-flex; align-items: center;
            margin-top: .3rem;
            padding: .2rem .55rem;
            border-radius: 999px;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .56rem; font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            align-self: flex-start;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .45rem;
            margin-top: .75rem;
        }
        .info-item {
            background: var(--bg-soft);
            border-radius: 10px;
            padding: .45rem .55rem;
            min-width: 0;
        }
        .info-label {
            color: var(--text-secondary);
            font-size: .53rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .info-value {
            margin-top: .1rem;
            color: var(--text-primary);
            font-size: .74rem;
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .facilities {
            display: flex;
            flex-wrap: wrap;
            gap: .28rem;
            margin-top: .7rem;
        }
        .facility-tag {
            padding: .2rem .5rem;
            border-radius: 999px;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .56rem; font-weight: 700;
        }

        .booking-info {
            margin-top: .7rem;
            border-radius: 10px;
            padding: .5rem .65rem;
            font-size: .64rem;
            font-weight: 800;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .booking-info.has-booking { background: #dbeafe; color: #1d4ed8; }
        .dark .booking-info.has-booking { background: #172554; color: #93c5fd; }
        .booking-info.no-booking { background: #d1fae5; color: #047857; }
        .dark .booking-info.no-booking { background: #052e1b; color: #86efac; }

        .card-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .45rem;
            margin-top: auto;
            padding-top: .8rem;
        }
        .btn-action {
            min-height: 36px;
            border: 0;
            border-radius: 10px;
            font-size: .7rem; font-weight: 900;
            display: inline-flex; align-items: center; justify-content: center;
            gap: .35rem;
            cursor: pointer;
            transition: transform var(--dur-fast), background var(--dur-fast);
        }
        .btn-action:active { transform: scale(.96); }
        .btn-action.detail { background: var(--primary); color: #fff; }
        .btn-action.detail:hover { background: var(--primary-dark); }
        .btn-action.book { background: var(--green); color: #fff; }
        .btn-action.book:hover { background: #15803d; }

        /* Empty state */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 1rem;
        }
        .empty-icon {
            width: 90px; height: 90px;
            margin: 0 auto 1rem;
            border-radius: 28px;
            background: var(--bg-soft);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary);
            font-size: 2.4rem;
        }
        .empty-title { margin: 0; font-size: 1rem; font-weight: 900; }
        .empty-description {
            margin: .35rem 0 0;
            color: var(--text-secondary);
            font-size: .78rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1.4rem;
        }

        /* ============================================================
           MODAL
        ============================================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            padding: 1rem;
            align-items: center;
            justify-content: center;
            background: rgba(2,6,23,.58);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .modal-overlay.active {
            display: flex;
            animation: fadeIn var(--dur-base) var(--easing);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .modal-box {
            width: min(960px, 100%);
            max-height: 92vh;
            overflow-y: auto;
            border-radius: var(--radius-xl);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            animation: zoomIn var(--dur-slow) var(--easing-bounce);
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(.94) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header {
            position: sticky;
            top: 0;
            z-index: 3;
            padding: 1rem 1.15rem;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            gap: .8rem;
        }
        .modal-header h3 { margin: 0; font-size: 1rem; font-weight: 950; }
        .modal-close {
            width: 36px; height: 36px;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            background: var(--bg-soft);
            color: var(--text-primary);
            display: flex; align-items: center; justify-content: center;
            transition: background var(--dur-fast), transform var(--dur-fast);
        }
        .modal-close:hover { background: var(--bg-hover); }
        .modal-close:active { transform: scale(.92); }
        .modal-body { padding: 1.15rem; }

        .detail-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.15rem;
            margin-bottom: 1.15rem;
        }
        .detail-image {
            width: 100%; height: 240px;
            object-fit: cover;
            border-radius: 14px;
            background: var(--bg-soft);
            border: 1px solid var(--border-color);
        }
        .detail-image-placeholder {
            width: 100%; height: 240px;
            border-radius: 14px;
            background: var(--bg-soft);
            border: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary);
            font-size: 3rem;
        }
        .detail-info-title {
            margin: 0 0 .7rem;
            font-size: .9rem;
            font-weight: 950;
            padding-bottom: .45rem;
            border-bottom: 2px solid var(--border-color);
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            font-size: .76rem;
        }
        .info-table tr { border-bottom: 1px solid var(--border-color); }
        .info-table tr:last-child { border-bottom: 0; }
        .info-table td { padding: .55rem .75rem; }
        .info-table td:first-child {
            width: 42%;
            color: var(--text-secondary);
            background: var(--bg-soft);
            font-weight: 700;
        }
        .info-table td:last-child { font-weight: 800; }

        .history-title {
            margin: 0 0 .55rem;
            font-size: .9rem;
            font-weight: 950;
        }
        .history-table-wrap {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .7rem;
            min-width: 640px;
        }
        .history-table thead {
            background: var(--bg-soft);
            color: var(--text-secondary);
        }
        .history-table th {
            padding: .5rem .65rem;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: .6rem;
            border-bottom: 1px solid var(--border-color);
        }
        .history-table td {
            padding: .5rem .65rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }
        .history-table tbody tr:last-child td { border-bottom: 0; }
        .history-table tbody tr:hover { background: var(--bg-soft); }

        .status-pill {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .18rem .55rem;
            border-radius: 999px;
            font-size: .6rem; font-weight: 900;
            border: 1px solid transparent;
        }
        .status-pill.selesai   { background: #d1fae5; color: #047857; border-color: #6ee7b7; }
        .status-pill.aktif     { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
        .status-pill.disetujui { background: #e0e7ff; color: #4338ca; border-color: #a5b4fc; }
        .status-pill.menunggu  { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
        .status-pill.ditolak   { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        .dark .status-pill.selesai   { background: #052e1b; color: #86efac; }
        .dark .status-pill.aktif     { background: #172554; color: #93c5fd; }
        .dark .status-pill.disetujui { background: #1e1b4b; color: #c7d2fe; }
        .dark .status-pill.menunggu  { background: #451a03; color: #fcd34d; }
        .dark .status-pill.ditolak   { background: #450a0a; color: #fca5a5; }

        /* ============================================================
           SCROLL TO TOP
        ============================================================ */
        .scroll-top {
            position: fixed;
            z-index: 700;
            right: 1.15rem; bottom: 1.15rem;
            width: 44px; height: 44px;
            border: 0;
            border-radius: 50%;
            background: #0f172a;
            color: #fff;
            box-shadow: var(--shadow-md);
            opacity: 0;
            pointer-events: none;
            transform: translateY(10px);
            transition:
                opacity var(--dur-base) var(--easing),
                transform var(--dur-base) var(--easing),
                background var(--dur-fast);
        }
        .dark .scroll-top { background: #38bdf8; color: #082f49; }
        .scroll-top.visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }
        .scroll-top:active { transform: scale(.94); }

        /* ============================================================
           RESPONSIVE
        ============================================================ */

        @media (max-width: 1320px) {
            .room-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .filter-form { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 1024px) {
            .room-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filter-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .detail-layout { grid-template-columns: 1fr; }
        }

        /* Tablet / Mobile (Sidebar off-canvas) */
        @media (max-width: 900px) {
            .app-body { margin-left: 0 !important; }
            .app-sidebar { box-shadow: 12px 0 40px rgba(0,0,0,.28); }
            .sidebar-backdrop { display: block; }
            body:not(.sidebar-closed) .sidebar-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
        }

        /* Mobile Portrait */
        @media (max-width: 640px) and (orientation: portrait) {
            .main-content { padding: .75rem; }
            .app-header { padding: .55rem .7rem; }
            .brand-subtitle { max-width: 140px; }

            .header-btn.dashboard-text {
                font-size: 0;
                width: 40px;
                padding: 0;
                border-radius: 50%;
            }
            .header-btn.dashboard-text i { font-size: .95rem; }
            .header-toggle { width: 40px; height: 40px; }

            .welcome-card {
                padding: .95rem 1rem;
                border-radius: var(--radius-lg);
                gap: .75rem;
            }
            .welcome-icon {
                width: 46px; height: 46px;
                font-size: 1.15rem;
                border-radius: 14px;
            }
            .welcome-text h2 { font-size: 1.05rem; }
            .welcome-text p  { font-size: .74rem; }
            .welcome-actions { width: 100%; gap: .45rem; }
            .sidebar-toggle-hero {
                min-height: 44px;
                padding: .5rem .85rem;
                font-size: .78rem;
                border-radius: var(--radius-md);
                flex: 1;
            }
            .sidebar-toggle-hero .kbd-inline { display: none; }
            .nim-inline { max-width: 100%; flex: 1 1 100%; }
            .nim-inline input { min-height: 44px; font-size: .82rem; }
            .nim-inline button { min-height: 44px; }

            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .65rem;
            }

            .room-card { border-radius: 13px; }
            .room-card-header { padding: .55rem .7rem; }
            .room-code-badge { font-size: .78rem; }
            .room-status-dot { width: 7px; height: 7px; }
            .card-image { height: 110px; }
            .card-body { padding: .65rem; }
            .room-title { font-size: .82rem; }
            .room-category { font-size: .52rem; padding: .15rem .45rem; margin-top: .25rem; }

            .info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .35rem;
                margin-top: .55rem;
            }
            .info-item { padding: .35rem .45rem; border-radius: var(--radius-sm); }
            .info-label { font-size: .48rem; }
            .info-value { font-size: .68rem; }

            .facilities { gap: .2rem; margin-top: .5rem; }
            .facility-tag { font-size: .5rem; padding: .15rem .4rem; }
            .facility-tag:nth-child(n+4) { display: none; }

            .booking-info {
                font-size: .58rem;
                padding: .42rem .55rem;
                margin-top: .5rem;
                white-space: normal;
                -webkit-line-clamp: 2;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .card-actions {
                grid-template-columns: 1fr 1fr;
                gap: .35rem;
                padding-top: .6rem;
            }
            .btn-action {
                min-height: 32px;
                font-size: .64rem;
                border-radius: var(--radius-sm);
            }
            .btn-action i { font-size: .72rem; }

            .rooms-title { font-size: 1.1rem; }
            .rooms-subtitle { font-size: .72rem; }
            .realtime-indicator { font-size: .62rem; padding: .32rem .55rem; }

            .filter-form { grid-template-columns: 1fr; gap: .5rem; }
            .filter-header { padding: .75rem .85rem; }
            .filter-header-left { font-size: .82rem; }
            .filter-reset { font-size: .68rem; }

            .modal-overlay { padding: 0; align-items: flex-end; }
            .modal-box {
                border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                max-height: 92vh;
                width: 100%;
            }
            .modal-header { padding: .85rem 1rem; }
            .modal-body { padding: 1rem; }
            .detail-layout { grid-template-columns: 1fr; gap: .85rem; }
            .detail-image, .detail-image-placeholder { height: 200px; }
        }

        /* Mobile Landscape */
        @media (max-width: 950px) and (orientation: landscape) and (max-height: 500px) {
            .main-content { padding: .65rem; }
            .app-header { padding: .45rem .8rem; }
            .brand-subtitle { display: none; }
            .header-toggle { width: 38px; height: 38px; }
            .header-btn.dashboard-text {
                font-size: 0; width: 38px;
                padding: 0; border-radius: 50%;
            }

            .welcome-card {
                padding: .75rem 1rem;
                border-radius: var(--radius-lg);
                margin-bottom: .85rem;
            }
            .welcome-icon { width: 40px; height: 40px; font-size: 1rem; }
            .welcome-text h2 { font-size: 1rem; }
            .welcome-text p  { display: none; }
            .sidebar-toggle-hero .kbd-inline { display: none; }

            .room-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: .55rem;
            }

            .room-card { border-radius: var(--radius-md); }
            .room-card-header { padding: .5rem .65rem; }
            .room-code-badge { font-size: .72rem; }
            .card-image { height: 85px; }
            .card-body { padding: .55rem; }
            .room-title { font-size: .76rem; }
            .room-category { font-size: .5rem; padding: .12rem .4rem; }

            .info-grid { gap: .3rem; margin-top: .45rem; }
            .info-item { padding: .3rem .4rem; }
            .info-label { font-size: .44rem; }
            .info-value { font-size: .62rem; }
            .info-item:nth-child(n+3) { display: none; }

            .facilities { display: none; }
            .booking-info {
                font-size: .55rem;
                padding: .35rem .5rem;
                margin-top: .4rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .card-actions { gap: .3rem; padding-top: .5rem; }
            .btn-action {
                min-height: 28px;
                font-size: .58rem;
                border-radius: 7px;
                padding: 0 .3rem;
            }
            .btn-action i { font-size: .65rem; }

            .filter-form { grid-template-columns: repeat(5, minmax(0, 1fr)); gap: .4rem; }
            .filter-header { padding: .6rem .85rem; }
            .rooms-title { font-size: 1rem; }
            .rooms-heading { margin-bottom: .65rem; }

            .app-sidebar { width: 240px; }
        }

        /* Small phone */
        @media (max-width: 380px) and (orientation: portrait) {
            .room-grid { grid-template-columns: 1fr; gap: .55rem; }
            .card-image { height: 140px; }
            .info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .facility-tag:nth-child(n+3) { display: none; }
        }

        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }

        /* Print */
        @media print {
            .app-sidebar, .app-header, .welcome-actions,
            .filter-section, .scroll-top, .modal-overlay, .sidebar-backdrop {
                display: none !important;
            }
            .app-body { margin-left: 0 !important; }
            .room-card { box-shadow: none; border: 1px solid #ccc; page-break-inside: avoid; }
        }
    </style>
</head>

<body class="sidebar-closed">

<div class="app-shell">

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <aside class="app-sidebar" id="appSidebar" aria-label="Menu Ruangan">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon"><i class="fas fa-door-open"></i></div>
                <div class="sidebar-brand-text">
                    <p class="sidebar-brand-title">Menu Ruangan</p>
                    <p class="sidebar-brand-sub">UNICODE · Room Publik</p>
                </div>
            </div>
            <button class="sidebar-close" id="sidebarClose" title="Tutup sidebar (Ctrl+B)" aria-label="Tutup sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="sidebar-body">
            <a href="{{ route('room.public.index') }}"
               class="sidebar-item {{ !request('room_token') ? 'active' : '' }}">
                <i class="fas fa-th leading"></i>
                <span class="label">Semua Ruangan</span>
                <span class="count">{{ $rooms->total() }}</span>
            </a>

            <div class="sidebar-section-title" style="margin-top:.5rem;">Berdasarkan Lantai</div>

            @forelse($roomsByFloor as $floor => $floorRooms)
                @php
                    $floorKey = $floor ?: 'Tanpa Lantai';
                    $isActiveFloor = request('room_token')
                        && $floorRooms->contains(fn ($r) => $r->room_token === request('room_token'));
                @endphp

                <div class="floor-group {{ $isActiveFloor ? 'open' : '' }}" data-floor="{{ $floorKey }}">
                    <button type="button" class="floor-header" aria-expanded="{{ $isActiveFloor ? 'true' : 'false' }}">
                        <i class="fas fa-chevron-right chev"></i>
                        <i class="fas fa-layer-group" style="font-size:.75rem;color:var(--text-secondary);"></i>
                        <span class="label">{{ $floorKey }}</span>
                        <span class="count">{{ $floorRooms->count() }}</span>
                    </button>

                    <div class="floor-rooms">
                        @foreach($floorRooms as $r)
                            <a href="{{ route('room.public.index', ['room_token' => $r->room_token]) }}"
                               class="room-link {{ request('room_token') === $r->room_token ? 'active' : '' }}"
                               data-room-id="{{ $r->id }}">
                                <i class="fas fa-door-closed"></i>
                                <span class="label">{{ $r->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="sidebar-empty">
                    <i class="fas fa-inbox"></i>
                    Belum ada ruangan publik.
                </div>
            @endforelse
        </div>

        <div class="sidebar-footer">
            <span>Toggle cepat</span>
            <span class="kbd">Ctrl</span> + <span class="kbd">B</span>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    {{-- ============================================================
         APP BODY
    ============================================================ --}}
    <div class="app-body">

        {{-- HEADER --}}
        <header class="app-header">
            <div class="header-inner">
                <div class="header-left">
                    <button type="button" class="header-toggle" id="headerSidebarToggle"
                            title="Buka/Tutup sidebar (Ctrl+B)" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="brand">
                        <div class="brand-logo"><i class="fas fa-door-open"></i></div>
                        <div class="brand-text">
                            <h1 class="brand-title">Room Publik</h1>
                            <p class="brand-subtitle">Informasi ruangan publik UNICODE</p>
                        </div>
                    </div>
                </div>

                <div class="header-actions">
                    <button id="themeToggle" type="button" class="header-btn icon-only" title="Ganti tema" aria-label="Ganti tema">
                        <i class="fas fa-moon"></i>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="header-btn primary dashboard-text" title="Dashboard">
                            <i class="fas fa-gauge-high"></i><span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="header-btn primary dashboard-text" title="Login">
                            <i class="fas fa-right-to-bracket"></i><span>Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="main-content">

            {{-- WELCOME CARD --}}
            <section class="welcome-card">
                <div class="welcome-left">
                    <div class="welcome-icon"><i class="fas fa-compass"></i></div>
                    <div class="welcome-text">
                        <h2>Selamat datang di Room Publik</h2>
                        <p>Telusuri ruangan per lantai dari sidebar, atau scan QR Code untuk melihat detail & jadwal realtime.</p>
                    </div>
                </div>

                <div class="welcome-actions">
                    <button type="button" class="sidebar-toggle-hero" id="heroSidebarToggle">
                        <i class="fas fa-bars"></i>
                        <span>Menu Ruangan</span>
                        <span class="kbd-inline">CTRL+B</span>
                    </button>

                    <form id="nimForm" class="nim-inline">
                        @csrf
                        <div class="nim-input-wrap">
                            <i class="fas fa-id-card"></i>
                            <input id="nimInput" type="text" maxlength="30"
                                   autocomplete="off" placeholder="Masukkan NIM">
                        </div>
                        <button type="submit" aria-label="Cari NIM"><i class="fas fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <p id="nimMessage"></p>
            </section>

            {{-- QR FOCUS BANNER --}}
            <section id="qrFocus" class="qr-focus" aria-live="polite">
                <div class="qr-focus-left">
                    <div class="qr-focus-icon"><i class="fas fa-qrcode"></i></div>
                    <div>
                        <p class="qr-focus-title">Mode QR Ruangan</p>
                        <p id="qrFocusRoom" class="qr-focus-room">Memuat ruangan...</p>
                    </div>
                </div>
                <button id="clearQrFocus" type="button" class="qr-focus-close" title="Tampilkan semua ruangan">
                    <i class="fas fa-xmark"></i>
                </button>
            </section>

            {{-- FILTER (Collapsible) --}}
            <section class="filter-section {{ request()->hasAny(['floor','category','status','condition','booking_today']) ? 'open' : '' }}" id="filterSection">
                <div class="filter-header" id="filterHeader" role="button" tabindex="0" aria-expanded="false">
                    <div class="filter-header-left">
                        <i class="fas fa-filter filter-icon"></i>
                        <span>Filter Ruangan</span>
                        <span class="filter-badge {{ request()->hasAny(['floor','category','status','condition','booking_today']) ? 'show' : '' }}" id="filterBadge">
                            Aktif
                        </span>
                    </div>
                    <div class="filter-header-right">
                        <a href="{{ route('room.public.index', request('room_token') ? ['room_token' => request('room_token')] : []) }}"
                           class="filter-reset"
                           onclick="event.stopPropagation();"
                           title="Reset filter">
                            Reset
                        </a>
                        <div class="filter-chevron"><i class="fas fa-chevron-down"></i></div>
                    </div>
                </div>

                <div class="filter-body" id="filterBody">
                    <form method="GET" class="filter-form" id="filterForm">
                        @if(request('room_token'))
                            <input type="hidden" name="room_token" value="{{ request('room_token') }}">
                        @endif

                        <select name="floor" aria-label="Filter lantai">
                            <option value="">Semua Lantai</option>
                            @foreach($floors as $floor)
                                <option value="{{ $floor }}" @selected(request('floor') === $floor)>{{ $floor }}</option>
                            @endforeach
                        </select>

                        <select name="category" aria-label="Filter kategori">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>

                        <select name="status" aria-label="Filter status">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>

                        <select name="condition" aria-label="Filter kondisi">
                            <option value="">Semua Kondisi</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition }}" @selected(request('condition') === $condition)>{{ $condition }}</option>
                            @endforeach
                        </select>

                        <label class="checkbox-group">
                            <input type="checkbox" name="booking_today" value="1" @checked(request('booking_today'))>
                            <span><i class="fas fa-calendar-day" style="color:#06b6d4;"></i> Booking hari ini</span>
                        </label>

                        <button class="btn-filter" type="submit">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                    </form>
                </div>
            </section>

            {{-- ROOM HEADING + GRID --}}
            <section>
                <div class="rooms-heading">
                    <div>
                        <h2 class="rooms-title">Ruangan</h2>
                        <p class="rooms-subtitle">
                            <span id="roomCount">{{ $rooms->total() }}</span> ruangan ditemukan
                        </p>
                    </div>
                    <div class="realtime-indicator">
                        <span class="realtime-indicator-dot"></span>
                        <span id="globalRealtimeText">Realtime aktif</span>
                    </div>
                </div>

                <div class="room-grid" id="roomGrid">
                    @forelse($rooms as $room)
                        @php
                            $images = is_array($room->images) ? $room->images : [];
                            $image  = $images[0] ?? null;

                            $booking = $room->bookings
                                ->where('status', 'Disetujui')
                                ->firstWhere(fn ($b) => $b->start_datetime?->isToday())
                                ?? $room->bookings()
                                    ->where('status', 'Disetujui')
                                    ->whereDate('start_datetime', today())
                                    ->orderBy('start_datetime')
                                    ->first();

                            $headerClass = match ($room->status) {
                                'Sedang Digunakan' => 'status-inuse',
                                'Dipesan'          => 'status-booked',
                                'Tersedia'         => '',
                                default            => 'status-other',
                            };
                        @endphp

                        <article class="room-card"
                                 id="room-{{ $room->id }}"
                                 data-room-id="{{ $room->id }}"
                                 data-room-token="{{ $room->room_token }}">

                            <div class="room-card-header {{ $headerClass }}">
                                <span class="room-code-badge">{{ $room->room_code ?: '—' }}</span>
                                <span class="room-status-dot" title="{{ $room->status }}"></span>
                            </div>

                            <div class="card-image">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="{{ $room->name }}"
                                         loading="lazy"
                                         onerror="this.replaceWith(Object.assign(document.createElement('div'),{
                                             className:'placeholder',
                                             innerHTML:'<i class=\'fas fa-door-open\'></i>'
                                         }))">
                                @else
                                    <div class="placeholder"><i class="fas fa-door-open"></i></div>
                                @endif
                            </div>

                            <div class="card-body">
                                <h3 class="room-title">{{ $room->name }}</h3>
                                <span class="room-category">
                                    {{ $room->category ?: 'Umum' }}
                                    @if($room->floor) · {{ $room->floor }} @endif
                                </span>

                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">Kapasitas</div>
                                        <div class="info-value">
                                            <i class="fas fa-users" style="color:#06b6d4;"></i>
                                            {{ $room->capacity_normal ?: '-' }}
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Status</div>
                                        <div class="info-value" data-live-status>{{ $room->status ?: '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Kondisi</div>
                                        <div class="info-value" data-live-condition>{{ $room->condition ?: '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Booking</div>
                                        <div class="info-value" data-live-booking>
                                            {{ $booking ? 'Ada booking' : 'Tidak ada' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="facilities" data-live-facilities>
                                    @forelse($room->facilities->take(5) as $facility)
                                        <span class="facility-tag">
                                            {{ $facility->name }}
                                            @if(($facility->quantity ?? 1) > 1) ×{{ $facility->quantity }} @endif
                                        </span>
                                    @empty
                                        <span class="facility-tag">Tidak ada fasilitas</span>
                                    @endforelse
                                </div>

                                <div class="booking-info {{ $booking ? 'has-booking' : 'no-booking' }}"
                                     data-live-booking-box>
                                    @if($booking)
                                        <i class="fas fa-calendar-check"></i>
                                        Booking:
                                        {{ optional($booking->start_datetime)->format('H:i') }}
                                        –
                                        {{ optional($booking->end_datetime)->format('H:i') }}
                                    @else
                                        <i class="fas fa-calendar-check"></i>
                                        Tidak ada booking hari ini
                                    @endif
                                </div>

                                <div class="card-actions">
                                    <button type="button" class="btn-action detail show-public-room">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <a href="{{ route('room.public.booking.form', $room->id) }}"
                                       class="btn-action book">
                                        <i class="fas fa-calendar-plus"></i> Pinjam
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-door-closed"></i></div>
                            <h3 class="empty-title">Belum ada ruangan publik</h3>
                            <p class="empty-description">
                                Ruangan yang sudah dipublikasikan akan muncul di halaman ini.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="pagination">{{ $rooms->links() }}</div>
            </section>
        </main>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div id="roomModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalRoomName">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalRoomName">-</h3>
            <button id="closeModal" type="button" class="modal-close" title="Tutup" aria-label="Tutup modal">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div id="modalContent" class="modal-body">Memuat...</div>
    </div>
</div>

{{-- SCROLL TOP --}}
<button id="scrollTopBtn" type="button" class="scroll-top" title="Kembali ke atas" aria-label="Kembali ke atas">
    <i class="fas fa-arrow-up"></i>
</button>

{{-- ============================================================
     ROOM DATA untuk JS
============================================================ --}}
@php
    $roomsData = $rooms->getCollection()->map(function ($room) {
        $images = is_array($room->images) ? $room->images : [];

        $history = $room->bookings()
            ->orderByDesc('start_datetime')
            ->limit(20)
            ->get()
            ->map(fn ($b) => [
                'id'             => $b->id,
                'requester_name' => $b->requester_name ?? $b->user->name ?? 'Anonim',
                'requester_id'   => $b->requester_id ?? '-',
                'room_code'      => $room->room_code,
                'created_at'     => optional($b->created_at)->format('Y-m-d H:i:s'),
                'start'          => optional($b->start_datetime)->format('Y-m-d H:i:s'),
                'end'            => optional($b->end_datetime)->format('Y-m-d H:i:s'),
                'status'         => $b->status,
            ])->values()->all();

        return [
            'id'                => $room->id,
            'name'              => $room->name,
            'code'              => $room->room_code,
            'token'             => $room->room_token,
            'building'          => $room->building,
            'floor'             => $room->floor,
            'category'          => $room->category,
            'capacity'          => $room->capacity_normal,
            'status'            => $room->status,
            'condition'         => $room->condition,
            'images'            => $images,
            'facilities'        => $room->facilities->map(fn ($f) => [
                'name'     => $f->name,
                'quantity' => $f->quantity,
            ])->values()->all(),
            'operational_hours' => $room->operational_hours ?? [],
            'history'           => $history,
        ];
    })->values();
@endphp

<script>
    const initialRooms = @json($roomsData);
    const realtimeRouteTemplate = @json(route('room.public.realtime', ['token' => '__ROOM_TOKEN__']));
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ============================================================
       SIDEBAR
    ============================================================ */
    const body              = document.body;
    const sidebarToggleHero = document.getElementById('heroSidebarToggle');
    const headerToggle      = document.getElementById('headerSidebarToggle');
    const sidebarClose      = document.getElementById('sidebarClose');
    const sidebarBackdrop   = document.getElementById('sidebarBackdrop');

    const isMobile = () => window.innerWidth <= 900;

    function setSidebarState(open) {
        body.classList.toggle('sidebar-closed', !open);
        body.classList.toggle('no-scroll', open && isMobile());
        try { localStorage.setItem('sidebarOpen', open ? '1' : '0'); } catch(e) {}
    }
    function toggleSidebar() {
        setSidebarState(body.classList.contains('sidebar-closed'));
    }

    (function initSidebar() {
        let stored = null;
        try { stored = localStorage.getItem('sidebarOpen'); } catch(e) {}
        if (stored === null) setSidebarState(!isMobile());
        else                 setSidebarState(stored === '1');
    })();

    sidebarToggleHero?.addEventListener('click', toggleSidebar);
    headerToggle?.addEventListener('click', toggleSidebar);
    sidebarClose?.addEventListener('click', () => setSidebarState(false));
    sidebarBackdrop?.addEventListener('click', () => setSidebarState(false));

    document.addEventListener('keydown', (e) => {
        const key = (e.key || '').toLowerCase();
        if ((e.ctrlKey || e.metaKey) && key === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
        if (e.key === 'Escape') {
            if (typeof closeRoomModal === 'function') closeRoomModal();
            if (isMobile()) setSidebarState(false);
        }
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) body.classList.remove('no-scroll');
    });

    /* ============================================================
       FLOOR ACCORDION
    ============================================================ */
    document.querySelectorAll('.floor-header').forEach(btn => {
        btn.addEventListener('click', () => {
            const group = btn.closest('.floor-group');
            const isOpen = group.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

    /* ============================================================
       FILTER COLLAPSIBLE
    ============================================================ */
    const filterSection = document.getElementById('filterSection');
    const filterHeader  = document.getElementById('filterHeader');

    filterHeader?.addEventListener('click', () => {
        const isOpen = filterSection.classList.toggle('open');
        filterHeader.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    filterHeader?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            filterHeader.click();
        }
    });

    /* ============================================================
       QR FOCUS
    ============================================================ */
    const qrFocus      = document.getElementById('qrFocus');
    const qrFocusRoom  = document.getElementById('qrFocusRoom');
    const clearQrFocus = document.getElementById('clearQrFocus');

    const urlParams   = new URLSearchParams(window.location.search);
    const qrRoomToken = urlParams.get('room_token');

    const getCardByToken = (token) =>
        token ? document.querySelector(`.room-card[data-room-token="${CSS.escape(token)}"]`) : null;
    const getCardById = (id) => document.querySelector(`.room-card[data-room-id="${id}"]`);

    function setQrFocus(token, roomName = 'Ruangan') {
        if (!token) { qrFocus?.classList.remove('active'); return; }
        if (qrFocusRoom) qrFocusRoom.textContent = roomName;
        qrFocus?.classList.add('active');
    }

    if (qrRoomToken) {
        const selectedRoom = initialRooms.find(room => room.token === qrRoomToken);
        if (selectedRoom) {
            setQrFocus(qrRoomToken, selectedRoom.name);
            const card = getCardByToken(qrRoomToken);
            if (card) {
                card.classList.add('qr-focused');
                setTimeout(() => card.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300);
            }
        }
    }

    clearQrFocus?.addEventListener('click', () => { window.location.href = window.location.pathname; });

    /* ============================================================
       REALTIME — IntersectionObserver
    ============================================================ */
    const realtimeTimers = {};
    const buildRealtimeUrl = (token) =>
        realtimeRouteTemplate.replace('__ROOM_TOKEN__', encodeURIComponent(token));

    async function loadRoomRealtime(card) {
        const token = card.dataset.roomToken;
        if (!token) return;

        const controller = new AbortController();
        const timeoutId  = setTimeout(() => controller.abort(), 8000);

        try {
            const response = await fetch(buildRealtimeUrl(token), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache',
                },
                cache: 'no-store',
                signal: controller.signal,
            });
            clearTimeout(timeoutId);
            if (response.status === 404) { stopRealtime(card); card.remove(); return; }
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            if (!data.success || !data.room) return;
            updateRoomCard(card, data.room);
            card.dataset.failCount = '0';
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') return;
            const fails = (Number(card.dataset.failCount) || 0) + 1;
            card.dataset.failCount = String(fails);
            if (fails >= 3) {
                stopRealtime(card);
                const el = card.querySelector('[data-live-updated]');
                if (el) el.textContent = 'Gagal terhubung';
            }
        }
    }

    function startRealtime(card) {
        const token = card.dataset.roomToken;
        if (!token || realtimeTimers[token]) return;
        loadRoomRealtime(card);
        realtimeTimers[token] = setInterval(() => loadRoomRealtime(card), 5000);
    }
    function stopRealtime(card) {
        const token = card.dataset.roomToken;
        if (token && realtimeTimers[token]) {
            clearInterval(realtimeTimers[token]);
            delete realtimeTimers[token];
        }
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const card = entry.target;
            if (!card.dataset.roomToken) return;
            entry.isIntersecting ? startRealtime(card) : stopRealtime(card);
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('.room-card').forEach(card => observer.observe(card));

    /* ============================================================
       UPDATE CARD dari data realtime
    ============================================================ */
    function updateRoomCard(card, room) {
        const liveStatus = room.live_status ?? room.status ?? '-';

        const statusEl = card.querySelector('[data-live-status]');
        if (statusEl) statusEl.textContent = liveStatus;

        const conditionEl = card.querySelector('[data-live-condition]');
        if (conditionEl && room.condition !== undefined) conditionEl.textContent = room.condition || '-';

        const header = card.querySelector('.room-card-header');
        if (header) {
            header.classList.remove('status-inuse', 'status-booked', 'status-other');
            if (liveStatus === 'Sedang Digunakan') header.classList.add('status-inuse');
            else if (liveStatus === 'Dipesan')     header.classList.add('status-booked');
            else if (liveStatus === 'Tersedia')    { /* default green */ }
            else                                   header.classList.add('status-other');
        }

        const liveBooking = card.querySelector('[data-live-booking]');
        const bookingBox  = card.querySelector('[data-live-booking-box]');

        if (room.current_booking) {
            const s = formatTime(room.current_booking.start);
            const e = formatTime(room.current_booking.end);
            if (liveBooking) liveBooking.textContent = `${s} – ${e}`;
            if (bookingBox) {
                bookingBox.classList.remove('no-booking');
                bookingBox.classList.add('has-booking');
                bookingBox.innerHTML = `<i class="fas fa-circle-play"></i> Sedang digunakan · ${s} – ${e}`;
            }
        } else if (room.next_booking) {
            const s = formatTime(room.next_booking.start);
            const e = formatTime(room.next_booking.end);
            if (liveBooking) liveBooking.textContent = `${s} – ${e}`;
            if (bookingBox) {
                bookingBox.classList.remove('no-booking');
                bookingBox.classList.add('has-booking');
                bookingBox.innerHTML = `<i class="fas fa-calendar-check"></i> Booking berikutnya · ${s} – ${e}`;
            }
        } else {
            if (liveBooking) liveBooking.textContent = 'Tidak ada';
            if (bookingBox) {
                bookingBox.classList.remove('has-booking');
                bookingBox.classList.add('no-booking');
                bookingBox.innerHTML = `<i class="fas fa-calendar-check"></i> Tidak ada booking`;
            }
        }

        const facilities = card.querySelector('[data-live-facilities]');
        if (facilities && Array.isArray(room.facilities)) {
            if (!room.facilities.length) {
                facilities.innerHTML = `<span class="facility-tag">Tidak ada fasilitas</span>`;
            } else {
                facilities.innerHTML = room.facilities.slice(0, 5).map(item => {
                    const qty = Number(item.quantity ?? 1);
                    return `<span class="facility-tag">${escapeHtml(item.name ?? '-')}${qty > 1 ? ` ×${qty}` : ''}</span>`;
                }).join('');
            }
        }

        if (room.is_public === false || room.is_published === false) {
            const isFocused = card.dataset.roomToken === qrRoomToken;
            card.remove();
            if (isFocused) setQrFocus(null);
        }
    }

    /* ============================================================
       HELPERS
    ============================================================ */
    function formatTime(value) {
        if (!value) return '--:--';
        try {
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return '--:--';
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        } catch { return '--:--'; }
    }

    function formatDateTime(value) {
        if (!value) return '-';
        try {
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleString('id-ID', {
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
            });
        } catch { return '-'; }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;').replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;').replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /* ============================================================
       MODAL DETAIL
    ============================================================ */
    const modal        = document.getElementById('roomModal');
    const modalContent = document.getElementById('modalContent');
    const modalName    = document.getElementById('modalRoomName');
    const closeModalBtn = document.getElementById('closeModal');

    function statusPillClass(status) {
        const s = String(status ?? '').toLowerCase();
        if (s.includes('selesai'))   return 'selesai';
        if (s.includes('sedang'))    return 'aktif';
        if (s.includes('setuju'))    return 'disetujui';
        if (s.includes('menunggu') || s.includes('pending')) return 'menunggu';
        if (s.includes('tolak') || s.includes('batal'))      return 'ditolak';
        return 'menunggu';
    }

    function showRoom(room) {
        if (!room) return;
        modalName.textContent = room.name || 'Ruangan';

        const imageHtml = room.images?.[0]
            ? `<img class="detail-image" src="/storage/${escapeHtml(room.images[0])}" alt="${escapeHtml(room.name)}" loading="lazy">`
            : `<div class="detail-image-placeholder"><i class="fas fa-door-open"></i></div>`;

        const facilitiesHtml = (Array.isArray(room.facilities) && room.facilities.length)
            ? room.facilities.map(f => {
                const qty = Number(f.quantity ?? 1);
                return `<span class="facility-tag">${escapeHtml(f.name)}${qty > 1 ? ` ×${qty}` : ''}</span>`;
            }).join('')
            : `<span style="color:var(--text-secondary);font-size:.75rem;">Tidak tersedia</span>`;

        const history = Array.isArray(room.history) ? room.history : [];
        const historyRows = history.length
            ? history.map((b, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>
                        <strong>${escapeHtml(b.requester_name)}</strong>
                        <div style="color:var(--text-secondary);font-size:.6rem;">${escapeHtml(b.requester_id)}</div>
                    </td>
                    <td>${escapeHtml(b.room_code ?? '-')}</td>
                    <td>${formatDateTime(b.created_at)}</td>
                    <td>${formatDateTime(b.start)}</td>
                    <td>${formatDateTime(b.end)}</td>
                    <td>
                        <span class="status-pill ${statusPillClass(b.status)}">
                            ${escapeHtml(b.status ?? '-')}
                        </span>
                    </td>
                </tr>
            `).join('')
            : `<tr><td colspan="7" style="text-align:center;color:var(--text-secondary);padding:1.4rem;">
                   Belum ada riwayat peminjaman.
               </td></tr>`;

        const operationalEntries = Object.entries(room.operational_hours || {});
        const operationalHtml = operationalEntries.length
            ? operationalEntries.map(([day, hour]) => `
                <div style="display:flex;justify-content:space-between;gap:.75rem;padding:.32rem 0;border-bottom:1px solid var(--border-color);">
                    <span>${escapeHtml(day)}</span>
                    <strong>${escapeHtml(hour)}</strong>
                </div>`).join('')
            : `<span style="color:var(--text-secondary);font-size:.75rem;">Informasi jam operasional tidak tersedia.</span>`;

        modalContent.innerHTML = `
            <div class="detail-layout">
                <div>${imageHtml}</div>
                <div>
                    <h4 class="detail-info-title">Informasi Ruangan</h4>
                    <table class="info-table">
                        <tr><td>Kode Ruangan</td><td>${escapeHtml(room.code ?? '-')}</td></tr>
                        <tr><td>Nama Ruangan</td><td>${escapeHtml(room.name ?? '-')}</td></tr>
                        <tr><td>Gedung</td><td>${escapeHtml(room.building ?? '-')}</td></tr>
                        <tr><td>Lantai</td><td>${escapeHtml(room.floor ?? '-')}</td></tr>
                        <tr><td>Kategori</td><td>${escapeHtml(room.category ?? '-')}</td></tr>
                        <tr><td>Kapasitas</td><td>${escapeHtml(room.capacity ?? '-')} orang</td></tr>
                        <tr><td>Status</td><td>${escapeHtml(room.live_status ?? room.status ?? '-')}</td></tr>
                        <tr><td>Kondisi</td><td>${escapeHtml(room.condition ?? '-')}</td></tr>
                    </table>
                </div>
            </div>

            <div style="margin-top:1rem;">
                <h4 class="detail-info-title">Fasilitas</h4>
                <div class="facilities">${facilitiesHtml}</div>
            </div>

            <div style="margin-top:1.15rem;">
                <h4 class="detail-info-title">Jam Operasional</h4>
                <div style="padding:.75rem;background:var(--bg-soft);border-radius:12px;font-size:.75rem;">
                    ${operationalHtml}
                </div>
            </div>

            <div style="margin-top:1.15rem;">
                <h4 class="history-title">Riwayat Peminjaman</h4>
                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Peminjam</th>
                                <th>Ruangan</th>
                                <th>Transaksi</th>
                                <th>Pinjam</th>
                                <th>Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${historyRows}</tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top:1rem;padding:.75rem;border-radius:13px;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;font-size:.72rem;">
                <i class="fas fa-circle-info"></i>
                Informasi ditampilkan sesuai pengaturan publikasi ruangan.
            </div>
        `;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    window.closeRoomModal = function() {
        modal?.classList.remove('active');
        document.body.style.overflow = '';
    };

    closeModalBtn?.addEventListener('click', window.closeRoomModal);
    modal?.addEventListener('click', e => { if (e.target === modal) window.closeRoomModal(); });

    /* ============================================================
       ROOM CARD EVENTS
    ============================================================ */
    document.querySelectorAll('.room-card').forEach(card => {
        const id   = Number(card.dataset.roomId);
        const room = initialRooms.find(item => Number(item.id) === id);

        card.querySelector('.show-public-room')?.addEventListener('click', e => {
            e.stopPropagation();
            if (room) showRoom(room);
        });

        card.addEventListener('click', e => {
            if (e.target.closest('a, button')) return;
            if (window.innerWidth <= 768 && room) showRoom(room);
        });

        card.addEventListener('dblclick', () => { if (room) showRoom(room); });
    });

    /* ============================================================
       NIM SEARCH
    ============================================================ */
    const nimForm = document.getElementById('nimForm');
    nimForm?.addEventListener('submit', async event => {
        event.preventDefault();
        const input   = document.getElementById('nimInput');
        const message = document.getElementById('nimMessage');
        const nim     = input.value.trim();

        message.style.display = 'block';
        if (!nim) { message.textContent = 'Silakan masukkan NIM.'; return; }
        message.textContent = 'Mencari ruangan...';

        try {
            const response = await fetch('{{ route('room.public.nim') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nim }),
            });
            const data = await response.json();
            if (!data.success) { message.textContent = data.message || 'Pencarian gagal.'; return; }
            if (!Array.isArray(data.rooms) || !data.rooms.length) {
                message.textContent = 'Tidak ada ruangan publik yang sedang berlangsung.';
                return;
            }

            message.textContent = `${data.rooms.length} ruangan ditemukan.`;

            const target = getCardById(data.rooms[0].id);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.classList.add('qr-focused');
                setTimeout(() => target.classList.remove('qr-focused'), 2500);
            }
        } catch (err) {
            console.error(err);
            message.textContent = 'Terjadi kesalahan saat mencari ruangan.';
        }
    });

    /* ============================================================
       THEME
    ============================================================ */
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    function setTheme(theme) {
        if (theme === 'dark') {
            htmlElement.classList.add('dark');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            htmlElement.classList.remove('dark');
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
        try { localStorage.setItem('theme', theme); } catch(e) {}
    }
    let savedTheme = 'light';
    try { savedTheme = localStorage.getItem('theme') || 'light'; } catch(e) {}
    setTheme(savedTheme);

    themeToggle?.addEventListener('click', () => {
        setTheme(htmlElement.classList.contains('dark') ? 'light' : 'dark');
    });

    /* ============================================================
       SCROLL TOP
    ============================================================ */
    const scrollBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', () => {
        scrollBtn.classList.toggle('visible', window.scrollY > 450);
    }, { passive: true });
    scrollBtn?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
});
</script>

</body>
</html>
@endsection