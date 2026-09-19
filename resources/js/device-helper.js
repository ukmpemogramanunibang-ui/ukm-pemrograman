/**
 * device-helper.js
 * ------------------------------------------------------------------
 * Auto-detect device → set class di <html>/<body> → expose helpers
 * untuk: breakpoint, swipe, tap cepat, orientasi, safe-area, motion.
 *
 * Dipakai oleh: seluruh layout (owner, anggota, publik, dll).
 * ------------------------------------------------------------------
 */

/* =========================================================
 * 1. KONSTANTA BREAKPOINT (sinkron dengan Tailwind defaults)
 * ======================================================= */
export const BREAKPOINTS = {
    xs: 0,
    sm: 640,
    md: 768,
    lg: 1024,
    xl: 1280,
    '2xl': 1536,
    '3xl': 1920,
};

/* =========================================================
 * 2. DETEKSI AWAL (dijalankan sekali saat modul di-load)
 * ======================================================= */
const ua = typeof navigator !== 'undefined' ? navigator.userAgent || '' : '';

const device = {
    // --- Platform ---
    isMobile:  /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua),
    isTablet:  /iPad|Android(?!.*Mobile)|Tablet|PlayBook|Silk/i.test(ua),
    isDesktop: !/Android|webOS|iPhone|iPod|iPad|BlackBerry|IEMobile|Opera Mini/i.test(ua),
    isIOS:     /iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
    isAndroid: /Android/i.test(ua),
    isWindows: /Windows/i.test(ua),
    isMac:     /Macintosh|Mac OS X/i.test(ua),
    isLinux:   /Linux/i.test(ua) && !/Android/i.test(ua),

    // --- Hardware ---
    isTouch:  ('ontouchstart' in window) || (navigator.maxTouchPoints > 0),
    isStylus: window.matchMedia?.('(pointer: coarse)').matches ?? false,
    pixelRatio: window.devicePixelRatio || 1,
    cores: navigator.hardwareConcurrency || 1,
    memoryGB: navigator.deviceMemory || 0,
    connection: navigator.connection?.effectiveType || 'unknown',

    // --- Preferensi user ---
    prefersReducedMotion: window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false,
    prefersDark:          window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false,
    prefersContrast:      window.matchMedia?.('(prefers-contrast: more)').matches ?? false,

    // --- Viewport ---
    width:  window.innerWidth,
    height: window.innerHeight,
    orientation: window.innerWidth > window.innerHeight ? 'landscape' : 'portrait',
    viewport: null, // diisi di updateViewport()
    breakpoint: null,
};

/* =========================================================
 * 3. HELPERS PUBLIK
 * ======================================================= */
export const isTouch       = () => device.isTouch;
export const isMobile      = () => device.isMobile;
export const isTablet      = () => device.isTablet;
export const isDesktop     = () => device.isDesktop;
export const getDevice     = () => ({ ...device });
export const getBreakpoint = () => device.breakpoint;
export const getViewport   = () => ({ ...device.viewport });

/**
 * Cek apakah viewport ≥ breakpoint tertentu.
 * contoh: isAtLeast('md') → true kalau lebar ≥ 768px
 */
export const isAtLeast = (bp) => {
    const min = BREAKPOINTS[bp];
    if (min === undefined) return false;
    return window.innerWidth >= min;
};

/**
 * Cek apakah viewport < breakpoint tertentu.
 */
export const isBelow = (bp) => {
    const min = BREAKPOINTS[bp];
    if (min === undefined) return false;
    return window.innerWidth < min;
};

/* =========================================================
 * 4. UPDATE VIEWPORT & KELAS DI <html>/<body>
 * ======================================================= */
function resolveBreakpoint(w) {
    if (w >= BREAKPOINTS['3xl']) return '3xl';
    if (w >= BREAKPOINTS['2xl']) return '2xl';
    if (w >= BREAKPOINTS.xl)    return 'xl';
    if (w >= BREAKPOINTS.lg)    return 'lg';
    if (w >= BREAKPOINTS.md)    return 'md';
    if (w >= BREAKPOINTS.sm)    return 'sm';
    return 'xs';
}

let rafId = null;
function scheduleViewportUpdate() {
    if (rafId) return;
    rafId = requestAnimationFrame(() => {
        rafId = null;
        updateViewport();
    });
}

function updateViewport() {
    const w = window.innerWidth;
    const h = window.innerHeight;

    device.width       = w;
    device.height      = h;
    device.orientation = w > h ? 'landscape' : 'portrait';
    device.breakpoint  = resolveBreakpoint(w);
    device.viewport = {
        width: w, height: h,
        isPortrait:  device.orientation === 'portrait',
        isLandscape: device.orientation === 'landscape',
        aspect: +(w / h).toFixed(3),
        isSmall:  w < 640,
        isMedium: w >= 640 && w < 1024,
        isLarge:  w >= 1024,
        isXL:     w >= 1280,
    };

    const html = document.documentElement;
    const body = document.body;

    // Simpan state di html sebagai atribut (untuk CSS selector)
    html.dataset.device    = device.isMobile ? 'mobile' : (device.isTablet ? 'tablet' : 'desktop');
    html.dataset.orientation = device.orientation;
    html.dataset.bp        = device.breakpoint;
    html.dataset.touch     = device.isTouch ? 'true' : 'false';

    if (body) {
        // Toggle class utama
        body.classList.toggle('is-mobile',  device.isMobile);
        body.classList.toggle('is-tablet',  device.isTablet);
        body.classList.toggle('is-desktop', device.isDesktop);
        body.classList.toggle('is-touch',   device.isTouch);
        body.classList.toggle('is-portrait',  device.viewport.isPortrait);
        body.classList.toggle('is-landscape', device.viewport.isLandscape);
        body.classList.toggle('has-reduced-motion', device.prefersReducedMotion);

        // Hapus bp-* lama
        [...body.classList].forEach(c => {
            if (c.startsWith('bp-')) body.classList.remove(c);
        });
        body.classList.add(`bp-${device.breakpoint}`);
    }

    // CSS var untuk JS → CSS
    html.style.setProperty('--vh', `${h * 0.01}px`);
    html.style.setProperty('--vw', `${w * 0.01}px`);
    html.style.setProperty('--dpr', String(device.pixelRatio));

    // Dispatch event
    window.dispatchEvent(new CustomEvent('device:viewport', { detail: { ...device } }));
}

/* =========================================================
 * 5. FAST TAP (hilangkan delay 300ms & hover-lock di mobile)
 * ======================================================= */
function enableFastTap() {
    if (!device.isTouch) return;

    // Pastikan viewport meta ada
    let meta = document.querySelector('meta[name="viewport"]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.name = 'viewport';
        document.head.appendChild(meta);
    }
    // Tambahin viewport-fit=cover → safe-area inset untuk iPhone notch
    if (!meta.content.includes('viewport-fit')) {
        meta.content = 'width=device-width, initial-scale=1, viewport-fit=cover';
    }

    // Hilangkan tap highlight & delay
    const style = document.createElement('style');
    style.textContent = `
        * { -webkit-tap-highlight-color: transparent; }
        button, a, [role="button"], input, select, textarea {
            touch-action: manipulation;
        }
    `;
    document.head.appendChild(style);

    // Fallback: dispatch click cepat untuk link/button
    document.addEventListener('touchstart', (e) => {
        const el = e.target.closest('a, button, [role="button"], label, input[type="checkbox"], input[type="radio"]');
        if (el) el.classList.add('is-touching');
    }, { passive: true });

    document.addEventListener('touchend', (e) => {
        document.querySelectorAll('.is-touching').forEach(el => el.classList.remove('is-touching'));
    }, { passive: true });
}

/* =========================================================
 * 6. SWIPE GESTURE
 * ======================================================= */
/**
 * attachSwipe(element, { onLeft, onRight, onUp, onDown, threshold=50, preventScroll=false })
 * Return: destroy()
 */
export function attachSwipe(el, opts = {}) {
    if (!el) return () => {};
    const {
        onLeft, onRight, onUp, onDown,
        threshold = 50,
        preventScroll = false,
    } = opts;

    let sx = 0, sy = 0, tracking = false;

    const start = (e) => {
        const t = e.touches ? e.touches[0] : e;
        sx = t.clientX; sy = t.clientY; tracking = true;
    };
    const move = (e) => {
        if (!tracking) return;
        if (preventScroll) e.preventDefault();
    };
    const end = (e) => {
        if (!tracking) return;
        tracking = false;
        const t = e.changedTouches ? e.changedTouches[0] : e;
        const dx = t.clientX - sx;
        const dy = t.clientY - sy;
        const ax = Math.abs(dx), ay = Math.abs(dy);

        if (Math.max(ax, ay) < threshold) return;

        if (ax > ay) {
            if (dx < 0 && onLeft)  onLeft({ dx, dy });
            if (dx > 0 && onRight) onRight({ dx, dy });
        } else {
            if (dy < 0 && onUp)    onUp({ dx, dy });
            if (dy > 0 && onDown)  onDown({ dx, dy });
        }
    };

    el.addEventListener('touchstart', start, { passive: true });
    el.addEventListener('touchmove',  move,  { passive: !preventScroll });
    el.addEventListener('touchend',   end,   { passive: true });
    el.addEventListener('touchcancel', () => { tracking = false; }, { passive: true });

    return () => {
        el.removeEventListener('touchstart', start);
        el.removeEventListener('touchmove',  move);
        el.removeEventListener('touchend',   end);
    };
}

/* =========================================================
 * 7. SAFE AREA (iPhone notch / Android cutout)
 * ======================================================= */
function applySafeArea() {
    const style = document.createElement('style');
    style.textContent = `
        .safe-top    { padding-top:    env(safe-area-inset-top, 0); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0); }
        .safe-left   { padding-left:   env(safe-area-inset-left, 0); }
        .safe-right  { padding-right:  env(safe-area-inset-right, 0); }
        .safe-x      { padding-left: env(safe-area-inset-left, 0);
                       padding-right: env(safe-area-inset-right, 0); }
        .safe-all    { padding: env(safe-area-inset-top, 0)
                               env(safe-area-inset-right, 0)
                               env(safe-area-inset-bottom, 0)
                               env(safe-area-inset-left, 0); }
    `;
    document.head.appendChild(style);
}

/* =========================================================
 * 8. UTIL: min-height touch target (untuk aksesibilitas)
 * ======================================================= */
function applyTouchTargets() {
    if (!device.isTouch) return;
    const style = document.createElement('style');
    style.textContent = `
        button, a, [role="button"], input[type="submit"] {
            min-height: 40px;
        }
        button.icon-only, a.icon-only {
            min-width: 40px; min-height: 40px;
            display: inline-grid; place-items: center;
        }
    `;
    document.head.appendChild(style);
}

/* =========================================================
 * 9. INIT
 * ======================================================= */
export function initDeviceHelper() {
    updateViewport();
    enableFastTap();
    applySafeArea();
    applyTouchTargets();

    window.addEventListener('resize',            scheduleViewportUpdate, { passive: true });
    window.addEventListener('orientationchange', scheduleViewportUpdate, { passive: true });
    window.addEventListener('pageshow',          scheduleViewportUpdate, { passive: true });

    // Media query listeners (reduced motion, dark, dll)
    window.matchMedia?.('(prefers-reduced-motion: reduce)').addEventListener?.('change', (e) => {
        device.prefersReducedMotion = e.matches;
        document.body.classList.toggle('has-reduced-motion', e.matches);
    });
}

/* =========================================================
 * 10. AUTO-INIT saat DOM siap
 * ======================================================= */
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeviceHelper, { once: true });
    } else {
        initDeviceHelper();
    }
}

/* =========================================================
 * 11. EXPOSE GLOBAL (opsional, untuk debug & inline script)
 * ======================================================= */
if (typeof window !== 'undefined') {
    window.UnicodeDevice = {
        device, isAtLeast, isBelow, isTouch, isMobile, isTablet, isDesktop,
        getBreakpoint, getViewport, getDevice,
        attachSwipe, initDeviceHelper,
        BREAKPOINTS,
    };
}

export default {
    device, isAtLeast, isBelow, isTouch, isMobile, isTablet, isDesktop,
    getBreakpoint, getViewport, getDevice, attachSwipe,
    initDeviceHelper, BREAKPOINTS,
};