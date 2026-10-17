<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtpChallenge;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class PasswordRecoveryController extends Controller
{
    private const OTP_LIFETIME_MINUTES  = 5;
    private const OTP_MAX_ATTEMPTS      = 5;
    private const OTP_RESEND_COOLDOWN   = 60;  // detik
    private const RESET_SESSION_LIFETIME = 10; // menit

    /* ──────────────────────────────────────────────
     | STEP 1 — Form input email
     * ────────────────────────────────────────────── */
    public function show()
    {
        return view('auth.forgot-password');
    }

    /* ──────────────────────────────────────────────
     | STEP 2 — Request OTP
     * ────────────────────────────────────────────── */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($request->email));

        // Rate limit per email (3x / 10 menit)
        $rateKey = 'pwd-recovery:' . hash_hmac('sha256', $email, config('app.key'));

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return back()->with('status',
                "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik."
            );
        }

        RateLimiter::hit($rateKey, 600);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // Generic response — jangan bocorkan apakah email terdaftar
        $genericMessage = 'Jika email tersebut terdaftar dan memenuhi syarat, '
            . 'kode OTP pemulihan akan dikirim. Silakan periksa Inbox atau Spam.';

        if (! $user || ! $user->is_active) {
            return back()->with('status', $genericMessage);
        }

        // Invalidate semua challenge lama
        PasswordResetOtpChallenge::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->whereNull('invalidated_at')
            ->update(['invalidated_at' => now()]);

        // Generate OTP
        $otp = (string) random_int(100000, 999999);

        $challenge = PasswordResetOtpChallenge::create([
            'user_id'      => $user->id,
            'otp_hash'     => Hash::make($otp),
            'expires_at'   => now()->addMinutes(self::OTP_LIFETIME_MINUTES),
            'attempts'     => 0,
            'max_attempts' => self::OTP_MAX_ATTEMPTS,
            'last_sent_at' => now(),
            'ip_hash'      => hash_hmac('sha256', (string) $request->ip(), config('app.key')),
            'user_agent'   => substr((string) $request->userAgent(), 0, 1000),
        ]);

        // Kirim email
        try {
            $user->notify(new PasswordResetOtpNotification($otp, $user->name));
        } catch (\Throwable $e) {
            \Log::error('Gagal kirim OTP', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('status',
                'Terjadi kendala saat mengirim email. Silakan coba lagi nanti.'
            );
        }

        // Simpan challenge di session
        session([
            'pwd_recovery_challenge_id' => $challenge->id,
            'pwd_recovery_email_mask'   => $this->maskEmail($user->email),
        ]);

        return redirect()->route('password.otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /* ──────────────────────────────────────────────
     | STEP 3 — Form OTP
     * ────────────────────────────────────────────── */
    public function showOtp()
    {
        if (! session('pwd_recovery_challenge_id')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'emailMask' => session('pwd_recovery_email_mask', '***'),
        ]);
    }

    /* ──────────────────────────────────────────────
     | STEP 4 — Verifikasi OTP
     * ────────────────────────────────────────────── */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $challengeId = session('pwd_recovery_challenge_id');

        if (! $challengeId) {
            return redirect()->route('password.request')
                ->withErrors(['otp' => 'Sesi pemulihan tidak valid.']);
        }

        $challenge = PasswordResetOtpChallenge::with('user')->find($challengeId);

        if (! $challenge) {
            return redirect()->route('password.request')
                ->withErrors(['otp' => 'Sesi pemulihan tidak ditemukan.']);
        }

        if ($challenge->isExpired()) {
            $challenge->update(['invalidated_at' => now()]);
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        if ($challenge->isInvalidated()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah tidak berlaku.']);
        }

        if ($challenge->attempts >= $challenge->max_attempts) {
            $challenge->update(['invalidated_at' => now()]);
            return back()->withErrors(['otp' => 'Batas percobaan telah tercapai. Silakan minta kode baru.']);
        }

        if (! Hash::check($request->otp, $challenge->otp_hash)) {
            $challenge->increment('attempts');
            $remaining = $challenge->max_attempts - $challenge->attempts;

            return back()->withErrors([
                'otp' => "Kode OTP tidak sesuai. Sisa percobaan: {$remaining}."
            ]);
        }

        // OTP benar
        $challenge->update(['verified_at' => now()]);

        session()->regenerate();
        session([
            'pwd_recovery_verified_user_id'    => $challenge->user_id,
            'pwd_recovery_verified_until'      => now()->addMinutes(self::RESET_SESSION_LIFETIME)->timestamp,
        ]);

        return redirect()->route('password.reset.custom');
    }

    /* ──────────────────────────────────────────────
     | STEP 5 — Form reset password
     * ────────────────────────────────────────────── */
    public function showReset()
    {
        $userId = session('pwd_recovery_verified_user_id');
        $until  = session('pwd_recovery_verified_until');

        if (! $userId || ! $until || now()->timestamp > $until) {
            session()->forget([
                'pwd_recovery_verified_user_id',
                'pwd_recovery_verified_until',
                'pwd_recovery_challenge_id',
                'pwd_recovery_email_mask',
            ]);

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi pemulihan telah berakhir. Silakan mulai lagi.']);
        }

        return view('auth.reset-password');
    }

    /* ──────────────────────────────────────────────
     | STEP 6 — Simpan password baru
     * ────────────────────────────────────────────── */
    public function resetPassword(Request $request)
    {
        $userId = session('pwd_recovery_verified_user_id');
        $until  = session('pwd_recovery_verified_until');

        if (! $userId || ! $until || now()->timestamp > $until) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi pemulihan telah berakhir.']);
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        DB::transaction(function () use ($user, $request) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Invalidate semua challenge aktif
            PasswordResetOtpChallenge::where('user_id', $user->id)
                ->whereNull('invalidated_at')
                ->update(['invalidated_at' => now()]);

            // Revoke semua sesi lain (logout dari perangkat lain)
            DB::table('sessions')->where('user_id', $user->id)->delete();
        });

        session()->forget([
            'pwd_recovery_verified_user_id',
            'pwd_recovery_verified_until',
            'pwd_recovery_challenge_id',
            'pwd_recovery_email_mask',
        ]);

        return redirect()->route('login')
            ->with('status', '✅ Password berhasil diubah. Silakan login dengan password baru.');
    }

    /* ──────────────────────────────────────────────
     | Helper
     * ────────────────────────────────────────────── */
    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $len   = strlen($local);
        $show  = min(2, max(1, $len - 2));
        $mask  = str_repeat('*', max(0, $len - $show));

        return substr($local, 0, $show) . $mask . '@' . $domain;
    }
}