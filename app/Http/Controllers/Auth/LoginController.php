<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\Status;
use App\Models\LoginAttempt;
use App\Models\AuditLog;
use App\Models\UserQrCode;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class LoginController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
     | KONFIGURASI — Aturan per role
     * ═══════════════════════════════════════════════════════════ */

    /** Mapping level_id → role slug. */
    private const LEVEL_TO_ROLE = [
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus',
        5 => 'anggota',
        6 => 'pengunjung',
    ];

    /** Role yang WAJIB mengisi PIN saat login. */
    private const ROLES_NEED_PIN = ['owner', 'superadmin'];

    /** Aturan panjang password & PIN per role. */
    private const RULES = [
        'owner'      => ['pw_min' => 8, 'pw_max' => 72, 'pin_min' => 8, 'pin_max' => 64, 'need_pin' => true],
        'superadmin' => ['pw_min' => 8, 'pw_max' => 72, 'pin_min' => 8, 'pin_max' => 64, 'need_pin' => true],
        'ketua'      => ['pw_min' => 8, 'pw_max' => 72, 'pin_min' => 6, 'pin_max' => 64, 'need_pin' => false],
        'pengurus'   => ['pw_min' => 8, 'pw_max' => 72, 'pin_min' => 6, 'pin_max' => 64, 'need_pin' => false],
        'anggota'    => ['pw_min' => 6, 'pw_max' => 72, 'pin_min' => 6, 'pin_max' => 64, 'need_pin' => false],
        'pengunjung' => ['pw_min' => 6, 'pw_max' => 72, 'pin_min' => 6, 'pin_max' => 64, 'need_pin' => false],
    ];

    private const RULE_DEFAULT = ['pw_min' => 6, 'pw_max' => 72, 'pin_min' => 6, 'pin_max' => 64, 'need_pin' => false];

    /** Batas percobaan login gagal sebelum akun terkunci. */
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES     = 15;

    /* ═══════════════════════════════════════════════════════════
     | HELPER — Role & Rules
     * ═══════════════════════════════════════════════════════════ */

    protected function resolveRoleSlug(?User $user): string
    {
        if (! $user) return 'anggota';
        $levelId = (int) ($user->level_id ?? 0);
        return self::LEVEL_TO_ROLE[$levelId]
            ?? (strtolower((string) ($user->level->name ?? '')) ?: 'anggota');
    }

    protected function getRulesForRole(string $roleSlug): array
    {
        return self::RULES[$roleSlug] ?? self::RULE_DEFAULT;
    }

    /* ═══════════════════════════════════════════════════════════
     | VIEW — Halaman
     * ═══════════════════════════════════════════════════════════ */

    public function showLoginOptions()   { return view('auth.login-options'); }
    public function showNimForm()        { return view('auth.login-manual'); }
    public function showQrForm()         { return view('auth.login-qr'); }
    public function showGuestRegister()  { return view('auth.guest-register'); }
    public function showGuestLogin()     { return view('auth.guest-login'); }
    public function showForgotPasswordForm() { return view('auth.forgot-password'); }

    /* ═══════════════════════════════════════════════════════════
     | PASSWORD RESET
     * ═══════════════════════════════════════════════════════════ */

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'            => Hash::make($password),
                    'password_changed_at' => now(),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function confirmPassword(Request $request)
    {
        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Password salah.']);
        }

        session(['password_confirmed_at' => now()]);
        return redirect()->intended('/dashboard');
    }

    /* ═══════════════════════════════════════════════════════════
     | AJAX: CEK NIM — Kirim role_slug + rules ke frontend
     * ═══════════════════════════════════════════════════════════ */

    public function checkNimJson(Request $request)
    {
        if (! $request->isMethod('post')) {
            return $this->jsonResponse(['error' => 'Method not allowed. Use POST.'], 405);
        }

        try {
            $request->validate(['nim' => 'required|string|max:32']);

            $nim = trim((string) $request->input('nim'));

            $user = User::with('level')->where('nim', $nim)->first();

            if (! $user) {
                return $this->jsonResponse(['found' => false]);
            }

            /* ── Akun terkunci ── */
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                return $this->jsonResponse([
                    'found'   => true,
                    'name'    => $user->name,
                    'role'    => '🔒 Terkunci',
                    'locked'  => true,
                    'message' => 'Akun terkunci sementara. Coba lagi nanti.',
                ]);
            }

            /* ── Akun tidak aktif ── */
            if (! $user->is_active) {
                return $this->jsonResponse([
                    'found'   => true,
                    'name'    => $user->name,
                    'role'    => '⛔ Nonaktif',
                    'locked'  => true,
                    'message' => 'Akun Anda dinonaktifkan.',
                ]);
            }

            /* ── Resolve role & rules ── */
            $roleSlug = $this->resolveRoleSlug($user);
            $rules    = $this->getRulesForRole($roleSlug);

            return $this->jsonResponse([
                'found'        => true,
                'name'         => $user->name,
                'role'         => ucfirst($roleSlug),
                'role_slug'    => $roleSlug,
                'needPin'      => (bool) $rules['need_pin'],
                'password_min' => (int) $rules['pw_min'],
                'password_max' => (int) $rules['pw_max'],
                'pin_min'      => (int) $rules['pin_min'],
                'pin_max'      => (int) $rules['pin_max'],
            ]);
        } catch (\Throwable $e) {
            Log::error('[checkNimJson] ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'nim'   => $request->input('nim'),
            ]);
            return $this->jsonResponse(['error' => 'Server error'], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
     | LOGIN MANUAL
     * ═══════════════════════════════════════════════════════════ */

    public function manualLogin(Request $request)
    {
        /* ── Rate limiter: IP ── */
        $ipKey = 'login-ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 15)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return $this->sendFailedResponse(
                "Terlalu banyak percobaan dari IP ini. Coba lagi dalam {$seconds} detik.",
                429
            );
        }

        /* ── Validasi request dasar ── */
        $request->validate([
            'nim'      => 'required|string|max:32',
            'password' => 'required|string|max:200',
            'pin'      => 'nullable|string|max:200',
        ]);

        $nim = trim((string) $request->input('nim'));

        /* ── Cari user ── */
        $user = User::with(['level', 'status'])->where('nim', $nim)->first();

        if (! $user) {
            RateLimiter::hit($ipKey, 300);
            return $this->sendFailedResponse('NIM tidak ditemukan.', 404);
        }

        /* ── Akun terkunci ── */
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            $seconds = now()->diffInSeconds($user->locked_until, false);
            return $this->sendFailedResponse(
                "Akun terkunci. Coba lagi dalam {$seconds} detik.",
                423
            );
        }

        /* ── Resolve role & rules ── */
        $roleSlug = $this->resolveRoleSlug($user);
        $rules    = $this->getRulesForRole($roleSlug);

        /* ── Validasi panjang password ── */
        $password = (string) $request->input('password');
        if (mb_strlen($password) < $rules['pw_min']) {
            return $this->sendFailedResponse(
                "Password minimal {$rules['pw_min']} karakter untuk role " . ucfirst($roleSlug) . '.',
                422
            );
        }

        /* ── Verifikasi password ── */
        if (! Hash::check($password, $user->password)) {
            $this->handleFailedAttempt($user, $request, 'password');
            RateLimiter::hit($ipKey, 300);
            return $this->sendFailedResponse('Password salah.', 401);
        }

        /* ── Verifikasi PIN (jika role membutuhkan) ── */
        if ($rules['need_pin']) {
            $pin = (string) $request->input('pin', '');

            if ($pin === '') {
                return $this->sendFailedResponse('PIN wajib diisi untuk role ini.', 422);
            }
            if (mb_strlen($pin) < $rules['pin_min']) {
                return $this->sendFailedResponse(
                    "PIN minimal {$rules['pin_min']} karakter untuk role " . ucfirst($roleSlug) . '.',
                    422
                );
            }
            if (! $user->pin || ! Hash::check($pin, (string) $user->pin)) {
                $this->handleFailedAttempt($user, $request, 'pin');
                return $this->sendFailedResponse('PIN salah.', 401);
            }
        }

        /* ── Cek status akun ── */
        if ($user->status && $user->status->name !== 'active') {
            return $this->sendFailedResponse('Akun tidak aktif. Hubungi administrator.', 403);
        }

        /* ── Reset counter gagal (atomic) ── */
        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ])->save();

        /* ── Login ── */
        Auth::login($user, $request->boolean('remember'));

        /* ── Regenerate session (anti session fixation) ── */
        $request->session()->regenerate();

        /* ── Catat device & audit ── */
        $this->recordDevice($user, $request);

        RateLimiter::clear($ipKey);
        RateLimiter::clear('login-user:' . $user->id);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        $this->recordSuccessfulLogin($user, $request);

        /* ── Redirect ── */
        $redirectUrl = $user->getDashboardRoute();

        try {
            event(new UserLoggedIn($user->id, $user->name, $redirectUrl));
        } catch (\Throwable $e) {
            Log::warning('[manualLogin] Event failed', ['error' => $e->getMessage()]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => $redirectUrl,
                'user'     => [
                    'name'    => $user->name,
                    'nim'     => $user->nim,
                    'avatar'  => $user->avatar_url ?? asset('images/default-avatar.png'),
                    'level'   => $user->level->display_name ?? $user->level->name ?? '',
                    'status'  => $user->status->display_name ?? $user->status->name ?? '',
                    'role'    => $roleSlug,
                    'jabatan' => $user->jabatans()->wherePivot('is_primary', true)->first()?->name ?? '',
                ],
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /* ═══════════════════════════════════════════════════════════
     | LOGOUT
     * ═══════════════════════════════════════════════════════════ */

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            try {
                AuditLog::create([
                    'user_id'     => $user->id,
                    'action'      => 'logout',
                    'description' => 'Logout',
                    'ip_address'  => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                ]);
                event(new UserLoggedOut($user->id, $user->name));
            } catch (\Throwable $e) {
                Log::warning('[logout] Audit/event failed', ['error' => $e->getMessage()]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /* ═══════════════════════════════════════════════════════════
     | QR CODE
     * ═══════════════════════════════════════════════════════════ */

    public function qrScan(Request $request)
    {
        Log::info('QR Scan request received', ['payload' => $request->qr_payload]);

        $request->validate(['qr_payload' => 'required|string|max:2048']);

        $uuid = $this->extractUuid($request->qr_payload);
        if (! $uuid) {
            return response()->json(['valid' => false, 'message' => 'Format QR tidak dikenali.'], 422);
        }

        $credential = UserQrCode::where('qr_uuid', $uuid)->first();
        if (! $credential) {
            return response()->json(['valid' => false, 'message' => 'Credential tidak ditemukan.'], 404);
        }

        $validation = $this->validateCredential($credential);
        if (! $validation['valid']) {
            return response()->json(['valid' => false, 'message' => $validation['message']], 403);
        }

        $user     = $credential->user;
        $roleSlug = $this->resolveRoleSlug($user);
        $rules    = $this->getRulesForRole($roleSlug);

        /* ── Generate challenge ── */
        $challenge = Str::random(48);
        session([
            'qr_challenge_' . $challenge => [
                'user_id'   => $user->id,
                'created_at'=> now()->timestamp,
                'ip'        => $request->ip(),
            ],
        ]);

        return response()->json([
            'valid'              => true,
            'challenge'          => $challenge,
            'user'               => [
                'id'            => $user->id,
                'name'          => $user->name,
                'nim'           => $user->nim,
                'avatar'        => $user->avatar_url ?? asset('images/default-avatar.png'),
                'role'          => ucfirst($roleSlug),
                'role_slug'     => $roleSlug,
                'member_number' => $user->member_number,
                'is_active'     => (bool) $user->is_active,
                'needPin'       => (bool) $rules['need_pin'],
            ],
            'requires_password'  => true,
            'requires_pin'       => (bool) $rules['need_pin'],
            'password_min'       => (int) $rules['pw_min'],
            'pin_min'            => (int) $rules['pin_min'],
        ]);
    }

    public function qrAuthenticate(Request $request)
    {
        $request->validate([
            'challenge' => 'required|string|max:64',
            'password'  => 'required|string|max:200',
            'pin'       => 'nullable|string|max:200',
        ]);

        $challengeKey = 'qr_challenge_' . $request->challenge;
        $payload      = session($challengeKey);

        /* Support format lama (int) dan format baru (array) */
        $userId = is_array($payload) ? ($payload['user_id'] ?? null) : $payload;

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi autentikasi tidak valid. Silakan scan ulang QR.',
            ], 401);
        }

        $user = User::with(['level', 'status'])->find($userId);
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        /* ── Akun terkunci ── */
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return response()->json(['success' => false, 'message' => 'Akun terkunci sementara.'], 423);
        }

        /* ── Resolve role & rules ── */
        $roleSlug = $this->resolveRoleSlug($user);
        $rules    = $this->getRulesForRole($roleSlug);

        /* ── Validasi panjang password ── */
        if (mb_strlen((string) $request->password) < $rules['pw_min']) {
            return response()->json([
                'success' => false,
                'message' => "Password minimal {$rules['pw_min']} karakter.",
            ], 422);
        }

        /* ── Verifikasi password ── */
        if (! Hash::check($request->password, $user->password)) {
            $this->handleFailedAttempt($user, $request, 'password', $challengeKey);
            return response()->json(['success' => false, 'message' => 'Password salah.'], 401);
        }

        /* ── Verifikasi PIN jika diperlukan ── */
        if ($rules['need_pin']) {
            $pin = (string) $request->input('pin', '');

            if ($pin === '' || mb_strlen($pin) < $rules['pin_min']) {
                return response()->json([
                    'success' => false,
                    'message' => "PIN minimal {$rules['pin_min']} karakter.",
                ], 422);
            }
            if (! $user->pin || ! Hash::check($pin, (string) $user->pin)) {
                $this->handleFailedAttempt($user, $request, 'pin', $challengeKey);
                return response()->json(['success' => false, 'message' => 'PIN salah.'], 401);
            }
        }

        /* ── Reset counter ── */
        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ])->save();

        /* ── Login ── */
        Auth::login($user, $request->boolean('remember', false));
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        try {
            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'login',
                'description' => 'Login via QR Code',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        /* ── Hapus challenge & semua challenge kedaluwarsa ── */
        session()->forget($challengeKey);

        return response()->json([
            'success'  => true,
            'redirect' => $user->getDashboardRoute(),
            'user'     => [
                'name' => $user->name,
                'role' => $roleSlug,
            ],
        ]);
    }

    protected function extractUuid($payload)
    {
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $payload, $m)) {
            return $m[0];
        }
        if (preg_match('#/q/([0-9a-f-]+)#i', $payload, $m)) {
            return $m[1];
        }
        return null;
    }

    protected function validateCredential($credential): array
    {
        if (! $credential->is_active || $credential->is_revoked) {
            return ['valid' => false, 'message' => 'QR tidak aktif atau telah dicabut.'];
        }
        if ($credential->expired_at && now()->greaterThan($credential->expired_at)) {
            return ['valid' => false, 'message' => 'QR sudah kedaluwarsa.'];
        }
        $user = $credential->user;
        if (! $user) {
            return ['valid' => false, 'message' => 'User tidak ditemukan.'];
        }
        if (! $user->is_active) {
            return ['valid' => false, 'message' => 'Akun tidak aktif.'];
        }
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return ['valid' => false, 'message' => 'Akun terkunci.'];
        }
        return ['valid' => true];
    }

    /* ═══════════════════════════════════════════════════════════
     | GUEST
     * ═══════════════════════════════════════════════════════════ */

    public function guestRegister(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'phone'     => 'required|string|max:20',
            'institusi' => 'required|string|max:255',
            'prodi'     => 'nullable|string|max:255',
            'password'  => 'required|min:8|max:72',
        ]);

        $guestLevel   = Level::where('name', 'guest')->first();
        $activeStatus = Status::where('name', 'active')->first();

        $user = User::create([
            'user_uuid'    => (string) Str::uuid(),
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'password'     => Hash::make($data['password']),
            'level_id'     => $guestLevel?->id,
            'status_id'    => $activeStatus?->id,
            'is_active'    => true,
            'is_verified'  => true,
            'account_type' => 'guest',
        ]);

        if (method_exists($user, 'assignRole')) {
            try { $user->assignRole('guest'); } catch (\Throwable $e) {}
        }

        Auth::login($user);
        $request->session()->regenerate();

        try {
            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'register',
                'description' => 'Pendaftaran pengunjung',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('guest.dashboard');
    }

    public function guestLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|max:200',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (! $user->hasRole('guest')) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan akun pengunjung.']);
            }

            $request->session()->regenerate();
            return redirect()->route('guest.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS
     * ═══════════════════════════════════════════════════════════ */

    protected function jsonResponse($data, int $status = 200)
    {
        return response()->json($data, $status)
            ->header('Content-Type', 'application/json');
    }

    protected function sendFailedResponse(string $message, int $code = 400)
    {
        if (request()->ajax() || request()->expectsJson()) {
            return $this->jsonResponse(['error' => $message], $code);
        }
        return back()->with('error', $message)->withInput(request()->except(['password', 'pin']));
    }

    /**
     * Handle failed login attempt — ATOMIC (aman dari race condition).
     */
    protected function handleFailedAttempt(User $user, Request $request, string $type = 'password', ?string $challengeKey = null): void
    {
        try {
            DB::transaction(function () use ($user, $request, $type) {
                // Lock row untuk hindari race condition
                $fresh = User::whereKey($user->id)->lockForUpdate()->first();
                if (! $fresh) return;

                $attempts = (int) $fresh->failed_login_attempts + 1;
                $payload  = ['failed_login_attempts' => $attempts];

                if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
                    $payload['locked_until']          = now()->addMinutes(self::LOCKOUT_MINUTES);
                    $payload['last_failed_login_at']  = now();
                    $payload['last_failed_login_ip']  = $request->ip();
                }

                $fresh->forceFill($payload)->save();
            });

            // Catat di LoginAttempt
            LoginAttempt::create([
                'user_id'      => $user->id,
                'email_or_nim' => $user->nim,
                'ip_address'   => $request->ip(),
                'success'      => false,
                'attempted_at' => now(),
                'note'         => $type === 'pin' ? 'PIN salah' : 'Password salah',
            ]);

            // Rate-limiter per-user
            RateLimiter::hit('login-user:' . $user->id, 900);

            // Log forensik
            Log::warning('[Login Failed]', [
                'user_id' => $user->id,
                'nim'     => $user->nim,
                'type'    => $type,
                'ip'      => $request->ip(),
                'ua'      => substr((string) $request->userAgent(), 0, 200),
            ]);
        } catch (\Throwable $e) {
            Log::error('[handleFailedAttempt] ' . $e->getMessage());
        }
    }

    protected function recordSuccessfulLogin(User $user, Request $request): void
    {
        try {
            LoginAttempt::create([
                'user_id'      => $user->id,
                'email_or_nim' => $user->nim,
                'ip_address'   => $request->ip(),
                'success'      => true,
                'attempted_at' => now(),
            ]);

            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'login',
                'description' => 'Login manual berhasil',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[recordSuccessfulLogin] ' . $e->getMessage());
        }
    }

    protected function recordDevice(User $user, Request $request): void
    {
        try {
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            $deviceInfo = [
                'ip'          => $request->ip(),
                'browser'     => $agent->browser(),
                'browser_ver' => $agent->version($agent->browser()),
                'os'          => $agent->platform(),
                'os_ver'      => $agent->version($agent->platform()),
                'device'      => $agent->device(),
                'device_type' => $agent->isDesktop() ? 'desktop' : ($agent->isTablet() ? 'tablet' : 'mobile'),
                'login_at'    => now()->toDateTimeString(),
            ];

            // Pastikan selalu array
            $raw     = $user->login_devices;
            $devices = is_array($raw) ? $raw : (is_string($raw) ? (json_decode($raw, true) ?? []) : []);

            $devices[] = $deviceInfo;
            if (count($devices) > 5) {
                $devices = array_slice($devices, -5);
            }

            // Simpan — model akan auto-cast kalau sudah setup
            $user->forceFill(['login_devices' => $devices])->save();
        } catch (\Throwable $e) {
            Log::warning('[recordDevice] ' . $e->getMessage());
        }
    }
}