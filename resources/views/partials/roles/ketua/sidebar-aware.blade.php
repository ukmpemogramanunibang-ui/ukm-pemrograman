@once
@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════
       SIDEBAR-AWARE PAGE SHELL
       Auto-expand konten ketika sidebar collapse
       ═══════════════════════════════════════════════════════════ */
    .page-shell {
        width: 100%;
        min-width: 0;
        transition: padding .3s cubic-bezier(.4, 0, .2, 1);
    }

    .page-content-narrow {
        max-width: 80rem; /* 1280px */
        margin-left: auto;
        margin-right: auto;
        transition: max-width .35s cubic-bezier(.4, 0, .2, 1);
    }

    /* Mode collapsed — sidebar tersembunyi → konten melebar maksimal */
    body.sidebar-collapsed .page-content-narrow {
        max-width: 100%;
    }

    /* Mode collapsed — grid stats jadi 2 kolom (kiri+kanan) */
    body.sidebar-collapsed .responsive-aside-grid {
        grid-template-columns: 1fr 380px !important;
    }

    body.sidebar-collapsed .stats-grid-4 {
        grid-template-columns: repeat(4, 1fr) !important;
    }

    @media (max-width: 1023px) {
        .responsive-aside-grid { grid-template-columns: 1fr !important; }
        .stats-grid-4 { grid-template-columns: repeat(2, 1fr) !important; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════
       SIDEBAR WATCHER
       Deteksi sidebar collapse via ResizeObserver + MutationObserver
       Tambah/hapus class `sidebar-collapsed` di <body>
       ═══════════════════════════════════════════════════════════ */
    function initSidebarWatcher() {
        const sidebar = document.getElementById('sidebar');
        if (! sidebar) return;

        let rafId = null;
        let lastState = null;

        function computeCollapsed() {
            const style = window.getComputedStyle(sidebar);
            const width = sidebar.getBoundingClientRect().width;
            const isHidden = style.display === 'none'
                || style.visibility === 'hidden'
                || width < 80
                || sidebar.classList.contains('collapsed')
                || sidebar.classList.contains('hidden')
                || document.body.classList.contains('sidebar-hidden');
            return isHidden;
        }

        function apply() {
            const collapsed = computeCollapsed();
            if (collapsed === lastState) return;
            lastState = collapsed;

            document.body.classList.toggle('sidebar-collapsed', collapsed);
            document.documentElement.style.setProperty(
                '--sidebar-current-width',
                collapsed ? '0px' : sidebar.offsetWidth + 'px'
            );

            // Dispatch event untuk komponen lain
            window.dispatchEvent(new CustomEvent('sidebar:state', {
                detail: { collapsed },
            }));
        }

        function schedule() {
            if (rafId) cancelAnimationFrame(rafId);
            rafId = requestAnimationFrame(apply);
        }

        // Size changes
        if ('ResizeObserver' in window) {
            const ro = new ResizeObserver(schedule);
            ro.observe(sidebar);
        } else {
            window.addEventListener('resize', schedule);
        }

        // Attribute changes (class/style)
        const mo = new MutationObserver(schedule);
        mo.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class', 'style', 'hidden'],
        });
        mo.observe(document.body, {
            attributes: true,
            attributeFilter: ['class'],
        });

        // Init
        schedule();

        // Expose manual trigger
        window.__refreshSidebarState = schedule;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarWatcher);
    } else {
        initSidebarWatcher();
    }
})();
</script>
@endpush
@endonce