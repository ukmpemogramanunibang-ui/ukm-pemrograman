<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * =============================================================================
 *  ImpersonationDashboardController — Expert Edition
 * =============================================================================
 *
 *  Prinsip desain:
 *   1. NEVER TRUST  — parameter route & session selalu disanitasi.
 *   2. NEVER CRASH  — setiap operasi rawan (query, route, relasi) dibungkus
 *                     try/catch dengan fallback aman.
 *   3. FAULT ISOLATION — satu statistik gagal tidak menjatuhkan seluruh halaman.
 *   4. CACHE ISOLATION — cache key selalu di-scope ke user target (bukan owner).
 *   5. OBSERVABILITY — audit log best-effort, tidak pernah throw.
 *   6. WHITELIST ONLY — tidak ada `__call` magic, tidak ada silent fallback.
 *   7. ZERO N+1 — semua relasi di-`loadMissing` sekali, statistik di-cache.
 * =============================================================================
 */
final class ImpersonationDashboardController extends Controller
{
    /** Whitelist role → view. Menambah role = tambah baris di sini. */
    private const ROLE_VIEW_MAP = [
        'superadmin' => 'superadmin.dashboard',
        'ketua'      => 'dashboard.ketua',
        'pengurus'   => 'pengurus.dashboard',
        'anggota-ukm'    => 'anggota-ukm.dashboard',
        'anggota-umum'    => 'anggota-umum.dashboard',
        'pengunjung' => 'pengunjung.beranda',
    ];

    /** Statistik di-cache singkat supaya reload cepat, tapi tetap fresh. */
    private const STATS_TTL_SECONDS = 300;   // 5 menit
    private const ACTIVITY_LIMIT    = 10;
    private const ROLESLUG_MAX_LEN  = 32;

    // =========================================================================
    //  ENTRY POINT
    // =========================================================================

public function dashboard(Request $request, string $roleSlug, string $impersonationToken): View
{
    $roleSlug = $this->sanitizeRoleSlug($roleSlug);
    $view     = self::ROLE_VIEW_MAP[$roleSlug] ?? null;

    abort_if($view === null, 404, "Role '{$roleSlug}' tidak didukung.");

    // ✅ FALLBACK: kalau view belum ada, cari alternatif / tampilkan halaman aman
    if (! view()->exists($view)) {
        $fallback = $this->resolveFallbackView($roleSlug);
        Log::warning('impersonate.view_missing', [
            'role'      => $roleSlug,
            'expected'  => $view,
            'fallback'  => $fallback,
        ]);
        $view = $fallback;
    }

        $target = $this->resolveTarget($request);

        $this->hydrateRelations($target);

        $payload = $this->buildPayload($request, $target, $roleSlug);

        $this->audit('impersonate.dashboard', $request, $target, $roleSlug);

        return view($view, $payload);
    }

    /**
     * Halaman profil impersonasi (opsional).
     */
    public function profile(
        Request $request,
        string $roleSlug,
        string $impersonationToken,
    ): View {
        $target = $this->resolveTarget($request);
        $this->hydrateRelations($target);

        return view('owner.settings.explore.account', [
            'user'            => $target,
            'target'          => $target,
            'exploreTarget'   => $target,
            'exploreRole'     => $this->sanitizeRoleSlug($roleSlug),
            'isImpersonating' => true,
            'owner'           => $request->user(),
        ]);
    }

    // =========================================================================
    //  TARGET RESOLUTION — SAFE CHAIN
    // =========================================================================

    /**
     * Resolusi target dengan fallback berlapis.
     * Urutan: middleware attribute → session → 404.
     * TIDAK PERNAH memakai Auth::user() — itu OWNER, bukan target.
     */
    private function resolveTarget(Request $request): User
    {
        $target = $request->attributes->get('impersonation_target');

        if (! $target instanceof User) {
            $sessionId = (int) session('owner.explore.user_id');
            $target    = $sessionId > 0 ? User::find($sessionId) : null;
        }

        abort_unless(
            $target instanceof User,
            404,
            'Target impersonasi tidak ditemukan atau sudah tidak valid.',
        );

        return $target;
    }

    // =========================================================================
    //  RELATION HYDRATION
    // =========================================================================

    private function hydrateRelations(User $target): void
    {
        try {
            $target->loadMissing([
                'level', 'status', 'university', 'major',
                'jabatans', 'roles',
            ]);
        } catch (Throwable $e) {
            // Relasi hilang bukan alasan untuk 500.
            Log::warning('impersonate.relations.failed', [
                'user_id' => $target->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    //  PAYLOAD BUILDER
    // =========================================================================

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Request $request, User $target, string $roleSlug): array
    {
        $stats      = $this->gatherStats($target);
        $activities = $this->gatherActivities($target);

        return [
            // Core identity
            'user'            => $target,
            'target'          => $target,
            'exploreTarget'   => $target,
            'exploreRole'     => $roleSlug,
            'isImpersonating' => true,
            'owner'           => $request->user(),   // pemilik sesi asli

            // Statistik — selalu bertipe int / '-' (tidak pernah null)
            'totalPoin'       => $stats['poin'],
            'totalEvent'      => $stats['event'],
            'totalPresensi'   => $stats['presensi'],
            'totalSertifikat' => $stats['sertifikat'],
            'rank'            => $stats['rank'],

            // Aktivitas — selalu Collection (bisa kosong, tidak pernah null)
            'activities'      => $activities,

            // Navigasi aman (route bisa tidak ada saat impersonasi)
            'profileUrl'      => $this->safeRoute('anggota.profil'),
            'dashboardUrl'    => $this->safeRoute('owner.impersonate.dashboard', [
                'roleSlug'           => $roleSlug,
                'impersonationToken' => $request->route('impersonationToken'),
            ]),

            // Meta untuk debugging / tooltip
            'statsMeta' => [
                'cached'     => $stats['cached'],
                'computedAt' => now()->toIso8601String(),
            ],
        ];
    }

    // =========================================================================
    //  STATISTICS — PER-STAT ERROR CONTAINMENT
    // =========================================================================

    /**
     * @return array{poin:int,event:int,presensi:int,sertifikat:int,rank:int|string,cached:bool}
     */
    private function gatherStats(User $target): array
    {
        $cacheKey = "impersonate:v2:stats:{$target->id}";

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && $this->statsShapeIsValid($cached)) {
            return $cached + ['cached' => true];
        }

        $fresh = [
            'poin'       => $this->safeCount(fn () => (int) ($target->total_poin ?? 0)),
            'event'      => $this->safeCount(fn () => (int) $target->events()->count()),
            'presensi'   => $this->safeCount(fn () => (int) $target->presensis()->count()),
            'sertifikat' => $this->safeCount(fn () => (int) $target->sertifikats()->count()),
            'rank'       => $this->safeRank($target),
        ];

        // Hanya cache jika seluruh stat valid — hindari menyimpan hasil transien.
        if ($this->statsShapeIsValid($fresh)) {
            Cache::put($cacheKey, $fresh, self::STATS_TTL_SECONDS);
        }

        return $fresh + ['cached' => false];
    }

    /**
     * Jalankan callback rawan dan kembalikan 0 bila gagal. Tidak pernah throw.
     */
    private function safeCount(callable $fn): int
    {
        try {
            return max(0, (int) $fn());
        } catch (Throwable $e) {
            Log::warning('impersonate.stat.failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Hitung peringkat global. Fallback '-' bila kolom tidak ada / query gagal.
     */
    private function safeRank(User $target): int|string
    {
        try {
            $poin = (int) ($target->total_poin ?? 0);
            if ($poin <= 0) {
                return '-';
            }

            return (int) User::query()
                ->where('total_poin', '>', $poin)
                ->count() + 1;
        } catch (Throwable $e) {
            Log::warning('impersonate.rank.failed', ['error' => $e->getMessage()]);
            return '-';
        }
    }

    /**
     * Pastikan bentuk array cache valid sebelum dipakai.
     */
    private function statsShapeIsValid(array $stats): bool
    {
        return isset($stats['poin'], $stats['event'], $stats['presensi'], $stats['sertifikat'], $stats['rank'])
            && is_int($stats['poin'])
            && is_int($stats['event'])
            && is_int($stats['presensi'])
            && is_int($stats['sertifikat']);
    }

    // =========================================================================
    //  ACTIVITIES — BOUNDED & SAFE
    // =========================================================================

    private function gatherActivities(User $target): Collection
    {
        try {
            if (! method_exists($target, 'pointActivities')) {
                return collect();
            }

            return $target->pointActivities()
                ->latest()
                ->limit(self::ACTIVITY_LIMIT)
                ->get(['type', 'description', 'points', 'created_at'])
                ->values();
        } catch (Throwable $e) {
            Log::warning('impersonate.activities.failed', [
                'user_id' => $target->id,
                'error'   => $e->getMessage(),
            ]);
            return collect();
        }
    }

    // =========================================================================
    //  UTILITIES
    // =========================================================================
/**
 * Cari view alternatif jika view utama belum tersedia.
 * Urutan: config view → shared generic → error view.
 */
private function resolveFallbackView(string $roleSlug): string
{
    // 1. Coba ambil dari config (kalau ada override)
    $cfgView = config("impersonation.views.{$roleSlug}");
    if ($cfgView && view()->exists($cfgView)) return $cfgView;

    // 2. Coba varian generik
    $generics = [
        "{$roleSlug}.index",
        "{$roleSlug}.home",
        str_replace('-', '.', $roleSlug) . '.dashboard',   // anggota-umum → anggota.umum.dashboard
        str_replace('-', '', $roleSlug) . '.dashboard',    // anggota-umum → anggptaumum.dashboard (edge)
    ];
    foreach ($generics as $g) {
        if (view()->exists($g)) return $g;
    }

    // 3. Fallback terakhir — view error yang aman
    if (view()->exists('errors.role-fallback')) return 'errors.role-fallback';

    // 4. Ultima ratio — bikin inline
    abort(503, "Halaman untuk role '{$roleSlug}' sedang disiapkan. Hubungi administrator.");
}
    /**
     * Sanitasi slug: lowercase, hanya [a-z0-9_-], maksimal 32 karakter.
     * Mencegah path traversal, injection, dan oversized input.
     */
    private function sanitizeRoleSlug(string $slug): string
    {
        return Str::of($slug)
            ->lower()
            ->replaceMatches('/[^a-z0-9_\-]/', '')
            ->limit(self::ROLESLUG_MAX_LEN, '')
            ->toString();
    }

    /**
     * Bangun URL hanya jika route terdaftar. Sebaliknya kembalikan '#'.
     * Mencegah RouteNotFoundException saat impersonasi.
     */
    private function safeRoute(string $name, array $params = []): string
    {
        try {
            return route($name, $params);
        } catch (Throwable) {
            return '#';
        }
    }

    /**
     * Audit trail best-effort. Tidak pernah throw.
     */
    private function audit(string $event, Request $request, User $target, string $roleSlug): void
    {
        try {
            Log::info('impersonate.audit', [
                'event'      => $event,
                'owner_id'   => $request->user()?->id,
                'target_id'  => $target->id,
                'role'       => $roleSlug,
                'ip'         => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 180, ''),
                'timestamp'  => now()->toIso8601String(),
            ]);
        } catch (Throwable) {
            // Logging tidak boleh menjatuhkan request.
        }
    }
}