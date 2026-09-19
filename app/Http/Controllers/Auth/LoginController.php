<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\Status;
use App\Models\LoginAttempt;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Models\UserQrCode;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN HALAMAN
    |--------------------------------------------------------------------------
    */

    public function showLoginOptions()
    {
        return view('auth.login-options');
    }

    public function showNimForm()
    {
        return view('auth.login-manual');
    }

    public function showQrForm()
    {
        return view('auth.login-qr');
    }

    public function showGuestRegister()
    {
        return view('auth.guest-register');
    }

    public function showGuestLogin()
    {
        return view('auth.guest-login');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /*
    |--------------------------------------------------------------------------
    | PASSWORD RESET
    |--------------------------------------------------------------------------
    */

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
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
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
        if (!Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Password salah.']);
        }

        session(['password_confirmed_at' => now()]);
        return redirect()->intended('/dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: CEK NIM (tanpa autentikasi)
    |--------------------------------------------------------------------------
    */

    public function checkNimJson(Request $request)
    {
        // Bersihkan buffer output
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        if ($request->isMethod('get')) {
            ob_end_clean();
            return $this->jsonResponse(['error' => 'Method not allowed. Use POST.'], 405);
        }

        try {
            $request->validate(['nim' => 'required|string']);

            $user = User::with('level')->where('nim', $request->nim)->first();

    if (!$user) {
        return $this->jsonResponse(['found' => false]);
    }

    // Cek terkunci
    if ($user->locked_until && now()->lessThan($user->locked_until)) {
        return $this->jsonResponse([
            'found'   => true,
            'name'    => $user->name,
            'role'    => '🔒 Terkunci',
            'locked'  => true,
            'message' => 'Akun terkunci sementara.',
        ]);
    }

    // 🔥 Perbaikan: cek nama level, bukan ID
    $needPin = ($user->level && $user->level->name === 'owner');
    $roleName = optional($user->level)->name ?? 'Anggota';

    return $this->jsonResponse([
        'found'   => true,
        'name'    => $user->name,
        'role'    => $roleName,
        'needPin' => $needPin,
    ]);
}   catch (\Exception $e) {
            \Log::error('checkNimJson error: ' . $e->getMessage());
            ob_end_clean();
            return $this->jsonResponse(['error' => 'Server error'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES LOGIN MANUAL
    |--------------------------------------------------------------------------
    */

    public function manualLogin(Request $request)
    {
        $request->validate([
            'nim'      => 'required|string|max:20',
            'password' => 'required|string',
            'pin'      => 'nullable|string',
        ]);

        // Rate Limiting
        $ipKey = 'login_ip_' . $request->ip();
        $userKey = 'login_user_' . $request->nim;

        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return $this->sendFailedResponse(
                "Terlalu banyak percobaan dari IP ini. Coba lagi dalam {$seconds} detik.",
                429
            );
        }

        // Cari user berdasarkan NIM
        $user = User::where('nim', $request->nim)->first();

        if (!$user) {
            RateLimiter::hit($ipKey);
            return $this->sendFailedResponse('NIM tidak ditemukan.', 404);
        }

        // Cek akun terkunci
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return $this->sendFailedResponse(
                'Akun terkunci sementara. Silakan coba lagi nanti atau hubungi admin.',
                423
            );
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            $this->recordFailedLogin($user, $request);
            RateLimiter::hit($ipKey);
            RateLimiter::hit($userKey, 60 * 15);

            $user->increment('failed_login_attempts');
            if ($user->failed_login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                return $this->sendFailedResponse(
                    'Akun terkunci karena 5 kali percobaan gagal. Coba lagi 15 menit lagi.',
                    423
                );
            }

            return $this->sendFailedResponse('Password salah.', 401);
        }

        // Verifikasi PIN (HANYA UNTUK OWNER)
$needPin = ($user->level && $user->level->name === 'owner');
if ($needPin && !Hash::check($request->pin, $user->pin)) {
    $this->recordFailedLogin($user, $request, true);
    return $this->sendFailedResponse('PIN salah.', 401);
}

        // Cek status akun
        if ($user->status && $user->status->name !== 'active') {
            return $this->sendFailedResponse('Akun tidak aktif. Hubungi administrator.', 403);
        }

        // Reset percobaan gagal
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);

        // Login
        Auth::login($user, $request->filled('remember'));

        // Catat perangkat
        $this->recordDevice($user, $request);

        // Clear rate limiter
        RateLimiter::clear($ipKey);
        RateLimiter::clear($userKey);

        // Update user
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Catat log sukses
        $this->recordSuccessfulLogin($user, $request);

        // Tentukan redirect
        $redirectUrl = $this->redirectPath();

        // Broadcast event login
        event(new UserLoggedIn($user->id, $user->name, $redirectUrl));

        // Response
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
                    'role'    => $user->roles->pluck('name')->first() ?? '',
                    'jabatan' => $user->jabatans()->wherePivot('is_primary', true)->first()?->name ?? '',
                ]
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT AFTER LOGIN
    |--------------------------------------------------------------------------
    */

    protected function redirectPath()
    {
        return auth()->user()->getDashboardRoute();
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'logout',
                'description' => 'Logout',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /*
    |--------------------------------------------------------------------------
    | QR CODE SCAN
    |--------------------------------------------------------------------------
    */

    /**
     * Scan QR dan validasi credential, kembalikan challenge + user info.
     */
    public function qrScan(Request $request)
    {
        Log::info('QR Scan request received', ['payload' => $request->qr_payload]);
        $request->validate(['qr_payload' => 'required|string']);

        $uuid = $this->extractUuid($request->qr_payload);
        if (!$uuid) {
            return response()->json(['valid' => false, 'message' => 'Format QR tidak dikenali.'], 422);
        }

        $credential = UserQrCode::where('qr_uuid', $uuid)->first();

        if (!$credential) {
            return response()->json([
                'valid' => false,
                'message' => 'Credential tidak ditemukan.'
            ], 404);
        }

        $validation = $this->validateCredential($credential);
        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ], 403);
        }

        $user = $credential->user;
        $needPin = ($user->level && $user->level->name === 'owner');

        $challenge = Str::random(32);
        session(['qr_challenge_' . $challenge => $user->id]);

        return response()->json([
            'valid' => true,
            'challenge' => $challenge,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim' => $user->nim,
                'avatar' => $user->avatar_url ?? asset('images/default-avatar.png'),
                'role' => $user->level->name ?? 'Guest',
                'member_number' => $user->member_number,
                'is_active' => $user->is_active,
                'needPin' => $needPin,
            ],
            'requires_password' => true,
            'requires_pin' => $needPin,
        ]);
    }

    /**
     * Proses autentikasi setelah scan (password + pin jika diperlukan).
     */
    public function qrAuthenticate(Request $request)
    {
        $request->validate([
            'challenge' => 'required|string',
            'password' => 'required|string',
            'pin' => 'nullable|string',
        ]);

        $userId = session('qr_challenge_' . $request->challenge);
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi autentikasi tidak valid. Silakan scan ulang QR.'
            ], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        // Cek akun terkunci
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun terkunci sementara.'
            ], 423);
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            // Catat percobaan gagal
            $user->increment('failed_login_attempts');
            if ($user->failed_login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                return response()->json([
                    'success' => false,
                    'message' => 'Akun terkunci karena 5 kali percobaan gagal.'
                ], 423);
            }
            $user->save();

            return response()->json([
                'success' => false,
                'message' => 'Password salah.'
            ], 401);
        }

       // Verifikasi PIN jika diperlukan (Owner)
$needPin = ($user->level && $user->level->name === 'owner');
if ($needPin) {
    if (!$request->pin || !Hash::check($request->pin, $user->pin)) {
        return response()->json([
            'success' => false,
            'message' => 'PIN salah.'
        ], 401);
    }
}

        // Reset percobaan gagal
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        // Login user
        Auth::login($user, $request->boolean('remember', false));

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Log audit
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Login via QR Code',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Hapus challenge
        session()->forget('qr_challenge_' . $request->challenge);

        // Regenerate session
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'redirect' => $user->getDashboardRoute(),
            'user' => [
                'name' => $user->name,
                'role' => $user->level->name ?? 'Guest',
            ]
        ]);
    }

    /**
     * Ekstrak UUID dari QR payload.
     */
    protected function extractUuid($payload)
    {
        if (preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i', $payload, $matches)) {
            return $matches[0];
        }
        if (preg_match('#/q/([0-9a-f-]+)#i', $payload, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Validasi credential QR.
     */
    protected function validateCredential($credential)
    {
        if (!$credential->is_active || $credential->is_revoked) {
            return ['valid' => false, 'message' => 'QR tidak aktif atau telah dicabut.'];
        }
        if ($credential->expired_at && now()->greaterThan($credential->expired_at)) {
            return ['valid' => false, 'message' => 'QR sudah kedaluwarsa.'];
        }
        $user = $credential->user;
        if (!$user) {
            return ['valid' => false, 'message' => 'User tidak ditemukan.'];
        }
        if (!$user->is_active) {
            return ['valid' => false, 'message' => 'Akun tidak aktif.'];
        }
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            return ['valid' => false, 'message' => 'Akun terkunci.'];
        }
        return ['valid' => true];
    }

    public function guestRegister(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'phone'     => 'required|string|max:20',
            'institusi' => 'required|string|max:255',
            'prodi'     => 'nullable|string|max:255',
            'password'  => 'required|min:6|max:16',
        ]);

        $guestLevel = Level::where('name', 'guest')->first();
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

        $user->assignRole('guest');
        Auth::login($user);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'register',
            'description' => 'Pendaftaran pengunjung',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return redirect()->route('guest.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | GUEST LOGIN (via email)
    |--------------------------------------------------------------------------
    */

    public function guestLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->hasRole('guest')) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan akun pengunjung.']);
            }

            return redirect()->route('guest.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Kirim response JSON dengan header yang konsisten
     */
    protected function jsonResponse($data, $status = 200)
    {
        return response()->json($data, $status)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Kirim response gagal (JSON atau redirect)
     */
    protected function sendFailedResponse($message, $code = 400)
    {
        if (request()->ajax() || request()->expectsJson()) {
            return $this->jsonResponse(['error' => $message], $code);
        }
        return back()->with('error', $message)->withInput();
    }

    /**
     * Catat percobaan login gagal
     */
    protected function recordFailedLogin($user, Request $request, $pinFailed = false)
    {
        LoginAttempt::create([
            'user_id'      => $user->id,
            'email_or_nim' => $request->nim,
            'ip_address'   => $request->ip(),
            'success'      => false,
            'attempted_at' => now(),
            'note'         => $pinFailed ? 'PIN salah' : 'Password salah',
        ]);
    }

    /**
     * Catat login sukses
     */
    protected function recordSuccessfulLogin($user, Request $request)
    {
        LoginAttempt::create([
            'user_id'      => $user->id,
            'email_or_nim' => $request->nim,
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
    }

    /**
     * Catat perangkat yang digunakan
     */
    protected function recordDevice($user, Request $request)
    {
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

        $devices = $user->login_devices ?? [];
        $devices[] = $deviceInfo;

        if (count($devices) > 5) {
            $devices = array_slice($devices, -5);
        }

        $user->update(['login_devices' => $devices]);
    }
}