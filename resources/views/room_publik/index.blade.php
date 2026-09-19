<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Room Publik – UNICODE</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-soft: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --header-bg: rgba(255,255,255,.94);
            --shadow-sm: 0 1px 3px rgba(15,23,42,.06);
            --shadow-md: 0 10px 30px rgba(15,23,42,.08);
            --shadow-lg: 0 20px 60px rgba(15,23,42,.12);
            --primary: #2563eb;
            --cyan: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        .dark {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-soft: #273449;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #334155;
            --header-bg: rgba(15,23,42,.94);
            --shadow-sm: 0 1px 3px rgba(0,0,0,.25);
            --shadow-md: 0 10px 30px rgba(0,0,0,.25);
            --shadow-lg: 0 20px 60px rgba(0,0,0,.35);
        }

        * {
            box-sizing: border-box;
            transition:
                background-color .2s ease,
                border-color .2s ease,
                color .2s ease,
                box-shadow .2s ease;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-primary);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        button,
        input,
        select {
            font: inherit;
        }

        a {
            color: inherit;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .app-header {
            position: sticky;
            top: 0;
            z-index: 500;
            background: var(--header-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-color);
            padding: .8rem 1.25rem;
        }

        .header-inner {
            max-width: 1800px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-width: 0;
        }

        .brand-logo {
            width: 46px;
            height: 46px;
            border-radius: 15px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #06b6d4, #2563eb, #4f46e5);
            color: #fff;
            font-size: 1.35rem;
            box-shadow: 0 8px 25px rgba(37,99,235,.25);
        }

        .brand-text {
            min-width: 0;
        }

        .brand-title {
            margin: 0;
            font-weight: 900;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .brand-subtitle {
            margin: .1rem 0 0;
            font-size: .72rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: .55rem;
        }

        .header-btn {
            min-height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--text-primary);
            padding: .55rem .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            text-decoration: none;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
        }

        .header-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .header-btn.icon-only {
            width: 40px;
            padding: 0;
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

        /* =========================================================
           MAIN
        ========================================================= */

        .main-content {
            width: min(1800px, 100%);
            margin: 0 auto;
            padding: 1.5rem;
        }

        /* =========================================================
           HERO
        ========================================================= */

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 2rem;
            margin-bottom: 1.35rem;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.18), transparent 28%),
                radial-gradient(circle at bottom left, rgba(255,255,255,.10), transparent 25%),
                linear-gradient(135deg, #06b6d4, #2563eb 55%, #4f46e5);
            box-shadow: 0 25px 70px rgba(37,99,235,.22);
        }

        .hero::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            right: -80px;
            top: -100px;
            background: rgba(255,255,255,.08);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .35rem .8rem;
            border-radius: 999px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.14);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .hero-title {
            margin: .9rem 0 .45rem;
            font-size: clamp(1.65rem, 3vw, 2.5rem);
            line-height: 1.08;
            font-weight: 950;
        }

        .hero-description {
            max-width: 760px;
            margin: 0 0 1.2rem;
            color: rgba(255,255,255,.82);
            line-height: 1.7;
            font-size: .94rem;
        }

        .nim-form {
            width: min(620px, 100%);
            display: grid;
            grid-template-columns: 1fr auto;
            gap: .65rem;
        }

        .nim-input-wrap {
            position: relative;
        }

        .nim-input-wrap i {
            position: absolute;
            left: .95rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.58);
        }

        .nim-input {
            width: 100%;
            min-height: 48px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,.2);
            padding: .75rem 1rem .75rem 2.75rem;
            background: rgba(255,255,255,.10);
            color: white;
            outline: none;
            backdrop-filter: blur(6px);
        }

        .nim-input::placeholder {
            color: rgba(255,255,255,.52);
        }

        .nim-input:focus {
            border-color: rgba(255,255,255,.65);
            background: rgba(255,255,255,.15);
        }

        .nim-submit {
            min-height: 48px;
            border: 0;
            border-radius: 15px;
            padding: .75rem 1.2rem;
            background: #fff;
            color: #0f172a;
            font-weight: 800;
            cursor: pointer;
        }

        .nim-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0,0,0,.15);
        }

        #nimMessage {
            display: none;
            margin: .75rem 0 0;
            font-size: .8rem;
            color: rgba(255,255,255,.8);
        }

        /* =========================================================
           QR FOCUS
        ========================================================= */

        .qr-focus {
            display: none;
            margin-bottom: 1.35rem;
            padding: 1rem 1.15rem;
            border-radius: 18px;
            border: 1px solid #bfdbfe;
            background: linear-gradient(
                135deg,
                rgba(239,246,255,.96),
                rgba(236,254,255,.96)
            );
            box-shadow: var(--shadow-sm);
        }

        .dark .qr-focus {
            border-color: #164e63;
            background: linear-gradient(
                135deg,
                rgba(15,23,42,.92),
                rgba(8,47,73,.92)
            );
        }

        .qr-focus.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .qr-focus-left {
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .qr-focus-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #2563eb;
        }

        .dark .qr-focus-icon {
            background: #082f49;
            color: #67e8f9;
        }

        .qr-focus-title {
            margin: 0;
            font-size: .78rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 800;
        }

        .qr-focus-room {
            margin: .15rem 0 0;
            font-weight: 900;
            color: var(--text-primary);
        }

        .qr-focus-close {
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1.05rem;
        }

        /* =========================================================
           FILTER
        ========================================================= */

        .filter-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 1.35rem;
            box-shadow: var(--shadow-sm);
        }

        .filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .85rem;
        }

        .filter-header h3 {
            margin: 0;
            font-size: .92rem;
            font-weight: 900;
        }

        .filter-reset {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: .75rem;
            font-weight: 700;
        }

        .filter-reset:hover {
            color: var(--primary);
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .65rem;
        }

        .filter-form select,
        .filter-form .checkbox-group {
            min-height: 42px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--bg-soft);
            color: var(--text-primary);
            padding: .6rem .75rem;
            outline: none;
        }

        .filter-form .checkbox-group {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
        }

        .filter-form input[type="checkbox"] {
            accent-color: var(--primary);
        }

        .btn-filter {
            min-height: 42px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }

        /* =========================================================
           ROOM HEADER
        ========================================================= */

        .rooms-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1rem;
            margin-bottom: .9rem;
        }

        .rooms-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 950;
        }

        .rooms-subtitle {
            margin: .2rem 0 0;
            color: var(--text-secondary);
            font-size: .82rem;
        }

        .realtime-indicator {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .42rem .7rem;
            border-radius: 999px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: .72rem;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        .realtime-indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,.12);
        }

        /* =========================================================
           ROOM GRID
        ========================================================= */

        .room-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .room-card {
            position: relative;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }

        .room-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .room-card.qr-focused {
            border-color: #06b6d4;
            box-shadow:
                0 0 0 2px rgba(6,182,212,.15),
                0 20px 55px rgba(6,182,212,.15);
        }

        .room-card.qr-focused::before {
            content: "RUANGAN QR";
            position: absolute;
            right: .75rem;
            top: .75rem;
            z-index: 10;
            padding: .3rem .55rem;
            border-radius: 999px;
            font-size: .58rem;
            font-weight: 900;
            letter-spacing: .07em;
            color: #0f172a;
            background: #67e8f9;
        }

        /* =========================================================
           LIVE PANEL
        ========================================================= */

        .live-room-panel {
            padding: .8rem;
            border-bottom: 1px solid var(--border-color);
            background:
                linear-gradient(
                    135deg,
                    rgba(6,182,212,.05),
                    rgba(37,99,235,.05)
                );
        }

        .live-status-row {
            display: flex;
            align-items: center;
            gap: .42rem;
            font-size: .62rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,.12);
            animation: pulseLive 1.8s infinite;
        }

        .live-updated {
            margin-left: auto;
            color: var(--text-secondary);
            font-size: .58rem;
            text-transform: none;
            letter-spacing: 0;
        }

        @keyframes pulseLive {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: .55;
                transform: scale(.82);
            }
        }

        .live-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .45rem;
            margin-top: .65rem;
        }

        .live-item {
            padding: .5rem .6rem;
            border-radius: 10px;
            background: var(--bg-soft);
            min-width: 0;
        }

        .live-label {
            color: var(--text-secondary);
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .live-value {
            margin-top: .1rem;
            color: var(--text-primary);
            font-size: .74rem;
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================================================
           IMAGE
        ========================================================= */

        .card-image {
            position: relative;
            height: 185px;
            overflow: hidden;
            background: var(--bg-soft);
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 3rem;
        }

        .status-badge {
            position: absolute;
            left: .75rem;
            bottom: .75rem;
            padding: .35rem .65rem;
            border-radius: 999px;
            font-size: .62rem;
            font-weight: 900;
            color: #fff;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 20px rgba(0,0,0,.16);
        }

        .status-badge.available {
            background: rgba(16,185,129,.92);
        }

        .status-badge.in-use {
            background: rgba(37,99,235,.92);
        }

        .status-badge.booked {
            background: rgba(245,158,11,.95);
        }

        .status-badge.other {
            background: rgba(100,116,139,.92);
        }

        /* =========================================================
           CARD BODY
        ========================================================= */

        .card-body {
            padding: .95rem;
        }

        .room-heading-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .7rem;
        }

        .room-category {
            display: inline-flex;
            align-items: center;
            padding: .22rem .55rem;
            border-radius: 999px;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .58rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .room-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 950;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .room-code {
            display: block;
            margin-top: .2rem;
            color: var(--text-secondary);
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: .62rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
            margin-top: .8rem;
        }

        .info-item {
            background: var(--bg-soft);
            border-radius: 11px;
            padding: .55rem .65rem;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: .56rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-value {
            margin-top: .1rem;
            color: var(--text-primary);
            font-size: .76rem;
            font-weight: 900;
        }

        .facilities {
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            margin-top: .75rem;
        }

        .facility-tag {
            padding: .23rem .52rem;
            border-radius: 999px;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .58rem;
            font-weight: 700;
        }

        /* =========================================================
           BOOKING
        ========================================================= */

        .booking-info {
            margin-top: .75rem;
            border-radius: 11px;
            padding: .6rem .7rem;
            font-size: .66rem;
            font-weight: 800;
            line-height: 1.4;
        }

        .booking-info.has-booking {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .dark .booking-info.has-booking {
            background: #172554;
            color: #93c5fd;
        }

        .booking-info.no-booking {
            background: #d1fae5;
            color: #047857;
        }

        .dark .booking-info.no-booking {
            background: #052e1b;
            color: #86efac;
        }

        /* =========================================================
           SCHEDULE
        ========================================================= */

        .schedule-section {
            margin-top: .8rem;
            border-top: 1px solid var(--border-color);
            padding-top: .8rem;
        }

        .schedule-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            margin-bottom: .5rem;
        }

        .schedule-title {
            margin: 0;
            font-size: .72rem;
            font-weight: 900;
        }

        .schedule-day {
            color: var(--text-secondary);
            font-size: .59rem;
        }

        .schedule-list {
            display: grid;
            gap: .4rem;
            max-height: 190px;
            overflow: auto;
            padding-right: 2px;
        }

        .schedule-item {
            display: grid;
            grid-template-columns: 76px 1fr;
            gap: .55rem;
            align-items: start;
            padding: .55rem;
            border-radius: 10px;
            background: var(--bg-soft);
        }

        .schedule-time {
            font-size: .61rem;
            font-weight: 900;
            color: #2563eb;
        }

        .dark .schedule-time {
            color: #67e8f9;
        }

        .schedule-name {
            margin: 0;
            font-size: .64rem;
            font-weight: 900;
            color: var(--text-primary);
        }

        .schedule-meta {
            margin: .12rem 0 0;
            font-size: .55rem;
            color: var(--text-secondary);
        }

        .schedule-empty {
            padding: .7rem;
            border-radius: 10px;
            background: var(--bg-soft);
            color: var(--text-secondary);
            font-size: .62rem;
            text-align: center;
        }

        /* =========================================================
           SHOW BUTTON
        ========================================================= */

        .btn-show {
            width: 100%;
            min-height: 42px;
            margin-top: .85rem;
            border: 0;
            border-radius: 12px;
            background: #0f172a;
            color: #fff;
            font-size: .73rem;
            font-weight: 900;
            cursor: pointer;
        }

        .dark .btn-show {
            background: #38bdf8;
            color: #082f49;
        }

        .btn-show:hover {
            transform: translateY(-1px);
        }

        /* =========================================================
           EMPTY
        ========================================================= */

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 1rem;
        }

        .empty-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 1rem;
            border-radius: 28px;
            background: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 2.4rem;
        }

        .empty-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 900;
        }

        .empty-description {
            margin: .35rem 0 0;
            color: var(--text-secondary);
            font-size: .78rem;
        }

        /* =========================================================
           PAGINATION
        ========================================================= */

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        /* =========================================================
           MODAL
        ========================================================= */

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
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            width: min(760px, 100%);
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 40px 90px rgba(0,0,0,.28);
        }

        .modal-header {
            position: sticky;
            top: 0;
            z-index: 3;
            padding: 1rem 1.2rem;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 950;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            background: var(--bg-soft);
            color: var(--text-primary);
            cursor: pointer;
        }

        .modal-body {
            padding: 1.2rem;
        }

        .modal-image {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-radius: 16px;
            background: var(--bg-soft);
        }

        .modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .65rem;
            margin-top: 1rem;
        }

        .modal-info-box {
            padding: .8rem;
            border-radius: 13px;
            background: var(--bg-soft);
        }

        .modal-info-label {
            color: var(--text-secondary);
            font-size: .62rem;
        }

        .modal-info-value {
            margin-top: .15rem;
            font-weight: 900;
            font-size: .86rem;
        }

        /* =========================================================
           SCROLL TOP
        ========================================================= */

        .scroll-top {
            position: fixed;
            z-index: 700;
            right: 1.25rem;
            bottom: 1.25rem;
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 50%;
            background: #0f172a;
            color: white;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            opacity: 0;
            pointer-events: none;
        }

        .dark .scroll-top {
            background: #38bdf8;
            color: #082f49;
        }

        .scroll-top.visible {
            opacity: 1;
            pointer-events: auto;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1320px) {
            .room-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .app-header {
                padding: .7rem .8rem;
            }

            .header-inner {
                align-items: flex-start;
            }

            .brand-subtitle {
                max-width: 200px;
            }

            .header-btn.dashboard-text {
                font-size: 0;
                width: 40px;
                padding: 0;
                border-radius: 50%;
            }

            .header-btn.dashboard-text i {
                font-size: .95rem;
            }

            .main-content {
                padding: .85rem;
            }

            .hero {
                padding: 1.3rem;
                border-radius: 21px;
            }

            .nim-form {
                grid-template-columns: 1fr;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .rooms-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .room-grid {
                grid-template-columns: 1fr;
            }

            .qr-focus.active {
                align-items: flex-start;
            }

            .modal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header class="app-header">
    <div class="header-inner">

        <div class="brand">
            <div class="brand-logo">
                <i class="fas fa-door-open"></i>
            </div>

            <div class="brand-text">
                <h1 class="brand-title">Room Publik</h1>
                <p class="brand-subtitle">
                    Informasi ruangan publik UNICODE
                </p>
            </div>
        </div>

        <div class="header-actions">

            <button
                id="themeToggle"
                type="button"
                class="header-btn icon-only"
                title="Ganti tema"
            >
                <i class="fas fa-moon"></i>
            </button>

            @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="header-btn primary dashboard-text"
                    title="Dashboard"
                >
                    <i class="fas fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="header-btn primary dashboard-text"
                    title="Login"
                >
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>
            @endauth

        </div>
    </div>
</header>

<main class="main-content">

    {{-- =========================================================
         HERO
    ========================================================== --}}

    <section class="hero">
        <div class="hero-content">

            <span class="hero-tag">
                <i class="fas fa-globe"></i>
                Akses Publik
            </span>

            <h2 class="hero-title">
                Selamat datang di Room Publik
            </h2>

            <p class="hero-description">
                Scan QR Code ruangan untuk melihat informasi ruangan,
                jadwal publik, booking, status penggunaan, hari, jam,
                fasilitas, serta perubahan data secara realtime.
            </p>

            <form id="nimForm" class="nim-form">
                @csrf

                <div class="nim-input-wrap">
                    <i class="fas fa-id-card"></i>

                    <input
                        id="nimInput"
                        class="nim-input"
                        type="text"
                        maxlength="30"
                        autocomplete="off"
                        placeholder="Masukkan NIM"
                    >
                </div>

                <button
                    class="nim-submit"
                    type="submit"
                >
                    <i class="fas fa-magnifying-glass"></i>
                    Lihat Room
                </button>
            </form>

            <p id="nimMessage"></p>

        </div>
    </section>

    {{-- =========================================================
         QR FOCUS MODE
    ========================================================== --}}

    <section
        id="qrFocus"
        class="qr-focus"
        aria-live="polite"
    >
        <div class="qr-focus-left">

            <div class="qr-focus-icon">
                <i class="fas fa-qrcode"></i>
            </div>

            <div>
                <p class="qr-focus-title">
                    Mode QR Ruangan
                </p>

                <p
                    id="qrFocusRoom"
                    class="qr-focus-room"
                >
                    Memuat ruangan...
                </p>
            </div>

        </div>

        <button
            id="clearQrFocus"
            type="button"
            class="qr-focus-close"
            title="Tampilkan semua ruangan"
        >
            <i class="fas fa-xmark"></i>
        </button>
    </section>

    {{-- =========================================================
         FILTER
    ========================================================== --}}

    <section class="filter-section">

        <div class="filter-header">

            <h3>
                <i
                    class="fas fa-filter"
                    style="color:#06b6d4;"
                ></i>

                Filter Ruangan
            </h3>

            <a
                href="{{ route('room.public') }}"
                class="filter-reset"
            >
                Reset
            </a>

        </div>

        <form
            method="GET"
            class="filter-form"
            id="filterForm"
        >

            {{-- Pertahankan room_token ketika memakai filter --}}
            @if(request('room_token'))
                <input
                    type="hidden"
                    name="room_token"
                    value="{{ request('room_token') }}"
                >
            @endif

            <select name="floor">
                <option value="">
                    Semua Lantai
                </option>

                @foreach($floors as $floor)
                    <option
                        value="{{ $floor }}"
                        @selected(request('floor') === $floor)
                    >
                        {{ $floor }}
                    </option>
                @endforeach
            </select>

            <select name="category">
                <option value="">
                    Semua Kategori
                </option>

                @foreach($categories as $category)
                    <option
                        value="{{ $category }}"
                        @selected(request('category') === $category)
                    >
                        {{ $category }}
                    </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">
                    Semua Status
                </option>

                @foreach($statuses as $status)
                    <option
                        value="{{ $status }}"
                        @selected(request('status') === $status)
                    >
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            <select name="condition">
                <option value="">
                    Semua Kondisi
                </option>

                @foreach($conditions as $condition)
                    <option
                        value="{{ $condition }}"
                        @selected(request('condition') === $condition)
                    >
                        {{ $condition }}
                    </option>
                @endforeach
            </select>

            <label class="checkbox-group">
                <input
                    type="checkbox"
                    name="booking_today"
                    value="1"
                    @checked(request('booking_today'))
                >

                <span>
                    <i
                        class="fas fa-calendar-day"
                        style="color:#06b6d4;"
                    ></i>

                    Booking hari ini
                </span>
            </label>

            <button
                class="btn-filter"
                type="submit"
            >
                <i class="fas fa-filter"></i>
                Terapkan
            </button>

        </form>

    </section>

    {{-- =========================================================
         ROOM HEADING
    ========================================================== --}}

    <section>

        <div class="rooms-heading">

            <div>
                <h2 class="rooms-title">
                    Ruangan
                </h2>

                <p class="rooms-subtitle">
                    <span id="roomCount">
                        {{ $rooms->total() }}
                    </span>

                    ruangan ditemukan
                </p>
            </div>

            <div class="realtime-indicator">
                <span class="realtime-indicator-dot"></span>

                <span id="globalRealtimeText">
                    Realtime aktif
                </span>
            </div>

        </div>

        {{-- =====================================================
             ROOM GRID
        ====================================================== --}}

        <div
            class="room-grid"
            id="roomGrid"
        >

            @forelse($rooms as $room)

                @php
                    $images = is_array($room->images)
                        ? $room->images
                        : [];

                    $image = $images[0] ?? null;

                    $booking = $room->bookings()
                        ->where('status', 'Disetujui')
                        ->whereDate('start_datetime', today())
                        ->orderBy('start_datetime')
                        ->first();

                    $roomSchedules = \App\Models\Schedule::query()
                        ->where('room_id', $room->id)
                        ->orderBy('start_datetime')
                        ->limit(8)
                        ->get();
                @endphp

                <article
                    class="room-card"
                    data-room-id="{{ $room->id }}"
                    data-room-token="{{ $room->room_token }}"
                >

                    {{-- =================================================
                         LIVE PANEL
                    ================================================== --}}

                    <div class="live-room-panel">

                        <div class="live-status-row">

                            <span class="live-dot"></span>

                            <span>LIVE</span>

                            <span
                                class="live-updated"
                                data-live-updated
                            >
                                Menunggu data
                            </span>

                        </div>

                        <div class="live-grid">

                            <div class="live-item">
                                <div class="live-label">
                                    Hari
                                </div>

                                <div
                                    class="live-value"
                                    data-live-day
                                >
                                    -
                                </div>
                            </div>

                            <div class="live-item">
                                <div class="live-label">
                                    Jam
                                </div>

                                <div
                                    class="live-value"
                                    data-live-clock
                                >
                                    -
                                </div>
                            </div>

                            <div class="live-item">
                                <div class="live-label">
                                    Status
                                </div>

                                <div
                                    class="live-value"
                                    data-live-status
                                >
                                    {{ $room->status ?: '-' }}
                                </div>
                            </div>

                            <div class="live-item">
                                <div class="live-label">
                                    Booking
                                </div>

                                <div
                                    class="live-value"
                                    data-live-booking
                                >
                                    {{ $booking ? 'Ada booking' : 'Tidak ada' }}
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- =================================================
                         FOTO
                    ================================================== --}}

                    <div class="card-image">

                        @if($image)

                            <img
                                src="{{ asset('storage/' . $image) }}"
                                alt="{{ $room->name }}"
                                loading="lazy"
                            >

                        @else

                            <div class="placeholder">
                                <i class="fas fa-door-open"></i>
                            </div>

                        @endif

                        <div
                            class="status-badge
                                @if($room->status === 'Tersedia')
                                    available
                                @elseif($room->status === 'Sedang Digunakan')
                                    in-use
                                @elseif($room->status === 'Dipesan')
                                    booked
                                @else
                                    other
                                @endif"
                            data-live-badge
                        >
                            @if($room->status === 'Tersedia')
                                🟢 Tersedia
                            @elseif($room->status === 'Sedang Digunakan')
                                🔵 Sedang Digunakan
                            @elseif($room->status === 'Dipesan')
                                🟡 Dipesan
                            @else
                                {{ $room->status ?: 'Tidak tersedia' }}
                            @endif
                        </div>

                    </div>

                    {{-- =================================================
                         CARD BODY
                    ================================================== --}}

                    <div class="card-body">

                        <div class="room-heading-row">

                            <div>
                                <h3 class="room-title">
                                    {{ $room->name }}
                                </h3>

                                <span class="room-code">
                                    {{ $room->room_code }}
                                </span>
                            </div>

                            <span class="room-category">
                                {{ $room->category ?: 'Umum' }}
                            </span>

                        </div>

                        {{-- INFO --}}
                        <div class="info-grid">

                            <div class="info-item">
                                <div class="info-label">
                                    Kapasitas
                                </div>

                                <div class="info-value">
                                    <i
                                        class="fas fa-users"
                                        style="color:#06b6d4;"
                                    ></i>

                                    {{ $room->capacity_normal ?: '-' }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    Lantai
                                </div>

                                <div class="info-value">
                                    {{ $room->floor ?: '-' }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    Kondisi
                                </div>

                                <div
                                    class="info-value"
                                    data-live-condition
                                >
                                    {{ $room->condition ?: '-' }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    Kategori
                                </div>

                                <div class="info-value">
                                    {{ $room->category ?: 'Umum' }}
                                </div>
                            </div>

                        </div>

                        {{-- FASILITAS --}}
                        <div
                            class="facilities"
                            data-live-facilities
                        >
                            @forelse($room->facilities->take(5) as $facility)

                                <span class="facility-tag">
                                    {{ $facility->name }}

                                    @if(($facility->quantity ?? 1) > 1)
                                        ×{{ $facility->quantity }}
                                    @endif
                                </span>

                            @empty

                                <span class="facility-tag">
                                    Tidak ada fasilitas
                                </span>

                            @endforelse
                        </div>

                        {{-- JAM OPERASIONAL --}}
                        <div
                            style="
                                margin-top:.65rem;
                                font-size:.65rem;
                                color:var(--text-secondary);
                            "
                        >
                            <i
                                class="fas fa-clock"
                                style="color:#06b6d4;"
                            ></i>

                            {{ is_array($room->operational_hours)
                                ? count($room->operational_hours) . ' hari operasional'
                                : 'Jam operasional tersedia'
                            }}
                        </div>

                        {{-- BOOKING --}}
                        <div
                            class="booking-info {{ $booking ? 'has-booking' : 'no-booking' }}"
                            data-live-booking-box
                        >

                            @if($booking)

                                <i class="fas fa-calendar-check"></i>

                                Booking:

                                {{ $booking->start_datetime->format('H:i') }}
                                –
                                {{ $booking->end_datetime->format('H:i') }}

                            @else

                                <i class="fas fa-calendar-check"></i>

                                Tidak ada booking hari ini

                            @endif

                        </div>

                        {{-- =================================================
                             SCHEDULE
                        ================================================== --}}

                        <div class="schedule-section">

                            <div class="schedule-section-header">

                                <h4 class="schedule-title">
                                    <i
                                        class="fas fa-calendar-days"
                                        style="color:#06b6d4;"
                                    ></i>

                                    Jadwal Ruangan
                                </h4>

                                <span
                                    class="schedule-day"
                                    data-live-schedule-day
                                >
                                    -
                                </span>

                            </div>

                            <div
                                class="schedule-list"
                                data-live-schedules
                            >

                                @forelse($roomSchedules as $schedule)

                                    <div
                                        class="schedule-item"
                                        data-schedule-id="{{ $schedule->id }}"
                                    >

                                        <div class="schedule-time">
                                            {{ $schedule->start_datetime
                                                ? $schedule->start_datetime->format('H:i')
                                                : '--:--'
                                            }}

                                            –

                                            {{ $schedule->end_datetime
                                                ? $schedule->end_datetime->format('H:i')
                                                : '--:--'
                                            }}
                                        </div>

                                        <div>
                                            <p class="schedule-name">
                                                {{ $schedule->title }}
                                            </p>

                                            <p class="schedule-meta">
                                                {{ $schedule->activity_type ?: 'Umum' }}

                                                @if($schedule->status)
                                                    · {{ $schedule->status }}
                                                @endif
                                            </p>
                                        </div>

                                    </div>

                                @empty

                                    <div class="schedule-empty">
                                        Belum ada jadwal ruangan.
                                    </div>

                                @endforelse

                            </div>

                        </div>

                        <button
                            type="button"
                            class="btn-show show-public-room"
                        >
                            <i class="fas fa-eye"></i>

                            Lihat Detail Ruangan
                        </button>

                    </div>

                </article>

            @empty

                <div class="empty-state">

                    <div class="empty-icon">
                        <i class="fas fa-door-closed"></i>
                    </div>

                    <h3 class="empty-title">
                        Belum ada ruangan publik
                    </h3>

                    <p class="empty-description">
                        Ruangan yang sudah dipublikasikan akan
                        muncul di halaman ini.
                    </p>

                </div>

            @endforelse

        </div>

        <div class="pagination">
            {{ $rooms->links() }}
        </div>

    </section>

</main>

{{-- =============================================================
     MODAL
============================================================= --}}

<div
    id="roomModal"
    class="modal-overlay"
>

    <div class="modal-box">

        <div class="modal-header">

            <h3 id="modalRoomName">
                -
            </h3>

            <button
                id="closeModal"
                type="button"
                class="modal-close"
                title="Tutup"
            >
                <i class="fas fa-xmark"></i>
            </button>

        </div>

        <div
            id="modalContent"
            class="modal-body"
        >
            Memuat...
        </div>

    </div>

</div>

{{-- =============================================================
     SCROLL TOP
============================================================= --}}

<button
    id="scrollTopBtn"
    type="button"
    class="scroll-top"
    title="Kembali ke atas"
>
    <i class="fas fa-arrow-up"></i>
</button>

{{-- =============================================================
     ROOM DATA
============================================================= --}}

@php
    $roomsData = $rooms->getCollection()->map(function ($room) {

        $images = is_array($room->images)
            ? $room->images
            : [];

        return [
            'id' => $room->id,
            'name' => $room->name,
            'code' => $room->room_code,
            'token' => $room->room_token,
            'building' => $room->building,
            'floor' => $room->floor,
            'category' => $room->category,
            'capacity' => $room->capacity_normal,
            'status' => $room->status,
            'condition' => $room->condition,
            'images' => $images,

            'facilities' => $room->facilities
                ->map(function ($facility) {
                    return [
                        'name' => $facility->name,
                        'quantity' => $facility->quantity,
                    ];
                })
                ->values()
                ->all(),

            'operational_hours' => $room->operational_hours ?? [],
        ];
    })->values();
@endphp

<script>
    const initialRooms = @json($roomsData);
    const realtimeRouteTemplate = @json(
        route('room.public.realtime', ['token' => '__ROOM_TOKEN__'])
    );
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const roomGrid = document.getElementById('roomGrid');
    const qrFocus = document.getElementById('qrFocus');
    const qrFocusRoom = document.getElementById('qrFocusRoom');
    const clearQrFocus = document.getElementById('clearQrFocus');

    const modal = document.getElementById('roomModal');
    const modalContent = document.getElementById('modalContent');
    const modalName = document.getElementById('modalRoomName');
    const closeModal = document.getElementById('closeModal');

    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    const scrollBtn = document.getElementById('scrollTopBtn');

    const urlParams = new URLSearchParams(
        window.location.search
    );

    const qrRoomToken =
        urlParams.get('room_token');

    /*
    |--------------------------------------------------------------------------
    | ROOM HELPERS
    |--------------------------------------------------------------------------
    */

    function getCardByToken(token) {

        if (!token) {
            return null;
        }

        return document.querySelector(
            `.room-card[data-room-token="${CSS.escape(token)}"]`
        );
    }

    function getCardById(id) {

        return document.querySelector(
            `.room-card[data-room-id="${id}"]`
        );
    }

    function setQrFocus(token, roomName = 'Ruangan') {

        if (!token) {
            qrFocus?.classList.remove('active');
            return;
        }

        if (qrFocusRoom) {
            qrFocusRoom.textContent = roomName;
        }

        qrFocus?.classList.add('active');
    }

    /*
    |--------------------------------------------------------------------------
    | QR FOCUS MODE
    |--------------------------------------------------------------------------
    */

    if (qrRoomToken) {

        const selectedRoom =
            initialRooms.find(
                room => room.token === qrRoomToken
            );

        if (selectedRoom) {

            setQrFocus(
                qrRoomToken,
                selectedRoom.name
            );

            const card =
                getCardByToken(qrRoomToken);

            if (card) {

                card.classList.add(
                    'qr-focused'
                );

                setTimeout(() => {

                    card.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                }, 250);
            }
        }
    }

    clearQrFocus?.addEventListener(
        'click',
        () => {

            const cleanUrl =
                window.location.pathname;

            window.location.href = cleanUrl;
        }
    );

    /*
    |--------------------------------------------------------------------------
    | REALTIME
    |--------------------------------------------------------------------------
    */

    const realtimeTimers = {};

    function buildRealtimeUrl(token) {

        return realtimeRouteTemplate.replace(
            '__ROOM_TOKEN__',
            encodeURIComponent(token)
        );
    }

    async function loadRoomRealtime(card) {

        const token =
            card.dataset.roomToken;

        if (!token) {
            return;
        }

        try {

            const response =
                await fetch(
                    buildRealtimeUrl(token),
                    {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache',
                        },

                        cache: 'no-store',
                    }
                );

            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}`
                );
            }

            const data =
                await response.json();

            if (!data.success || !data.room) {
                return;
            }

            updateRoomCard(
                card,
                data.room
            );

        } catch (error) {

            console.error(
                `Realtime room ${token}:`,
                error
            );
        }
    }

    function updateRoomCard(
        card,
        room
    ) {

        /*
        |--------------------------------------------------------------------------
        | JAM
        |--------------------------------------------------------------------------
        */

        const clock =
            card.querySelector(
                '[data-live-clock]'
            );

        if (clock) {

            clock.textContent =
                new Date().toLocaleTimeString(
                    'id-ID',
                    {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    }
                );
        }

        /*
        |--------------------------------------------------------------------------
        | HARI
        |--------------------------------------------------------------------------
        */

        const day =
            card.querySelector(
                '[data-live-day]'
            );

        if (day) {

            day.textContent =
                new Intl.DateTimeFormat(
                    'id-ID',
                    {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                    }
                ).format(new Date());
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE TIME
        |--------------------------------------------------------------------------
        */

        const updated =
            card.querySelector(
                '[data-live-updated]'
            );

        if (updated) {

            updated.textContent =
                'Update ' +
                new Date().toLocaleTimeString(
                    'id-ID',
                    {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    }
                );
        }

        /*
        |--------------------------------------------------------------------------
        | LIVE STATUS
        |--------------------------------------------------------------------------
        */

        const liveStatus =
            room.live_status ??
            room.status ??
            '-';

        const status =
            card.querySelector(
                '[data-live-status]'
            );

        if (status) {
            status.textContent =
                liveStatus;
        }

        /*
        |--------------------------------------------------------------------------
        | BADGE STATUS
        |--------------------------------------------------------------------------
        */

        const badge =
            card.querySelector(
                '[data-live-badge]'
            );

        if (badge) {

            badge.classList.remove(
                'available',
                'in-use',
                'booked',
                'other'
            );

            if (liveStatus === 'Tersedia') {

                badge.classList.add(
                    'available'
                );

                badge.textContent =
                    '🟢 Tersedia';

            } else if (
                liveStatus === 'Sedang Digunakan'
            ) {

                badge.classList.add(
                    'in-use'
                );

                badge.textContent =
                    '🔵 Sedang Digunakan';

            } else if (
                liveStatus === 'Dipesan'
            ) {

                badge.classList.add(
                    'booked'
                );

                badge.textContent =
                    '🟡 Dipesan';

            } else {

                badge.classList.add(
                    'other'
                );

                badge.textContent =
                    liveStatus;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CONDITION
        |--------------------------------------------------------------------------
        */

        const condition =
            card.querySelector(
                '[data-live-condition]'
            );

        if (
            condition &&
            room.condition !== undefined
        ) {
            condition.textContent =
                room.condition || '-';
        }

        /*
        |--------------------------------------------------------------------------
        | BOOKING
        |--------------------------------------------------------------------------
        */

        const liveBooking =
            card.querySelector(
                '[data-live-booking]'
            );

        const bookingBox =
            card.querySelector(
                '[data-live-booking-box]'
            );

        if (room.current_booking) {

            const start =
                formatTime(
                    room.current_booking.start
                );

            const end =
                formatTime(
                    room.current_booking.end
                );

            if (liveBooking) {

                liveBooking.textContent =
                    `${start} – ${end}`;
            }

            if (bookingBox) {

                bookingBox.classList.remove(
                    'no-booking'
                );

                bookingBox.classList.add(
                    'has-booking'
                );

                bookingBox.innerHTML =
                    `<i class="fas fa-circle-play"></i>
                     Sedang digunakan · ${start} – ${end}`;
            }

        } else if (room.next_booking) {

            const start =
                formatTime(
                    room.next_booking.start
                );

            const end =
                formatTime(
                    room.next_booking.end
                );

            if (liveBooking) {

                liveBooking.textContent =
                    `${start} – ${end}`;
            }

            if (bookingBox) {

                bookingBox.classList.remove(
                    'no-booking'
                );

                bookingBox.classList.add(
                    'has-booking'
                );

                bookingBox.innerHTML =
                    `<i class="fas fa-calendar-check"></i>
                     Booking berikutnya · ${start} – ${end}`;
            }

        } else {

            if (liveBooking) {

                liveBooking.textContent =
                    'Tidak ada';
            }

            if (bookingBox) {

                bookingBox.classList.remove(
                    'has-booking'
                );

                bookingBox.classList.add(
                    'no-booking'
                );

                bookingBox.innerHTML =
                    `<i class="fas fa-calendar-check"></i>
                     Tidak ada booking`;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FACILITIES
        |--------------------------------------------------------------------------
        */

        const facilities =
            card.querySelector(
                '[data-live-facilities]'
            );

        if (
            facilities &&
            Array.isArray(room.facilities)
        ) {

            if (!room.facilities.length) {

                facilities.innerHTML =
                    `<span class="facility-tag">
                        Tidak ada fasilitas
                    </span>`;

            } else {

                facilities.innerHTML =
                    room.facilities
                        .slice(0, 5)
                        .map(item => {

                            const quantity =
                                Number(
                                    item.quantity ?? 1
                                );

                            return `
                                <span class="facility-tag">
                                    ${escapeHtml(item.name ?? '-')}

                                    ${
                                        quantity > 1
                                            ? ` ×${quantity}`
                                            : ''
                                    }
                                </span>
                            `;

                        })
                        .join('');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SCHEDULE
        |--------------------------------------------------------------------------
        */

        updateSchedules(
            card,
            room
        );

        /*
        |--------------------------------------------------------------------------
        | ROOM REMOVED / UNPUBLISHED
        |--------------------------------------------------------------------------
        */

        if (
            room.is_public === false ||
            room.is_published === false
        ) {

            const isFocused =
                card.dataset.roomToken === qrRoomToken;

            card.remove();

            if (isFocused) {

                setQrFocus(
                    null
                );
            }
        }
    }

    function updateSchedules(
        card,
        room
    ) {

        const scheduleContainer =
            card.querySelector(
                '[data-live-schedules]'
            );

        const scheduleDay =
            card.querySelector(
                '[data-live-schedule-day]'
            );

        if (!scheduleContainer) {
            return;
        }

        if (scheduleDay) {

            scheduleDay.textContent =
                new Intl.DateTimeFormat(
                    'id-ID',
                    {
                        weekday: 'long'
                    }
                ).format(new Date());
        }

        const schedules =
            Array.isArray(room.schedules)
                ? room.schedules
                : [];

        if (!schedules.length) {

            scheduleContainer.innerHTML =
                `<div class="schedule-empty">
                    Tidak ada jadwal publik.
                </div>`;

            return;
        }

        scheduleContainer.innerHTML =
            schedules
                .slice(0, 8)
                .map(schedule => {

                    return `
                        <div
                            class="schedule-item"
                            data-schedule-id="${schedule.id ?? ''}"
                        >

                            <div class="schedule-time">
                                ${formatTime(schedule.start)}
                                –
                                ${formatTime(schedule.end)}
                            </div>

                            <div>

                                <p class="schedule-name">
                                    ${escapeHtml(
                                        schedule.title ?? 'Jadwal'
                                    )}
                                </p>

                                <p class="schedule-meta">
                                    ${escapeHtml(
                                        schedule.activity_type ?? 'Umum'
                                    )}

                                    ${
                                        schedule.status
                                            ? ` · ${escapeHtml(schedule.status)}`
                                            : ''
                                    }
                                </p>

                            </div>

                        </div>
                    `;

                })
                .join('');
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT TIME
    |--------------------------------------------------------------------------
    */

    function formatTime(value) {

        if (!value) {
            return '--:--';
        }

        try {

            const normalized =
                String(value)
                    .replace(' ', 'T');

            const date =
                new Date(normalized);

            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {
                return '--:--';
            }

            return date.toLocaleTimeString(
                'id-ID',
                {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

        } catch {
            return '--:--';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HTML ESCAPE
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /*
    |--------------------------------------------------------------------------
    | START REALTIME PER CARD
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.room-card')
        .forEach(card => {

            loadRoomRealtime(card);

            const token =
                card.dataset.roomToken;

            realtimeTimers[token] =
                setInterval(
                    () => loadRoomRealtime(card),
                    3000
                );
        });

    /*
    |--------------------------------------------------------------------------
    | CLOCK ALL CARDS
    |--------------------------------------------------------------------------
    */

    setInterval(() => {

        document
            .querySelectorAll('.room-card')
            .forEach(card => {

                const clock =
                    card.querySelector(
                        '[data-live-clock]'
                    );

                if (!clock) {
                    return;
                }

                clock.textContent =
                    new Date().toLocaleTimeString(
                        'id-ID',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                        }
                    );
            });

    }, 1000);

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    function showRoom(room) {

        if (!room) {
            return;
        }

        modalName.textContent =
            room.name || 'Ruangan';

        const image =
            room.images?.[0]
                ? `/storage/${room.images[0]}`
                : null;

        const imageHtml =
            image
                ? `
                    <img
                        src="${image}"
                        alt="${escapeHtml(room.name)}"
                        class="modal-image"
                    >
                `
                : `
                    <div
                        class="modal-image"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:var(--text-secondary);
                            font-size:4rem;
                        "
                    >
                        <i class="fas fa-door-open"></i>
                    </div>
                `;

        const facilities =
            Array.isArray(room.facilities) &&
            room.facilities.length
                ? room.facilities.map(f => {

                    const quantity =
                        Number(
                            f.quantity ?? 1
                        );

                    return `
                        <span class="facility-tag">
                            ${escapeHtml(f.name)}
                            ${
                                quantity > 1
                                    ? ` ×${quantity}`
                                    : ''
                            }
                        </span>
                    `;

                }).join('')
                : `
                    <span
                        style="color:var(--text-secondary);"
                    >
                        Tidak tersedia
                    </span>
                `;

        const operationalHours =
            Object.entries(
                room.operational_hours || {}
            );

        const operationalHtml =
            operationalHours.length
                ? operationalHours
                    .map(([day, hour]) => `
                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                gap:.75rem;
                                padding:.4rem 0;
                                border-bottom:1px solid var(--border-color);
                            "
                        >
                            <span>${escapeHtml(day)}</span>
                            <strong>${escapeHtml(hour)}</strong>
                        </div>
                    `)
                    .join('')
                : `
                    Informasi jam operasional
                    tidak tersedia.
                `;

        const schedules =
            Array.isArray(room.schedules)
                ? room.schedules
                : [];

        const schedulesHtml =
            schedules.length
                ? schedules.map(schedule => `
                    <div
                        style="
                            display:grid;
                            grid-template-columns:80px 1fr;
                            gap:.65rem;
                            padding:.65rem;
                            margin-top:.45rem;
                            background:var(--bg-soft);
                            border-radius:12px;
                        "
                    >
                        <strong
                            style="
                                color:#2563eb;
                                font-size:.7rem;
                            "
                        >
                            ${formatTime(schedule.start)}
                            -
                            ${formatTime(schedule.end)}
                        </strong>

                        <div>
                            <div
                                style="
                                    font-size:.75rem;
                                    font-weight:900;
                                "
                            >
                                ${escapeHtml(schedule.title)}
                            </div>

                            <div
                                style="
                                    margin-top:.12rem;
                                    color:var(--text-secondary);
                                    font-size:.62rem;
                                "
                            >
                                ${escapeHtml(
                                    schedule.activity_type ?? 'Umum'
                                )}

                                ${
                                    schedule.status
                                        ? ` · ${escapeHtml(schedule.status)}`
                                        : ''
                                }
                            </div>
                        </div>
                    </div>
                `).join('')
                : `
                    <div
                        style="
                            margin-top:.5rem;
                            color:var(--text-secondary);
                            font-size:.75rem;
                        "
                    >
                        Tidak ada jadwal publik.
                    </div>
                `;

        modalContent.innerHTML = `
            ${imageHtml}

            <div class="modal-grid">

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Kode Ruangan
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(room.code ?? '-')}
                    </div>
                </div>

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Status
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(
                            room.live_status ??
                            room.status ??
                            '-'
                        )}
                    </div>
                </div>

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Kapasitas
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(room.capacity ?? '-')}
                    </div>
                </div>

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Lantai
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(room.floor ?? '-')}
                    </div>
                </div>

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Kategori
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(room.category ?? '-')}
                    </div>
                </div>

                <div class="modal-info-box">
                    <div class="modal-info-label">
                        Kondisi
                    </div>

                    <div class="modal-info-value">
                        ${escapeHtml(room.condition ?? '-')}
                    </div>
                </div>

            </div>

            <div style="margin-top:1rem;">
                <h4 style="margin:0;font-size:.88rem;">
                    Fasilitas
                </h4>

                <div
                    style="
                        display:flex;
                        flex-wrap:wrap;
                        gap:.4rem;
                        margin-top:.55rem;
                    "
                >
                    ${facilities}
                </div>
            </div>

            <div style="margin-top:1rem;">
                <h4 style="margin:0;font-size:.88rem;">
                    Jadwal Publik
                </h4>

                <div style="margin-top:.55rem;">
                    ${schedulesHtml}
                </div>
            </div>

            <div style="margin-top:1rem;">
                <h4 style="margin:0;font-size:.88rem;">
                    Jam Operasional
                </h4>

                <div
                    style="
                        margin-top:.55rem;
                        padding:.8rem;
                        background:var(--bg-soft);
                        border-radius:13px;
                        font-size:.72rem;
                    "
                >
                    ${operationalHtml}
                </div>
            </div>

            <div
                style="
                    margin-top:1rem;
                    padding:.8rem;
                    border-radius:13px;
                    background:#fef3c7;
                    color:#92400e;
                    border:1px solid #fcd34d;
                    font-size:.72rem;
                "
            >
                <i class="fas fa-circle-info"></i>

                Informasi ditampilkan sesuai
                pengaturan publikasi ruangan.
            </div>
        `;

        modal.classList.add('active');

        document.body.style.overflow =
            'hidden';
    }

    function closeRoomModal() {

        modal.classList.remove(
            'active'
        );

        document.body.style.overflow =
            '';
    }

    closeModal?.addEventListener(
        'click',
        closeRoomModal
    );

    modal?.addEventListener(
        'click',
        event => {

            if (
                event.target === modal
            ) {
                closeRoomModal();
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | ROOM CARD EVENTS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.room-card')
        .forEach(card => {

            const id =
                Number(
                    card.dataset.roomId
                );

            const room =
                initialRooms.find(
                    item => Number(item.id) === id
                );

            card
                .querySelector('.show-public-room')
                ?.addEventListener(
                    'click',
                    event => {

                        event.stopPropagation();

                        if (room) {
                            showRoom(room);
                        }
                    }
                );

            card.addEventListener(
                'dblclick',
                () => {

                    if (room) {
                        showRoom(room);
                    }
                }
            );

            card.addEventListener(
                'click',
                event => {

                    if (
                        window.innerWidth <= 768 &&
                        !event.target.closest('button')
                    ) {

                        if (room) {
                            showRoom(room);
                        }
                    }
                }
            );

        });

    /*
    |--------------------------------------------------------------------------
    | NIM SEARCH
    |--------------------------------------------------------------------------
    */

    const nimForm =
        document.getElementById(
            'nimForm'
        );

    nimForm?.addEventListener(
        'submit',
        async event => {

            event.preventDefault();

            const input =
                document.getElementById(
                    'nimInput'
                );

            const message =
                document.getElementById(
                    'nimMessage'
                );

            const nim =
                input.value.trim();

            if (!nim) {

                message.textContent =
                    'Silakan masukkan NIM.';

                message.style.display =
                    'block';

                return;
            }

            message.textContent =
                'Mencari ruangan...';

            message.style.display =
                'block';

            try {

                const response =
                    await fetch(
                        '{{ route('room.public.nim') }}',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'input[name="_token"]'
                                    )?.value,

                                'Accept':
                                    'application/json',
                            },

                            body: JSON.stringify({
                                nim
                            })
                        }
                    );

                const data =
                    await response.json();

                if (!data.success) {

                    message.textContent =
                        data.message ||
                        'Pencarian gagal.';

                    return;
                }

                if (
                    !Array.isArray(data.rooms) ||
                    !data.rooms.length
                ) {

                    message.textContent =
                        'Tidak ada ruangan publik yang sedang berlangsung.';

                    return;
                }

                message.textContent =
                    `${data.rooms.length} ruangan ditemukan.`;

                const target =
                    getCardById(
                        data.rooms[0].id
                    );

                if (target) {

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    target.classList.add(
                        'qr-focused'
                    );

                    setTimeout(() => {

                        target.classList.remove(
                            'qr-focused'
                        );

                    }, 2500);
                }

            } catch (error) {

                console.error(error);

                message.textContent =
                    'Terjadi kesalahan saat mencari ruangan.';
            }

        }
    );

    /*
    |--------------------------------------------------------------------------
    | THEME
    |--------------------------------------------------------------------------
    */

    function setTheme(theme) {

        if (theme === 'dark') {

            htmlElement.classList.add(
                'dark'
            );

            themeToggle.innerHTML =
                '<i class="fas fa-sun"></i>';

        } else {

            htmlElement.classList.remove(
                'dark'
            );

            themeToggle.innerHTML =
                '<i class="fas fa-moon"></i>';
        }

        localStorage.setItem(
            'theme',
            theme
        );
    }

    const savedTheme =
        localStorage.getItem('theme') ||
        'light';

    setTheme(savedTheme);

    themeToggle?.addEventListener(
        'click',
        () => {

            const current =
                htmlElement.classList.contains(
                    'dark'
                )
                    ? 'dark'
                    : 'light';

            setTheme(
                current === 'dark'
                    ? 'light'
                    : 'dark'
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | SCROLL TOP
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'scroll',
        () => {

            if (
                window.scrollY > 450
            ) {

                scrollBtn.classList.add(
                    'visible'
                );

            } else {

                scrollBtn.classList.remove(
                    'visible'
                );
            }

        }
    );

    scrollBtn?.addEventListener(
        'click',
        () => {

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

        }
    );

});
</script>

</body>
</html>