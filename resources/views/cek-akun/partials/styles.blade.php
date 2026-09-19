<style>
    :root {
        --check-border: rgba(148, 163, 184, .14);
    }
    body { overflow-x: hidden; }

    .account-check-page {
        min-height: 100vh;
        position: relative;
        isolation: isolate;
        background:
            radial-gradient(circle at 15% 10%, rgba(59, 130, 246, .13), transparent 28%),
            radial-gradient(circle at 85% 15%, rgba(168, 85, 247, .13), transparent 30%),
            radial-gradient(circle at 50% 100%, rgba(6, 182, 212, .10), transparent 35%);
    }
    .account-check-page::before {
        content: ""; position: absolute; inset: 0; pointer-events: none; z-index: -1;
        background-image:
            linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
        background-size: 42px 42px;
        mask-image: linear-gradient(to bottom, black, transparent 75%);
    }

    .check-shell { width: min(1280px, calc(100% - 24px)); margin-inline: auto; }

    .glass-panel {
        background: rgba(15, 23, 42, .72);
        border: 1px solid var(--check-border);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        box-shadow: 0 20px 60px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.035);
    }

    .method-card {
        position: relative;
        overflow: hidden;
        cursor: pointer;
        text-align: center;
        transition: transform .3s cubic-bezier(.16,1,.3,1),
                    border-color .3s ease,
                    box-shadow .3s ease;
    }
    .method-card:hover {
        transform: translateY(-6px);
        border-color: rgba(96, 165, 250, .35);
        box-shadow: 0 30px 80px rgba(2, 6, 23, .35);
    }
    .method-card::after {
        content: ""; position: absolute; inset: auto -50px -80px auto;
        width: 220px; height: 220px; border-radius: 999px;
        background: radial-gradient(circle, rgba(59,130,246,.18), transparent 70%);
        pointer-events: none; transition: opacity .3s;
        opacity: 0;
    }
    .method-card:hover::after { opacity: 1; }

    .method-icon {
        width: 68px; height: 68px; border-radius: 22px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 16px;
        transition: transform .3s ease;
    }
    .method-card:hover .method-icon { transform: scale(1.08) rotate(-4deg); }

    .result-card { animation: resultIn .45s cubic-bezier(.16,1,.3,1); }
    @keyframes resultIn {
        from { opacity: 0; transform: translateY(18px) scale(.985); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .status-dot { width: 10px; height: 10px; border-radius: 999px; flex: 0 0 auto; }

    .camera-preview {
        min-height: 240px;
        background: linear-gradient(145deg, rgba(15,23,42,.95), rgba(30,41,59,.82));
    }
    .scan-corners::before, .scan-corners::after {
        content: ""; position: absolute; width: 55px; height: 55px; border-color: rgba(103,232,249,.9);
    }
    .scan-corners::before { left:20px; top:20px; border-left:3px solid; border-top:3px solid; border-radius:12px 0 0 0; }
    .scan-corners::after  { right:20px; bottom:20px; border-right:3px solid; border-bottom:3px solid; border-radius:0 0 12px 0; }

    .field {
        width: 100%; border-radius: 16px; padding: 13px 15px;
        background: rgba(2, 6, 23, .56);
        border: 1px solid rgba(148,163,184,.15);
        color: white; outline: none; transition: .2s ease;
    }
    .field:focus { border-color: rgba(96,165,250,.55); box-shadow: 0 0 0 4px rgba(59,130,246,.10); }

    .primary-btn, .secondary-btn, .danger-btn {
        border-radius: 14px; padding: 11px 15px; font-weight: 700;
        transition: .2s ease; cursor: pointer;
    }
    .primary-btn {
        color: white; background: linear-gradient(135deg, #2563eb, #7c3aed);
        box-shadow: 0 10px 30px rgba(37,99,235,.22);
        border: none;
    }
    .primary-btn:hover { transform: translateY(-1px); box-shadow: 0 15px 35px rgba(37,99,235,.30); }
    .secondary-btn { color: #dbeafe; background: rgba(30,41,59,.65); border: 1px solid rgba(148,163,184,.14); }
    .secondary-btn:hover { background: rgba(51,65,85,.8); }
    .danger-btn { color: #fecaca; background: rgba(127,29,29,.25); border: 1px solid rgba(248,113,113,.18); }

    .state-good    { background: rgba(16,185,129,.12); color:#6ee7b7; border-color: rgba(16,185,129,.18); }
    .state-info    { background: rgba(59,130,246,.12); color:#93c5fd; border-color: rgba(59,130,246,.18); }
    .state-warning { background: rgba(245,158,11,.12); color:#fcd34d; border-color: rgba(245,158,11,.18); }
    .state-danger  { background: rgba(239,68,68,.12);  color:#fca5a5; border-color: rgba(239,68,68,.18); }

    .avatar-ring {
        padding: 4px; border-radius: 999px;
        background: linear-gradient(135deg, rgba(96,165,250,.85), rgba(168,85,247,.85), rgba(34,211,238,.85));
    }
    .avatar-main { width: 120px; height: 120px; object-fit: cover; border-radius: 999px; display:block; background:#0f172a; }
    .avatar-fallback { display:flex; align-items:center; justify-content:center; }

    .stat-box { border-radius: 18px; background: rgba(2,6,23,.38); border: 1px solid rgba(148,163,184,.10); }
    .smart-line { border-left: 3px solid rgba(96,165,250,.65); }
    .empty-result { min-height: 220px; display:grid; place-items:center; }

    .breadcrumb-link {
        transition: color .2s;
    }
    .breadcrumb-link:hover { color: #67e8f9; }

    @media (max-width: 768px) {
        .check-shell { width: min(100% - 14px, 1280px); }
        .avatar-main { width: 92px; height: 92px; }
        .method-icon { width: 60px; height: 60px; border-radius: 18px; font-size: 1.4rem; }
    }
</style>