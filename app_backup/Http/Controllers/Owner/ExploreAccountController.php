<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use App\Services\Impersonation\ImpersonationManager;

class ExploreAccountController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     | KONSTANTA
     * ═══════════════════════════════════════════════════════════ */

    private const SESSION_KEY     = 'owner_explorer';
    private const SESSION_TIMEOUT = 30;   // menit
    private const MAX_ATTEMPTS    = 5;
    private const LOCKOUT_MINUTES = 15;
    private const RATE_LIMIT_KEY  = 'owner_explorer_attempts';
    private const FINGERPRINT_KEY = 'owner_explorer_fp';
    private const IMPERSONATE_KEY = 'impersonation';

    /* ═══════════════════════════════════════════════════════════
     | AUTHORIZATION
     * ═══════════════════════════════════════════════════════════ */

    private function authorizeOwner(): User
    {
        $user = auth()->user();

        abort_unless(
            $user && (int) $user->level_id === 1,
            403,
            'Fitur Jelajahi Akun hanya dapat digunakan oleh Owner.'
        );

        return $user;
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — URL RESOLVER (FAILSAFE)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Failsafe URL resolver — pakai route() kalau tersedia,
     * fallback ke URL hardcoded kalau tidak.
     */
    private function safeUrl(string $routeName, string $fallbackUrl, array $params = []): string
    {
        try {
            if (Route::has($routeName)) {
                return route($routeName, $params);
            }
        } catch (\Throwable $e) {
            // silent
        }

        return url($fallbackUrl);
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS — ROLE MAPPING
     * ═══════════════════════════════════════════════════════════ */

    private function levelToSlug(int $levelId): string
    {
        return match ($levelId) {
            1 => 'owner',
            2 => 'superadmin',
            3 => 'ketua',
            4 => 'pengurus',
            5 => 'anggota',
            6 => 'pengunjung',
            default => 'user',
        };
    }

    private function slugToLevel(string $slug): ?int
    {
        return match ($slug) {
            'owner'      => 1,
            'superadmin' => 2,
            'ketua'      => 3,
            'pengurus'   => 4,
            'anggota'    => 5,
            'pengunjung' => 6,
            default      => null,
        };
    }

    private function dashboardUrlFor(User $user): string
    {
        return match ((int) $user->level_id) {
            3 => '/ketua/dashboard',
            4 => '/pengurus/tugas',
            5 => '/anggota/dashboard',
            6 => '/explore',
            default => '/dashboard',
        };
    }

    /* ═══════════════════════════════════════════════════════════
     | INDEX — Halaman pencarian user
     * ═══════════════════════════════════════════════════════════ */

public function index(): View
{
    $this->authorizeOwner();
    ImpersonationManager::cleanupExpired();

    return view('owner.settings.explore.index', [
        'impersonations' => ImpersonationManager::all(),
        'maxConcurrent'  => ImpersonationManager::MAX_CONCURRENT,
    ]);
}

    /* ═══════════════════════════════════════════════════════════
     | SEARCH — Cari user berdasarkan NIM / nama / username
     * ═══════════════════════════════════════════════════════════ */

    public function search(Request $request): View|RedirectResponse
    {
        $this->authorizeOwner();

        $request->validate([
            'keyword' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $keyword = trim($request->keyword);

        $users = User::query()
            ->with('level:id,name')
            ->where(function ($q) use ($keyword) {
                $q->where('nim', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('username', 'like', "%{$keyword}%");
            })
            ->where('level_id', '!=', 1)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'nim', 'username', 'avatar_url', 'level_id', 'is_active']);

        // Auto-redirect kalau tepat 1 hasil
        if ($users->count() === 1) {
            return redirect($this->safeUrl(
                'owner.settings.explore.verify',
                '/owner/settings/explore/verify?user_id=' . urlencode((string) $users->first()->id),
                ['user_id' => $users->first()->id]
            ));
        }

        if ($users->isEmpty()) {
            return back()->withInput()
                ->with('error', "Tidak ada akun yang cocok dengan \"{$keyword}\".");
        }

        return view('owner.settings.explore.results', compact('users', 'keyword'));
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY FORM (GET) — Form isi password Owner
     * ═══════════════════════════════════════════════════════════ */

    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        $this->authorizeOwner();

        $userId = (int) $request->input('user_id');

        // ── Tanpa user_id → redirect ke index
        if ($userId <= 0) {
            return redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ))->with('warning', 'Pilih akun yang ingin dijelajahi terlebih dahulu.');
        }

        $targetUser = User::with('level:id,name')->find($userId);

        // ── User tidak ditemukan
        if (! $targetUser) {
            return redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ))->with('error', 'Akun tidak ditemukan atau sudah dihapus.');
        }

        // ── Target owner → tidak boleh
        if ((int) $targetUser->level_id === 1) {
            return redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ))->with('error', 'Akun Owner tidak dapat dijelajahi.');
        }

        $rateLimited = $this->isRateLimited();
        $lockoutSec  = $rateLimited ? $this->remainingLockoutSeconds() : 0;

        return view('owner.settings.explore.verify', [
            'targetUser'     => $targetUser,
            'userId'         => $userId,
            'rateLimited'    => $rateLimited,
            'lockoutSeconds' => $lockoutSec,
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
        ]);

        if (! Hash::check($request->current_password, $owner->password)) {
            return back()->withInput()
                ->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        if (Hash::check($request->new_password, $owner->password)) {
            return back()->withInput()
                ->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $owner->update([
            'password'            => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);

        session()->forget(self::RATE_LIMIT_KEY);

        $this->logAudit('owner_password_changed', [
            'owner_id' => $owner->id,
            'ip'       => $request->ip(),
        ]);

        // Redirect balik ke verify kalau ada user_id, else index
        $redirect = $request->filled('user_id')
            ? redirect($this->safeUrl(
                'owner.settings.explore.verify',
                '/owner/settings/explore/verify?user_id=' . urlencode((string) $request->input('user_id')),
                ['user_id' => $request->input('user_id')]
            ))
            : redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ));

        return $redirect->with('success', 'Password Owner berhasil diubah. Silakan isi form verifikasi dengan password baru.');
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY (POST) — Validasi password, buat token
     * ═══════════════════════════════════════════════════════════ */

public function verify(Request $request): RedirectResponse
{
    $owner = $this->authorizeOwner();

    if ($this->isRateLimited()) {
        $min = (int) ceil($this->remainingLockoutSeconds() / 60);
        return back()->withErrors(['password' => "Terlalu banyak percobaan. Coba {$min} menit lagi."]);
    }

    $request->validate([
        'user_id'  => 'required|integer|exists:users,id',
        'password' => 'required|string|min:4|max:72',
    ]);

    if (! $this->verifyPassword($request->password, $owner)) {
        $this->recordFailedAttempt();
        return back()->withInput()->withErrors(['password' => 'Password salah.']);
    }
    $this->recordSuccessAttempt();

    $targetUser = User::with('level:id,name')->findOrFail((int) $request->user_id);

    if ((int) $targetUser->id === (int) $owner->id) {
        return back()->withErrors(['password' => 'Tidak dapat menjelajahi diri sendiri.']);
    }
    if ((int) $targetUser->level_id === 1) {
        return back()->withErrors(['password' => 'Owner tidak dapat dijelajahi.']);
    }

    // Sudah ada impersonate untuk target ini? → reuse
    $existing = ImpersonationManager::findByTarget($targetUser->id);
    if ($existing) {
        return redirect()->route('owner.impersonate.dashboard', [
            'roleSlug'           => $existing['role_slug'],
            'impersonationToken' => $existing['token'],
        ])->with('info', "Sudah menjelajahi {$targetUser->name}.");
    }

    // Buat token baru
    $roleSlug   = $this->levelToSlug((int) $targetUser->level_id);
    $tokenModel = ImpersonationToken::generate(
        $owner->id, $targetUser->id, $roleSlug,
        $request->ip(), (string) $request->userAgent()
    );

    // Simpan di session MAP
    ImpersonationManager::put($tokenModel->token, [
        'owner_id'       => $owner->id,
        'target_user_id' => $targetUser->id,
        'target_name'    => $targetUser->name,
        'role_slug'      => $roleSlug,
    ]);

    return redirect()->route('owner.impersonate.dashboard', [
        'roleSlug'           => $roleSlug,
        'impersonationToken' => $tokenModel->token,
    ])->with('success', "Menjelajahi akun {$targetUser->name}.");
}

    /* ═══════════════════════════════════════════════════════════
     | ENTER — Validasi token, aktifkan session impersonation,
     |         redirect ke /owner/{roleSlug}/dashboard
     * ═══════════════════════════════════════════════════════════ */

    public function enter(Request $request, string $role, string $token): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        // ── 1. Cari token
        $impToken = ImpersonationToken::where('token', $token)
            ->where('role_slug', $role)
            ->where('owner_id', $owner->id)
            ->first();

        if (! $impToken || ! $impToken->isValid()) {
            return redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ))->with('error', 'Token jelajah tidak valid atau sudah kadaluarsa.');
        }

        // ── 2. Validasi target
        $targetUser = User::with('level:id,name')->find($impToken->target_user_id);

        if (! $targetUser || (int) $targetUser->level_id === 1) {
            return redirect($this->safeUrl(
                'owner.settings.explore.index',
                '/owner/settings/explore'
            ))->with('error', 'User target tidak valid.');
        }

        // ── 3. Mark token as used
        $impToken->update(['used_at' => now()]);

        // ── 4. Simpan session impersonation (struktur flat + nested untuk kompatibilitas)
        $fingerprint = hash('sha256', $request->ip() . '|' . $request->userAgent());

        session([
            self::IMPERSONATE_KEY => [
                'active'         => true,
                'owner_id'       => $owner->id,
                'owner_name'     => $owner->name,
                'target_user_id' => $targetUser->id,
                'target_name'    => $targetUser->name,
                'target_level'   => $targetUser->level_id,
                'role_slug'      => $role,
                'started_at'     => now()->timestamp,
                'fingerprint'    => $fingerprint,
            ],

            self::SESSION_KEY . '.active'            => true,
            self::SESSION_KEY . '.target_user_id'    => $targetUser->id,
            self::SESSION_KEY . '.target_user_name'  => $targetUser->name,
            self::SESSION_KEY . '.target_user_level' => $targetUser->level_id,
            self::SESSION_KEY . '.started_at'        => now()->timestamp,
            self::SESSION_KEY . '.last_activity'     => now()->timestamp,
            self::FINGERPRINT_KEY                    => $fingerprint,
        ]);

        // ── 5. Log
        $this->logAudit('impersonation_started', [
            'owner_id'       => $owner->id,
            'owner_name'     => $owner->name,
            'target_user_id' => $targetUser->id,
            'target_name'    => $targetUser->name,
            'role_slug'      => $role,
            'ip'             => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 200),
        ]);

        // ── 6. Redirect ke /owner/{roleSlug}/dashboard
        return redirect($this->safeUrl(
            'owner.impersonate.dashboard',
            "/owner/{$role}/dashboard",
            ['roleSlug' => $role]
        ))->with('success', "Anda sekarang menjelajahi akun {$targetUser->name}.");
    }

    /* ═══════════════════════════════════════════════════════════
     | EXIT — Restore ke Owner
     * ═══════════════════════════════════════════════════════════ */

    public function exitExplorer(): RedirectResponse
    {
        $imp = session(self::IMPERSONATE_KEY);

        if ($imp && ! empty($imp['owner_id'])) {
            $this->logAudit('impersonation_ended', [
                'owner_id'       => $imp['owner_id'],
                'target_user_id' => $imp['target_user_id'] ?? null,
                'target_name'    => $imp['target_name']    ?? null,
            ]);
        }

        session()->forget([
            self::IMPERSONATE_KEY,
            self::SESSION_KEY,
            self::FINGERPRINT_KEY,
        ]);

        session()->regenerate();

        return redirect($this->safeUrl(
            'owner.dashboard',
            '/owner/dashboard'
        ))->with('success', 'Anda telah keluar dari mode jelajah akun.');
    }

    /* ═══════════════════════════════════════════════════════════
     | DEPRECATED — explore (untuk kompatibilitas route lama)
     * ═══════════════════════════════════════════════════════════ */

    public function explore(Request $request, User $user): RedirectResponse
    {
        $this->authorizeOwner();

        // Kalau session impersonation aktif untuk user ini
        $imp = session(self::IMPERSONATE_KEY);

        if ($imp && ($imp['active'] ?? false)
            && (int) ($imp['target_user_id'] ?? 0) === (int) $user->id) {
            $roleSlug = $imp['role_slug'] ?? $this->levelToSlug((int) $user->level_id);

            return redirect($this->safeUrl(
                'owner.impersonate.dashboard',
                "/owner/{$roleSlug}/dashboard",
                ['roleSlug' => $roleSlug]
            ));
        }

        return redirect($this->safeUrl(
            'owner.settings.explore.verify',
            '/owner/settings/explore/verify?user_id=' . urlencode((string) $user->id),
            ['user_id' => $user->id]
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     | SESSION HELPERS
     * ═══════════════════════════════════════════════════════════ */

    private function isExplorerActive(): bool
    {
        return session(self::SESSION_KEY . '.active') === true
            && session(self::SESSION_KEY . '.target_user_id') !== null;
    }

    private function hasExplorerTimedOut(): bool
    {
        $last = session(self::SESSION_KEY . '.last_activity');
        if (! $last) return false;

        return (now()->timestamp - (int) $last) > (self::SESSION_TIMEOUT * 60);
    }

    private function forgetExplorerSession(): void
    {
        session()->forget([self::SESSION_KEY, self::FINGERPRINT_KEY]);
    }

    /* ═══════════════════════════════════════════════════════════
     | PASSWORD VERIFY (dual mode)
     * ═══════════════════════════════════════════════════════════ */

    private function verifyPassword(string $input, User $owner): bool
    {
        // 1. Password login Owner
        if (Hash::check($input, $owner->password)) {
            return true;
        }

        // 2. Fallback: explorer password dari config
        $explorer = (string) config('explorer.password', '');
        if ($explorer !== '' && hash_equals($explorer, $input)) {
            return true;
        }

        return false;
    }

    /* ═══════════════════════════════════════════════════════════
     | RATE LIMIT
     * ═══════════════════════════════════════════════════════════ */

    private function isRateLimited(): bool
    {
        $attempts = (array) session(self::RATE_LIMIT_KEY, []);

        if (count($attempts) < self::MAX_ATTEMPTS) {
            return false;
        }

        return now()->timestamp < (min($attempts) + self::LOCKOUT_MINUTES * 60);
    }

    private function remainingLockoutSeconds(): int
    {
        $attempts = (array) session(self::RATE_LIMIT_KEY, []);
        if (empty($attempts)) return 0;

        return max(0, (min($attempts) + self::LOCKOUT_MINUTES * 60) - now()->timestamp);
    }

    private function recordFailedAttempt(): void
    {
        $attempts   = (array) session(self::RATE_LIMIT_KEY, []);
        $attempts[] = now()->timestamp;
        $attempts   = array_slice($attempts, -self::MAX_ATTEMPTS);

        session([self::RATE_LIMIT_KEY => $attempts]);
    }

    private function recordSuccessAttempt(): void
    {
        session()->forget(self::RATE_LIMIT_KEY);
    }

    /* ═══════════════════════════════════════════════════════════
     | LOGGING (fail-safe)
     * ═══════════════════════════════════════════════════════════ */

    private function logAudit(string $event, array $ctx = []): void
    {
        try {
            Log::info("[ExploreAccount] {$event}", array_merge($ctx, [
                'timestamp' => now()->toIso8601String(),
            ]));
        } catch (\Throwable $e) {
            @error_log("[ExploreAccount] log failed: {$e->getMessage()}");
        }
    }
}