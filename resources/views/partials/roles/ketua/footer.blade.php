    {{-- ═══════════════════════════ CLOSE LAYOUT ═══════════════════════════ --}}
</div>{{-- /#content-column --}}
</div>{{-- /#app-wrapper --}}

<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       SIDEBAR TOGGLE — satu fungsi untuk semua device
       ═══════════════════════════════════════════════════════════ */
    const html        = document.documentElement;
    const toggleBtn   = document.getElementById('sidebar-toggle');
    const overlay     = document.getElementById('sidebar-overlay');
    const darkToggle  = document.getElementById('dark-toggle');
    const mqDesktop   = window.matchMedia('(min-width: 1024px)');

    function isClosed() {
        return html.classList.contains('sidebar-closed');
    }

    function setState(state, persist) {
        if (state === 'closed') {
            html.classList.add('sidebar-closed');
            document.body.style.overflow = '';
        } else {
            html.classList.remove('sidebar-closed');
            // Kunci scroll body HANYA di mobile saat sidebar terbuka
            document.body.style.overflow = mqDesktop.matches ? '' : 'hidden';
        }
        if (persist) {
            try { localStorage.setItem('sidebar_state', state); } catch (e) {}
        }
    }

    function toggleSidebar() {
        setState(isClosed() ? 'open' : 'closed', true);
    }

    /* Init — kalau belum ada state tersimpan, tentukan default sesuai device */
    try {
        const stored = localStorage.getItem('sidebar_state');
        if (!stored) {
            setState(mqDesktop.matches ? 'open' : 'closed', false);
        } else {
            // pastikan body overflow konsisten
            setState(stored, false);
        }
    } catch (e) {
        setState(mqDesktop.matches ? 'open' : 'closed', false);
    }

    /* Klik tombol header */
    toggleBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });

    /* Klik overlay = tutup */
    overlay?.addEventListener('click', function () {
        setState('closed', true);
    });

    /* ESC = tutup */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !isClosed()) {
            setState('closed', true);
        }
    });

    /* ⭐ Ctrl+B / Cmd+B = toggle */
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
            e.preventDefault();
            toggleSidebar();
        }
    });

    /* Saat resize dari desktop <-> mobile, sesuaikan state & body lock */
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (mqDesktop.matches) {
                // Masuk desktop: body scroll dibebaskan
                document.body.style.overflow = '';
                // Kalau user belum pernah pilih, default open
                if (!localStorage.getItem('sidebar_state')) {
                    setState('open', false);
                }
            } else {
                // Masuk mobile: kalau state open, kunci scroll
                if (!isClosed()) document.body.style.overflow = 'hidden';
                else document.body.style.overflow = '';
            }
        }, 100);
    });

    /* ═══════════════════════════════════════════════════════════
       DARK MODE TOGGLE
       ═══════════════════════════════════════════════════════════ */
    darkToggle?.addEventListener('click', function () {
        const isDark = html.classList.toggle('dark');
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
    });

    /* ═══════════════════════════════════════════════════════════
       AUTO-HIGHLIGHT LINK (fallback kalau routeIs gagal)
       ═══════════════════════════════════════════════════════════ */
    const currentPath = window.location.pathname;
    document.querySelectorAll('#sidebar-menu a[href]:not([href="#"])').forEach(function (link) {
        try {
            const url = new URL(link.href);
            if (url.pathname !== '/' && currentPath.startsWith(url.pathname)) {
                link.classList.add('bg-purple-100/70', 'dark:bg-purple-900/30', 'text-purple-700', 'dark:text-purple-300');
            }
        } catch (e) {}
    });

    /* ═══════════════════════════════════════════════════════════
       Expose helper (dipakai tombol logout dsb.)
       ═══════════════════════════════════════════════════════════ */
    window.toggleSidebar = toggleSidebar;
})();
</script>

{{-- Modal logout (placeholder — sesuaikan dengan milik Anda) --}}
<script>
function showLogoutModal(e) {
    if (e) e.preventDefault();
    if (confirm('Yakin ingin keluar dari akun?')) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = '{{ route("logout") }}';
        const t = document.createElement('input');
        t.type = 'hidden'; t.name = '_token'; t.value = '{{ csrf_token() }}';
        f.appendChild(t);
        document.body.appendChild(f);
        f.submit();
    }
}
</script>

</body>
</html>