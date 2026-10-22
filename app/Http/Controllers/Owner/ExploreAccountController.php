<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationToken;
use App\Models\User;
use App\Services\Impersonation\ImpersonationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExploreAccountController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     | KONSTANTA — SSOT (Single Source of Truth)
     * ═══════════════════════════════════════════════════════════ */

    /** Sesi explorer (legacy, dipertahankan untuk kompat). */
    private const SESSION_KEY = 'owner_explorer';

    /** Kunci sesi impersonation aktif (dipakai oleh RequireImpersonation). */
    private const IMPERSONATION_CURRENT = 'impersonation_current';

    /** Kunci sesi lama — auto-cleanup saat exit. */
    private const LEGACY_IMPERSONATE_KEYS = [
        'impersonation',
        'impersonate_active',
        'impersonate_target_id',
        'impersonate_role_slug',
        'impersonated_user_id',
        'impersonate_user_id',
        'explore_user_id',
        'target_user_id',
        'role_slug',
        'impersonator_id',
        'owner_explorer',
        'owner_explorer_fp',
    ];

    /** Konfigurasi timeouts & limits. */
    private const SESSION_TIMEOUT_MIN = 30;
    private const MAX_ATTEMPTS        = 5;
    private const LOCKOUT_MINUTES     = 15;
    private const ATTEMPT_WINDOW_SEC  = 900;    // 15 menit sliding window
    private const SEARCH_LIMIT        = 25;
    private const MAX_KEYWORD_LEN     = 100;

    /* ═══════════════════════════════════════════════════════════
     | LEVEL ↔ SLUG MAP
     * ═══════════════════════════════════════════════════════════ */

    private const LEVEL_SLUG = [
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus',
        5 => 'anggota',
        6 => 'pengunjung',
    ];

    private const SLUG_LEVEL = [
        'owner'      => 1,
        'superadmin' => 2,
        'ketua'      => 3,
        'pengurus'   => 4,
        'anggota'    => 5,
        'pengunjung' => 6,
    ];

    /* ═══════════════════════════════════════════════════════════
     | AUTHORIZATION
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Pastikan user yang login adalah Owner (level_id = 1).
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeOwner(): User
    {
        $user = auth()->user();

        abort_unless($user, 401, 'Silakan login terlebih dahulu.');

        abort_unless(
            (int) $user->level_id === 1,
            403,
            'Fitur Jelajahi Akun hanya dapat digunakan oleh Owner.'
        );

        return $user;
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — URL RESOLVER (FAILSAFE)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Resolver URL yang aman. Coba `route()` dulu,
     * fallback ke URL literal kalau route tidak terdaftar.
     */
    private function safeUrl(string $routeName, string $fallbackUrl = '/', array $params = []): string
    {
        try {
            if (Route::has($routeName)) {
                return route($routeName, $params);
            }
        } catch (\Throwable $e) {
            $this->log('url_resolver_failed', [
                'route' => $routeName,
                'error' => $e->getMessage(),
            ], 'debug');
        }

        return url($fallbackUrl);
    }

    /**
     * Redirect aman + pesan flash.
     */
    private function safeRedirect(string $routeName, string $fallbackUrl, array $params = [], string $flashType = null, string $flashMsg = null): RedirectResponse
    {
        $response = redirect($this->safeUrl($routeName, $fallbackUrl, $params));

        if ($flashType && $flashMsg) {
            $response->with($flashType, $flashMsg);
        }

        return $response;
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — ROLE MAPPING
     * ═══════════════════════════════════════════════════════════ */

    private function levelToSlug(int $levelId): string
    {
        return self::LEVEL_SLUG[$levelId] ?? 'user';
    }

    private function slugToLevel(string $slug): ?int
    {
        return self::SLUG_LEVEL[$slug] ?? null;
    }

    private function isReservedRole(string $slug): bool
    {
        return in_array($slug, ['owner', 'superadmin'], true);
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — FINGERPRINT & SESSION
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Fingerprint ringan: IP + UA + Owner ID.
     * Digunakan untuk mendeteksi session hijacking.
     */
    private function fingerprint(Request $request, int $ownerId): string
    {
        $payload = implode('|', [
            $request->ip() ?? '0.0.0.0',
            substr((string) $request->userAgent(), 0, 200),
            (string) $ownerId,
        ]);

        return hash('sha256', $payload);
    }

    /**
     * Bersihkan SEMUA kunci sesi impersonation (modern + legacy).
     */
    private function purgeAllImpersonationSession(): void
    {
        $keys = array_merge(
            [self::IMPERSONATION_CURRENT],
            self::LEGACY_IMPERSONATE_KEYS
        );

        foreach ($keys as $key) {
            try { Session::forget($key); } catch (\Throwable $e) {}
        }

        try { ImpersonationManager::flush(); } catch (\Throwable $e) {}
    }

    /**
     * Cek apakah sesi explorer (mode lama) masih hidup.
     */
    private function isExplorerActive(): bool
    {
        return (bool) Session::get(self::SESSION_KEY . '.active', false)
            && Session::has(self::SESSION_KEY . '.target_user_id');
    }

    /**
     * Cek apakah sesi explorer kadaluarsa (> 30 menit idle).
     */
    private function hasExplorerTimedOut(): bool
    {
        $last = Session::get(self::SESSION_KEY . '.last_activity');
        if (! $last) return false;

        return (now()->timestamp - (int) $last) > (self::SESSION_TIMEOUT_MIN * 60);
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — LOGGING (SAFE)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Log audit dengan correlation-id + fail-safe.
     */
    private function log(string $event, array $ctx = [], string $level = 'info'): void
    {
        try {
            $payload = array_merge($ctx, [
                'event'     => $event,
                'cid'       => $ctx['cid'] ?? (string) Str::uuid(),
                'timestamp' => now()->toIso8601String(),
            ]);

            Log::channel(config('logging.default'))->{$level}("[ExploreAccount] {$event}", $payload);
        } catch (\Throwable $e) {
            // Fallback minimal — jangan sampai logging bikin request gagal
            @error_log("[ExploreAccount] {$event} :: " . ($e->getMessage() ?: 'log-failed'));
        }
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — RATE LIMIT (SLIDING WINDOW)
     * ═══════════════════════════════════════════════════════════ */

    private function rateLimitKey(): string
    {
        return 'owner_explorer_attempts';
    }

    private function isRateLimited(): bool
    {
        $attempts = (array) Session::get($this->rateLimitKey(), []);
        $now      = now()->timestamp;

        // Hanya hitung attempt dalam window
        $attempts = array_values(array_filter(
            $attempts,
            fn ($ts) => ($now - (int) $ts) <= self::ATTEMPT_WINDOW_SEC
        ));

        if (count($attempts) < self::MAX_ATTEMPTS) {
            Session::put($this->rateLimitKey(), $attempts);
            return false;
        }

        return true;
    }

    private function remainingLockoutSeconds(): int
    {
        $attempts = (array) Session::get($this->rateLimitKey(), []);
        if (empty($attempts)) return 0;

        $oldest = min(array_map('intval', $attempts));
        return max(0, ($oldest + self::LOCKOUT_MINUTES * 60) - now()->timestamp);
    }

    private function recordFailedAttempt(): void
    {
        $attempts   = (array) Session::get($this->rateLimitKey(), []);
        $attempts[] = now()->timestamp;
        $attempts   = array_slice($attempts, -self::MAX_ATTEMPTS);

        Session::put($this->rateLimitKey(), $attempts);
    }

    private function recordSuccessAttempt(): void
    {
        Session::forget($this->rateLimitKey());
    }

    /* ═══════════════════════════════════════════════════════════
     | PASSWORD VERIFY (DUAL MODE)
     * ═══════════════════════════════════════════════════════════ */

    private function verifyPassword(string $input, User $owner): bool
    {
        // Mode 1 — password login Owner
        if (Hash::check($input, $owner->password)) {
            return true;
        }

        // Mode 2 — fallback explorer password dari config
        $explorer = (string) config('explorer.password', '');
        if ($explorer !== '' && hash_equals($explorer, $input)) {
            return true;
        }

        return false;
    }

    /* ═══════════════════════════════════════════════════════════
     | VALIDASI — Target user
     * ═══════════════════════════════════════════════════════════ */

    /**
     * @return array{0: ?string, 1: ?User} [error message, target user]
     */
    private function resolveTarget(int $userId, User $owner, bool $allowSelf = false): array
    {
        if ($userId <= 0) {
            return ['Akun tidak ditemukan atau sudah dihapus.', null];
        }

        $target = User::with('level:id,name')->find($userId);

        if (! $target) {
            return ['Akun tidak ditemukan atau sudah dihapus.', null];
        }

        if (! $allowSelf && (int) $target->id === (int) $owner->id) {
            return ['Tidak dapat menjelajahi diri sendiri.', null];
        }

        if ((int) $target->level_id === 1) {
            return ['Akun Owner tidak dapat dijelajahi.', null];
        }

        if (! $target->is_active) {
            return ['Akun target sedang dinonaktifkan.', null];
        }

        return [null, $target];
    }

    /* ═══════════════════════════════════════════════════════════
     | INDEX — Halaman pencarian user
     * ═══════════════════════════════════════════════════════════ */

    public function index(): View
    {
        $owner = $this->authorizeOwner();

        // Cleanup expired impersonation
        try { ImpersonationManager::cleanupExpired(); } catch (\Throwable $e) {}

        return view('owner.settings.explore.index', [
            'impersonations' => ImpersonationManager::all(),
            'maxConcurrent'  => ImpersonationManager::MAX_CONCURRENT,
            'activeCount'    => ImpersonationManager::count(),
            'currentUser'    => $owner,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | SEARCH — Cari user berdasarkan NIM / nama / username
     * ═══════════════════════════════════════════════════════════ */

    public function search(Request $request): View|RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $validated = $request->validate([
            'keyword' => ['required', 'string', 'min:2', 'max:' . self::MAX_KEYWORD_LEN],
        ]);

        $keyword = trim($validated['keyword']);

        // Sanitasi wildcard LIKE
        $like = str_replace(['%', '_'], ['\%', '\_'], $keyword);

        $users = User::query()
            ->with('level:id,name')
            ->where(function ($q) use ($like) {
                $q->where('nim',      'like', "%{$like}%")
                  ->orWhere('name',   'like', "%{$like}%")
                  ->orWhere('username','like', "%{$like}%");
            })
            ->where('level_id', '!=', 1)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->limit(self::SEARCH_LIMIT)
            ->get([
                'id', 'name', 'nim', 'username',
                'avatar_url', 'level_id', 'is_active',
            ]);

        // ── Auto-redirect kalau tepat 1 hasil
        if ($users->count() === 1) {
            return $this->safeRedirect(
                'owner.settings.explore.verify',
                '/owner/settings/explore/verify',
                ['user_id' => $users->first()->id]
            );
        }

        // ── Tidak ada hasil
        if ($users->isEmpty()) {
            return back()->withInput()
                ->with('error', "Tidak ada akun yang cocok dengan \"{$keyword}\".");
        }

        // ── Banyak hasil
        return view('owner.settings.explore.results', [
            'users'   => $users,
            'keyword' => $keyword,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY FORM (GET) — Form isi password Owner
     * ═══════════════════════════════════════════════════════════ */

    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $owner  = $this->authorizeOwner();
        $userId = (int) $request->input('user_id', 0);

        if ($userId <= 0) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'warning',
                'Pilih akun yang ingin dijelajahi terlebih dahulu.'
            );
        }

        [$error, $targetUser] = $this->resolveTarget($userId, $owner, false);

        if ($error || ! $targetUser) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                $error ?? 'Target tidak valid.'
            );
        }

        $rateLimited = $this->isRateLimited();
        $lockoutSec  = $rateLimited ? $this->remainingLockoutSeconds() : 0;

        return view('owner.settings.explore.verify', [
            'targetUser'     => $targetUser,
            'userId'         => $userId,
            'rateLimited'    => $rateLimited,
            'lockoutSeconds' => $lockoutSec,
            'maxAttempts'    => self::MAX_ATTEMPTS,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | UPDATE PASSWORD OWNER (POST)
     * ═══════════════════════════════════════════════════════════ */

    public function updateOwnerPassword(Request $request): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'user_id'          => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if (! Hash::check($request->current_password, $owner->password)) {
            return back()->withInput()
                ->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        if (Hash::check($request->new_password, $owner->password)) {
            return back()->withInput()
                ->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        DB::transaction(function () use ($owner, $request) {
            $owner->update([
                'password'            => Hash::make($request->new_password),
                'password_changed_at' => now(),
            ]);
        });

        $this->recordSuccessAttempt();

        $this->log('owner_password_changed', [
            'owner_id' => $owner->id,
            'ip'       => $request->ip(),
        ]);

        // Redirect balik ke verify kalau ada user_id
        if ($request->filled('user_id')) {
            return $this->safeRedirect(
                'owner.settings.explore.verify',
                '/owner/settings/explore/verify',
                ['user_id' => $request->input('user_id')],
                'success',
                'Password Owner berhasil diubah. Silakan isi form verifikasi dengan password baru.'
            );
        }

        return $this->safeRedirect(
            'owner.settings.explore.index',
            '/owner/settings/explore',
            [],
            'success',
            'Password Owner berhasil diubah.'
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY (POST) — Validasi password, buat token, redirect
     * ═══════════════════════════════════════════════════════════ */

    public function verify(Request $request): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        // ── Rate limit gate
        if ($this->isRateLimited()) {
            $min = (int) ceil($this->remainingLockoutSeconds() / 60);
            return back()->withInput()->withErrors([
                'password' => "Terlalu banyak percobaan. Coba lagi dalam {$min} menit.",
            ]);
        }

        $request->validate([
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string', 'min:4', 'max:72'],
        ]);

        // ── Password check
        if (! $this->verifyPassword((string) $request->password, $owner)) {
            $this->recordFailedAttempt();

            $this->log('password_verify_failed', [
                'owner_id' => $owner->id,
                'ip'       => $request->ip(),
                'ua'       => substr((string) $request->userAgent(), 0, 200),
            ], 'warning');

            return back()->withInput()->withErrors(['password' => 'Password salah.']);
        }

        $this->recordSuccessAttempt();

        // ── Target resolution
        [$error, $targetUser] = $this->resolveTarget((int) $request->user_id, $owner, false);

        if ($error || ! $targetUser) {
            return back()->withErrors(['password' => $error ?? 'Target tidak valid.']);
        }

        // ── Reuse token yang sudah ada (jangan buat spam)
        $existing = ImpersonationManager::findByTarget($targetUser->id);
        if ($existing && ! empty($existing['token']) && ! empty($existing['role_slug'])) {
            $this->log('impersonation_reused', [
                'owner_id'       => $owner->id,
                'target_user_id' => $targetUser->id,
                'role_slug'      => $existing['role_slug'],
            ]);

            return $this->safeRedirect(
                'owner.impersonate.dashboard',
                "/owner/{$existing['role_slug']}/{$existing['token']}/dashboard",
                [
                    'roleSlug'           => $existing['role_slug'],
                    'impersonationToken' => $existing['token'],
                ],
                'info',
                "Sudah menjelajahi akun {$targetUser->name}."
            );
        }

        // ── Buat token baru
        $roleSlug = $this->levelToSlug((int) $targetUser->level_id);

        try {
            $tokenModel = ImpersonationToken::generate(
                $owner->id,
                $targetUser->id,
                $roleSlug,
                $request->ip(),
                (string) $request->userAgent()
            );
        } catch (\Throwable $e) {
            $this->log('token_generate_failed', [
                'error'     => $e->getMessage(),
                'owner_id'  => $owner->id,
                'target_id' => $targetUser->id,
            ], 'error');

            return back()->withErrors([
                'password' => 'Gagal membuat sesi jelajah. Silakan coba lagi.',
            ]);
        }

        // ── Simpan di session map
        ImpersonationManager::put($tokenModel->token, [
            'owner_id'       => $owner->id,
            'target_user_id' => $targetUser->id,
            'target_name'    => $targetUser->name,
            'role_slug'      => $roleSlug,
            'started_at'     => now()->timestamp,
        ]);

        $this->log('impersonation_created', [
            'owner_id'       => $owner->id,
            'target_user_id' => $targetUser->id,
            'target_name'    => $targetUser->name,
            'role_slug'      => $roleSlug,
            'ip'             => $request->ip(),
        ]);

        return $this->safeRedirect(
            'owner.impersonate.dashboard',
            "/owner/{$roleSlug}/{$tokenModel->token}/dashboard",
            [
                'roleSlug'           => $roleSlug,
                'impersonationToken' => $tokenModel->token,
            ],
            'success',
            "Menjelajahi akun {$targetUser->name}."
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | ENTER — Legacy entry point via token
     |         (dipakai oleh route owner.settings.explore.enter & impersonate)
     * ═══════════════════════════════════════════════════════════ */

    public function enter(Request $request, string $role, string $token): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        // ── Validasi format
        if (strlen($token) !== 48 || ! preg_match('/^[A-Za-z0-9]{48}$/', $token)) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                'Token jelajah tidak valid.'
            );
        }

        $levelId = $this->slugToLevel($role);
        if ($levelId === null || $levelId === 1) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                'Role tidak dikenali.'
            );
        }

        // ── Cari token (utamakan DB)
        try {
            $impToken = ImpersonationToken::query()
                ->where('token', $token)
                ->where('role_slug', $role)
                ->where('owner_id', $owner->id)
                ->first();
        } catch (\Throwable $e) {
            $impToken = null;
        }

        // ── Fallback ke session map kalau token tidak ada di DB
        if (! $impToken) {
            $sessionImp = ImpersonationManager::get($token);
            if ($sessionImp) {
                $targetUser = User::with('level:id,name')->find($sessionImp['target_user_id'] ?? 0);
                if ($targetUser && (int) $targetUser->level_id === $levelId) {
                    return $this->activateImpersonation($owner, $targetUser, $role, $token, $request);
                }
            }

            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                'Token jelajah tidak valid atau sudah kadaluarsa.'
            );
        }

        if (! $impToken->isValid()) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                'Token jelajah sudah kadaluarsa.'
            );
        }

        // ── Validasi target
        [$error, $targetUser] = $this->resolveTarget((int) $impToken->target_user_id, $owner, false);

        if ($error || ! $targetUser) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'error',
                $error ?? 'Target tidak valid.'
            );
        }

        // ── Mark as used
        try {
            $impToken->markUsed();
        } catch (\Throwable $e) {
            $this->log('token_mark_used_failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 8) . '...',
            ], 'warning');
        }

        return $this->activateImpersonation($owner, $targetUser, $role, $token, $request);
    }

    /**
     * Aktivasi sesi impersonation + redirect ke dashboard.
     */
    private function activateImpersonation(User $owner, User $targetUser, string $roleSlug, string $token, Request $request): RedirectResponse
    {
        // ── Pastikan terdaftar di session map
        $existing = ImpersonationManager::get($token);
        if (! $existing) {
            ImpersonationManager::put($token, [
                'owner_id'       => $owner->id,
                'target_user_id' => $targetUser->id,
                'target_name'    => $targetUser->name,
                'role_slug'      => $roleSlug,
                'started_at'     => now()->timestamp,
            ]);
        }

        $this->log('impersonation_activated', [
            'owner_id'       => $owner->id,
            'target_user_id' => $targetUser->id,
            'target_name'    => $targetUser->name,
            'role_slug'      => $roleSlug,
            'ip'             => $request->ip(),
            'ua'             => substr((string) $request->userAgent(), 0, 200),
        ]);

        return $this->safeRedirect(
            'owner.impersonate.dashboard',
            "/owner/{$roleSlug}/{$token}/dashboard",
            [
                'roleSlug'           => $roleSlug,
                'impersonationToken' => $token,
            ],
            'success',
            "Anda sekarang menjelajahi akun {$targetUser->name}."
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | EXIT — Keluar dari SATU target (token-based)
     * ═══════════════════════════════════════════════════════════ */

    public function exit(Request $request, string $token): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $imp = ImpersonationManager::get($token);

        if ($imp) {
            $this->log('impersonation_ended', [
                'owner_id'       => $owner->id,
                'target_user_id' => $imp['target_user_id'] ?? null,
                'target_name'    => $imp['target_name']    ?? null,
                'role_slug'      => $imp['role_slug']      ?? null,
                'reason'         => 'single_exit',
            ]);
        }

        ImpersonationManager::forget($token);

        // Kalau yang sedang aktif adalah token ini → bersihkan current
        $current = Session::get(self::IMPERSONATION_CURRENT);
        if (is_array($current) && ($current['token'] ?? null) === $token) {
            Session::forget(self::IMPERSONATION_CURRENT);
        }

        // Kalau masih ada impersonation lain → kembali ke index
        if (ImpersonationManager::count() > 0) {
            return $this->safeRedirect(
                'owner.settings.explore.index',
                '/owner/settings/explore',
                [],
                'success',
                'Sesi jelajah berhasil ditutup.'
            );
        }

        // Kalau sudah tidak ada → balik ke owner dashboard
        return $this->safeRedirect(
            'owner.dashboard',
            '/owner/dashboard',
            [],
            'success',
            'Anda telah keluar dari mode jelajah akun.'
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | EXIT-ALL — Keluar dari SEMUA target + purge session
     * ═══════════════════════════════════════════════════════════ */

    public function exitAll(Request $request): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $all = ImpersonationManager::all();

        $this->log('impersonation_ended', [
            'owner_id'  => $owner->id,
            'count'     => count($all),
            'targets'   => array_map(
                fn ($v) => $v['target_user_id'] ?? null,
                array_values($all)
            ),
            'reason'    => 'exit_all',
        ]);

        $this->purgeAllImpersonationSession();

        // Regenerate session ID untuk mencegah fixation
        try { Session::regenerate(); } catch (\Throwable $e) {}

        return $this->safeRedirect(
            'owner.dashboard',
            '/owner/dashboard',
            [],
            'success',
            'Semua sesi jelajah telah ditutup.'
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | EXIT (LEGACY) — kompat dengan pemanggil lama
     * ═══════════════════════════════════════════════════════════ */

    public function exitExplorer(): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $imp = Session::get(self::IMPERSONATION_CURRENT)
            ?? Session::get('impersonation');

        if (is_array($imp) && ! empty($imp['owner_id'])) {
            $this->log('impersonation_ended', [
                'owner_id'       => $imp['owner_id'],
                'target_user_id' => $imp['target_user_id'] ?? null,
                'target_name'    => $imp['target_name']    ?? null,
                'reason'         => 'legacy_exit_explorer',
            ]);
        }

        $this->purgeAllImpersonationSession();

        try { Session::regenerate(); } catch (\Throwable $e) {}

        return $this->safeRedirect(
            'owner.dashboard',
            '/owner/dashboard',
            [],
            'success',
            'Anda telah keluar dari mode jelajah akun.'
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | DEPRECATED — explore (kompatibilitas route lama)
     * ═══════════════════════════════════════════════════════════ */

    public function explore(Request $request, User $user): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        // Kalau sudah ada token aktif untuk target ini → langsung masuk
        $existing = ImpersonationManager::findByTarget($user->id);
        if ($existing && ! empty($existing['token']) && ! empty($existing['role_slug'])) {
            return $this->safeRedirect(
                'owner.impersonate.dashboard',
                "/owner/{$existing['role_slug']}/{$existing['token']}/dashboard",
                [
                    'roleSlug'           => $existing['role_slug'],
                    'impersonationToken' => $existing['token'],
                ]
            );
        }

        // Kalau belum → ke form verify
        return $this->safeRedirect(
            'owner.settings.explore.verify',
            '/owner/settings/explore/verify',
            ['user_id' => $user->id]
        );
    }
}