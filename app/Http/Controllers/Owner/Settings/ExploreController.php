<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner\Settings;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationToken;
use App\Models\OwnerExploreCard;
use App\Models\OwnerExploreFolder;
use App\Models\User;
use App\Services\Impersonation\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Throwable;

/**
 * =============================================================================
 *  ExploreController — Nexus Edition
 * =============================================================================
 *
 *  Fitur:
 *   ✅ Fix bug role slug: 'anggota-ukm' konsisten di token, URL, dan view
 *   ✅ Concurrent-safe: Owner & user asli bisa aktif bersamaan
 *   ✅ Invisible mode: aktivitas Owner tidak mengotori log user asli
 *   ✅ Session Radar: Owner bisa lihat apakah user asli sedang online
 *   ✅ Circuit-breaker, idempotent, timeout, cache berlapis
 *   ✅ Rate limiter + audit best-effort
 *
 *  Untuk info arsitektur, lihat config/impersonation.php
 * =============================================================================
 */
final class ExploreController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     | CONSTRUCTOR
     * ═══════════════════════════════════════════════════════════ */

    public function __construct(
        private readonly ImpersonationService $impersonation,
    ) {}

    /* ═══════════════════════════════════════════════════════════
     | AUTHORIZATION
     * ═══════════════════════════════════════════════════════════ */

    private function authorizeOwner(): User
    {
        $user = auth()->user();

        abort_unless(
            $user && (int) $user->level_id === 1,
            403,
            'Fitur Jelajah Akun hanya untuk Owner.'
        );

        return $user;
    }

    /* ═══════════════════════════════════════════════════════════
     | INDEX — Folder Dashboard
     * ═══════════════════════════════════════════════════════════ */

public function index(): View
{
    $owner = $this->authorizeOwner();

    $folders = OwnerExploreFolder::query()
        ->where('owner_id', $owner->id)
        ->with([
            'cards' => fn ($q) => $q
                ->select('id', 'folder_id', 'user_id', 'explore_count', 'created_at')
                ->latest()
                ->limit(50),
            'cards.user' => fn ($q) => $q->select([
                'id', 'name', 'nim', 'username', 'email',
                'level_id', 'avatar_url', 'avatar_updated_at',
                'is_active', 'is_online', 'last_seen_at',
            ]),
            'cards.user.level:id,name',
        ])
        ->withCount('cards')
        ->latest()
        ->get();

    // Preload total_points sekaligus — hindari N+1 di accessor $appends
    $userIds = $folders->pluck('cards')->flatten()
        ->pluck('user_id')->filter()->unique()->values();

    if ($userIds->isNotEmpty()) {
        $pointsMap = \App\Models\PointHistory::query()
            ->selectRaw('user_id, SUM(points) as total')
            ->whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->groupBy('user_id')
            ->pluck('total', 'user_id')
            ->all();

        foreach ($folders as $folder) {
            foreach ($folder->cards as $card) {
                if ($card->user) {
                    $card->user->setAttribute(
                        'total_points',
                        (int) ($pointsMap[$card->user->id] ?? 0)
                    );
                }
            }
        }
    }

    $activeSession = $this->impersonation->isActive()
        ? $this->impersonation->sessionRadar((int) session(config('impersonation.session_keys.user_id')))
        : null;

    return view('owner.settings.explore.index', compact('folders', 'activeSession'));
}

    /* ═══════════════════════════════════════════════════════════
     | FOLDER — CRUD
     * ═══════════════════════════════════════════════════════════ */

    public function storeFolder(Request $request): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'in:indigo,amber,emerald,rose,cyan,purple'],
        ]);

        $exists = OwnerExploreFolder::where('owner_id', $owner->id)
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Folder dengan nama ini sudah ada.'])->withInput();
        }

        OwnerExploreFolder::create([
            'owner_id'    => $owner->id,
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'color'       => $data['color'] ?? 'indigo',
        ]);

        $this->audit('folder_created', ['name' => $data['name']]);

        return back()->with('success', "Folder \"{$data['name']}\" berhasil dibuat.");
    }

    public function updateFolder(Request $request, OwnerExploreFolder $folder): RedirectResponse
    {
        $owner = $this->authorizeOwner();
        abort_unless((int) $folder->owner_id === (int) $owner->id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'in:indigo,amber,emerald,rose,cyan,purple'],
        ]);

        $dupe = OwnerExploreFolder::where('owner_id', $owner->id)
            ->where('name', $data['name'])
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($dupe) {
            return back()->withErrors(['name' => 'Nama folder sudah digunakan.'])->withInput();
        }

        $folder->update([
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? null,
            'color'       => $data['color'] ?? $folder->color,
        ]);

        $this->audit('folder_updated', ['folder_id' => $folder->id, 'name' => $data['name']]);

        return back()->with('success', "Folder \"{$data['name']}\" berhasil diperbarui.");
    }

    public function destroyFolder(OwnerExploreFolder $folder): RedirectResponse
    {
        $owner = $this->authorizeOwner();
        abort_unless((int) $folder->owner_id === (int) $owner->id, 403);

        $name = $folder->name;
        $folder->delete();

        $this->audit('folder_deleted', ['name' => $name]);

        return back()->with('success', "Folder \"{$name}\" berhasil dihapus.");
    }

    /* ═══════════════════════════════════════════════════════════
     | CARD — CRUD
     * ═══════════════════════════════════════════════════════════ */

    public function storeCard(Request $request, OwnerExploreFolder $folder): RedirectResponse
    {
        $owner = $this->authorizeOwner();
        abort_unless((int) $folder->owner_id === (int) $owner->id, 403);

        $data = $request->validate(['nim' => ['required', 'string', 'max:50']]);
        $nim  = trim($data['nim']);

        $target = User::query()
            ->select('id', 'name', 'nim', 'level_id', 'account_type')
            ->where('nim', $nim)
            ->first();

        if (! $target) {
            return back()->withErrors(['nim' => "Akun dengan NIM \"{$nim}\" tidak ditemukan."])->withInput();
        }

        if ((int) $target->id === (int) $owner->id) {
            return back()->withErrors(['nim' => 'Tidak bisa menambahkan akun sendiri.'])->withInput();
        }

        if ((int) $target->level_id === 1) {
            return back()->withErrors(['nim' => 'Akun Owner tidak dapat dijelajahi.'])->withInput();
        }

        $dupe = OwnerExploreCard::where('folder_id', $folder->id)
            ->where('user_id', $target->id)
            ->exists();

        if ($dupe) {
            return back()->withErrors(['nim' => "{$target->name} sudah ada di folder ini."])->withInput();
        }

        OwnerExploreCard::create([
            'folder_id' => $folder->id,
            'user_id'   => $target->id,
        ]);

        $this->audit('card_added', [
            'folder_id' => $folder->id,
            'user_id'   => $target->id,
            'nim'       => $nim,
        ]);

        return back()->with('success', "Akun \"{$target->name}\" berhasil ditambahkan ke folder \"{$folder->name}\".");
    }

    public function destroyCard(OwnerExploreCard $card): RedirectResponse
    {
        $owner  = $this->authorizeOwner();
        $folder = $card->folder;
        abort_unless($folder && (int) $folder->owner_id === (int) $owner->id, 403);

        $name = $card->user->name ?? 'user';
        $card->delete();

        $this->audit('card_removed', ['folder_id' => $folder->id, 'name' => $name]);

        return back()->with('success', "Akun \"{$name}\" berhasil dihapus dari folder.");
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY (GET) — Halaman Konfirmasi Password + PIN
     * ═══════════════════════════════════════════════════════════ */

    public function verify(Request $request): View|RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $userId = (int) $request->query('user_id', 0);
        if ($userId <= 0) {
            return redirect()->route('owner.settings.explore.index')
                ->with('warning', 'Pilih akun terlebih dahulu.');
        }

        $target = User::with('level:id,name')->find($userId);
        if (! $target) {
            return redirect()->route('owner.settings.explore.index')
                ->with('error', 'Akun tidak ditemukan.');
        }

        if ((int) $target->level_id === 1) {
            return redirect()->route('owner.settings.explore.index')
                ->with('error', 'Akun Owner tidak dapat dijelajahi.');
        }

        $card = OwnerExploreCard::where('user_id', $target->id)
            ->whereHas('folder', fn ($q) => $q->where('owner_id', $owner->id))
            ->with('folder')
            ->first();

        if (! $card) {
            return redirect()->route('owner.settings.explore.index')
                ->with('error', 'Akun ini belum terdaftar di folder penjelajahan Anda.');
        }

        /* Rate limit info untuk UI */
        $rateKey     = 'owner-explore:' . $owner->id;
        $rateLimited = RateLimiter::tooManyAttempts($rateKey, (int) config('impersonation.rate_limit.attempts', 5));
        $lockoutSec  = $rateLimited ? RateLimiter::availableIn($rateKey) : 0;

        /* Radar: cek apakah user asli sedang online */
        $realUserPresence = $this->impersonation->detectRealUserPresence($target->id);

        return view('owner.settings.explore.verify', compact(
            'target',
            'card',
            'rateLimited',
            'lockoutSec',
            'realUserPresence',
        ));
    }

    /* ═══════════════════════════════════════════════════════════
     | VERIFY (POST) — Authorize & Start Explore
     * ═══════════════════════════════════════════════════════════ */

    public function verifyAuthorize(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string', 'min:1'],
            'pin'      => ['required', 'string', 'min:1'],
        ]);

        $owner  = auth()->user();
        $target = User::findOrFail($request->user_id);

        /* ── 1. Verify password ── */
        if (! $this->verifyPassword($owner, (string) $request->input('password'))) {
            $this->auditWarning('verify_failed_password', [
                'owner_id' => $owner->id,
                'target'   => $target->id,
            ]);
            return back()
                ->withInput($request->except(['password', 'pin']))
                ->withErrors(['password' => 'Password Owner salah.']);
        }

        /* ── 2. Verify PIN ── */
        if (! $this->verifyPin($owner, (string) $request->input('pin'))) {
            $this->auditWarning('verify_failed_pin', [
                'owner_id' => $owner->id,
                'target'   => $target->id,
            ]);
            return back()
                ->withInput($request->except(['password', 'pin']))
                ->withErrors(['pin' => 'PIN Owner salah.']);
        }

        /* ── 3. Start explore ── */
        return $this->startExplore($owner, $target, $request);
    }

    /* ═══════════════════════════════════════════════════════════
     | START EXPLORE
     * ═══════════════════════════════════════════════════════════ */

    protected function startExplore(User $owner, User $target, Request $request): RedirectResponse
    {
        /* ── 1. Reset rate limiter (auth sukses) ── */
        $this->impersonation->resetRateLimiter($owner->id);

        /* ── 2. Resolve role slug (BUG FIXED) ── */
        $roleSlug = $this->impersonation->resolveRoleSlug($target);

        /* ── 3. Verify view mapping tersedia ── */
        if (! $this->impersonation->resolveView($roleSlug)) {
            Log::error('explore.view_missing', ['role_slug' => $roleSlug]);
            return back()->withErrors([
                'general' => "Konfigurasi view untuk role '{$roleSlug}' belum tersedia.",
            ]);
        }

        /* ── 4. Generate token ── */
        try {
            $token = ImpersonationToken::generate(
                ownerId:      $owner->id,
                targetUserId: $target->id,
                roleSlug:     $roleSlug,
                ip:           $request->ip(),
                ua:           (string) $request->userAgent(),
            );
        } catch (Throwable $e) {
            Log::error('explore.token_generate_failed', [
                'owner' => $owner->id,
                'target'=> $target->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors([
                'general' => 'Gagal memulai sesi jelajah. Coba lagi.',
            ]);
        }

        /* ── 5. Set session context ── */
        $this->impersonation->startSession($owner, $target, $token);

        /* ── 6. Audit ── */
        $this->audit('explore_started', [
            'target_id'  => $target->id,
            'role_slug'  => $roleSlug,
            'token_id'   => $token->id,
            'expires_at' => optional($token->expires_at)->toIso8601String(),
            'concurrent_mode' => (bool) config('impersonation.concurrent.enabled'),
        ]);

        /* ── 7. Redirect pakai $roleSlug (dinamis — bukan hardcode) ── */
        return redirect()->route('owner.impersonate.dashboard', [
            'roleSlug'           => $roleSlug,          // ← FIX: konsisten dengan token
            'impersonationToken' => $token->token,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════
     | SESSION RADAR — Endpoint JSON untuk live update
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Endpoint ini dipanggil frontend setiap 15 detik untuk update
     * status "user asli sedang online?", durasi sesi, dll.
     */
    public function radar(): \Illuminate\Http\JsonResponse
    {
        $this->authorizeOwner();

        if (! $this->impersonation->isActive()) {
            return response()->json(['active' => false]);
        }

        $targetId = (int) session(config('impersonation.session_keys.user_id'));
        $tokenId  = (int) session(config('impersonation.session_keys.token_id'));

        $token = $tokenId > 0
            ? ImpersonationToken::find($tokenId)
            : null;

        return response()->json(
            $this->impersonation->sessionRadar($targetId, $token)
        );
    }

    /* ═══════════════════════════════════════════════════════════
     | ACCOUNT (VIEW)
     * ═══════════════════════════════════════════════════════════ */

    public function account(User $user): View
    {
        $this->authorizeOwner();
        $owner = auth()->user();

        $targetId = (int) session(config('impersonation.session_keys.user_id'));
        $active   = (bool) session(config('impersonation.session_keys.active'));

        abort_unless($active && $targetId === (int) $user->id, 403, 'Sesi jelajah tidak valid.');

        session([config('impersonation.session_keys.last_activity') => now()->toIso8601String()]);

        $user->load(['level', 'status', 'university', 'major', 'jabatans']);

        $card = OwnerExploreCard::with('folder')
            ->where('user_id', $user->id)
            ->whereHas('folder', fn ($q) => $q->where('owner_id', $owner->id))
            ->first();

        return view('owner.settings.explore.account', compact('user', 'card'));
    }

    /* ═══════════════════════════════════════════════════════════
     | EXIT
     * ═══════════════════════════════════════════════════════════ */

    public function exitExplore(Request $request): RedirectResponse
    {
        $owner = $this->authorizeOwner();

        $targetId = (int) session(config('impersonation.session_keys.user_id'));
        $tokenId  = (int) session(config('impersonation.session_keys.token_id'));

        if ($targetId > 0) {
            $this->audit('explore_ended', [
                'owner_id' => $owner->id,
                'user_id'  => $targetId,
                'duration' => now()->diffInSeconds(
                    session(config('impersonation.session_keys.started_at'), now())
                ),
            ]);
        }

        /* Revoke token (best-effort) */
        if ($tokenId > 0) {
            try {
                ImpersonationToken::where('id', $tokenId)->update(['revoked_at' => now()]);
            } catch (Throwable) {}
        }

        $this->impersonation->endSession();

        return redirect()->route('owner.settings.explore.index')
            ->with('success', 'Mode penjelajahan telah dihentikan.');
    }

    /* ═══════════════════════════════════════════════════════════
     | PASSWORD & PIN VERIFICATION — Multi-Format
     * ═══════════════════════════════════════════════════════════ */

    protected function verifyPassword(User $user, string $input): bool
    {
        $input = (string) $input;
        if ($input === '') return false;

        foreach (['password', 'owner_password', 'password_hash'] as $col) {
            $stored = $user->{$col} ?? null;
            if ($stored === null || $stored === '') continue;

            try {
                if (Hash::check($input, (string) $stored)) return true;
            } catch (Throwable) {}

            if (hash_equals((string) $stored, $input)) return true;

            if (strlen($stored) === 32 && ctype_xdigit($stored) && hash_equals($stored, md5($input))) return true;
            if (strlen($stored) === 40 && ctype_xdigit($stored) && hash_equals($stored, sha1($input))) return true;
        }

        return false;
    }

    protected function verifyPin(User $user, string $input): bool
    {
        $input = trim((string) $input);
        if ($input === '') return false;

        foreach (['pin', 'owner_pin', 'pin_hash', 'pin_code', 'security_pin'] as $col) {
            $stored = $user->{$col} ?? null;
            if ($stored === null || $stored === '') continue;

            /* JSON / array */
            if (is_string($stored) && (str_starts_with($stored, '{') || str_starts_with($stored, '['))) {
                try {
                    $decoded = json_decode($stored, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $v) {
                            if ((string) $v === $input) return true;
                        }
                    }
                } catch (Throwable) {}
            }

            try {
                if (Hash::check($input, (string) $stored)) return true;
            } catch (Throwable) {}

            $storedStr = (string) $stored;

            if (hash_equals($storedStr, $input)) return true;
            if (strlen($storedStr) === 32 && ctype_xdigit($storedStr) && hash_equals($storedStr, md5($input))) return true;
            if (strlen($storedStr) === 40 && ctype_xdigit($storedStr) && hash_equals($storedStr, sha1($input))) return true;

            if (ctype_digit($storedStr)) {
                $storedNorm = ltrim($storedStr, '0') ?: '0';
                $inputNorm  = ltrim($input, '0') ?: '0';
                if ($storedNorm === $inputNorm && strlen($storedStr) === strlen($input)) return true;
            }
        }

        return false;
    }

    /* ═══════════════════════════════════════════════════════════
     | AUDIT
     * ═══════════════════════════════════════════════════════════ */

    private function audit(string $event, array $ctx = []): void
    {
        try {
            Log::info("[Explore] {$event}", array_merge($ctx, [
                'owner_id'  => auth()->id(),
                'ip'        => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ]));
        } catch (Throwable) {}
    }

    private function auditWarning(string $event, array $ctx = []): void
    {
        try {
            Log::warning("[Explore] {$event}", array_merge($ctx, [
                'owner_id'  => auth()->id(),
                'ip'        => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ]));
        } catch (Throwable) {}
    }
}