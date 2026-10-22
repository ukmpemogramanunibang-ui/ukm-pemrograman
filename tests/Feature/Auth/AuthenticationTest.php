<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  AuthenticationTest — Login Manual (NIM-based)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *  Route terdeteksi dari `php artisan route:list --path=login`:
 *    POST /login/manual  →  Auth\LoginController@manualLogin
 *    name: login.manual
 *
 *  Field kredensial: nim + password
 *
 *  ─── Catatan Signature Assertion (PENTING) ──────────────────────────────
 *
 *  Laravel's assertAuthenticatedAs($user, $guard = null)  →  TIDAK terima
 *      message string. Kalau mau custom message, pakai assertTrue(cond, msg).
 *
 *  Laravel's assertGuest($guard = null)  →  TIDAK terima message string.
 *
 *  Untuk custom message yang informatif, kita pakai assertTrue(Auth::check()).
 *
 * ═══════════════════════════════════════════════════════════════════════════
 */
class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->flushRateLimiters();
    }

    // ───────────────────────────────────────────────────────────────────────
    // TEST 1 — Login screen dapat diakses
    // ───────────────────────────────────────────────────────────────────────
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    // ───────────────────────────────────────────────────────────────────────
    // TEST 2 — Login manual dengan kredensial BENAR → authenticated
    // ───────────────────────────────────────────────────────────────────────
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user    = $this->resolveUser();
        $url     = $this->resolveLoginUrl();
        $payload = $this->buildLoginPayload($user, 'password');

        $response = $this->post($url, $payload);

        // ✅ Assertion BENAR (tanpa message sebagai guard):
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(
            Auth::check() && (int) Auth::id() === (int) $user->id,
            "Login ke {$url} (payload keys: " . implode(',', array_keys($payload)) . ") "
            . "harus terautentikasi sebagai user#{$user->id}. "
            . "Response status: {$response->status()}."
        );

        // Jika redirect, jangan balik ke /login (indikator validasi gagal)
        if ($response->isRedirect()) {
            $location = (string) $response->headers->get('Location');
            $this->assertFalse(
                str_ends_with(rtrim($location, '/'), '/login'),
                "Login redirect balik ke /login — kemungkinan ada error validasi. "
                . "Cek LoginController@manualLogin dan storage/logs/laravel.log."
            );
        }
    }

    // ───────────────────────────────────────────────────────────────────────
    // TEST 3 — Login dengan password SALAH → tetap guest
    // ───────────────────────────────────────────────────────────────────────
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user    = $this->resolveUser();
        $url     = $this->resolveLoginUrl();
        $payload = $this->buildLoginPayload($user, 'SALAH-' . Str::random(16));

        $this->post($url, $payload);

        // ✅ Assertion BENAR:
        $this->assertGuest();
        $this->assertFalse(
            Auth::check(),
            "Login dengan password salah TIDAK boleh authenticated. "
            . "Cek LoginController@manualLogin — mungkin ada bypass atau typo "
            . "pada Hash::check() atau Auth::login()."
        );
    }

    // ───────────────────────────────────────────────────────────────────────
    // TEST 4 — Logout
    // ───────────────────────────────────────────────────────────────────────
    public function test_users_can_logout(): void
    {
        $user = $this->resolveUser();

        $response = $this->actingAs($user)->post('/logout');

        // ✅ Assertion BENAR:
        $this->assertGuest();
        $this->assertFalse(
            Auth::check(),
            "Setelah logout, user masih authenticated — cek route POST /logout "
            . "dan Auth\\LoginController@logout."
        );

        $this->assertContains(
            $response->status(),
            [200, 302],
            "Logout harus redirect (302) atau OK (200), bukan {$response->status()}."
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER LAYER
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Ambil user konsisten — 4 level fallback.
     * Password plain-text yang dipakai test: "password".
     */
    private function resolveUser(): User
    {
        // Prioritas 1: user ketua dari UserSeeder
        $user = User::where('nim', '0000000003')->first();
        if ($user) {
            return $user;
        }

        // Prioritas 2: user level ketua (id = 3 di sistem ini)
        $user = User::where('level_id', 3)->first();
        if ($user) {
            return $user;
        }

        // Prioritas 3: user pertama yang ada
        $user = User::first();
        if ($user) {
            return $user;
        }

        // Prioritas 4: fallback factory
        return User::factory()->create([
            'nim'               => '9999999999',
            'email'             => 'auth-test-' . Str::uuid() . '@example.com',
            'password'          => Hash::make('password'),
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Deteksi URL POST login — prioritas named route → scan route collection.
     * Ditemukan dari route:list: POST /login/manual, name login.manual.
     */
    private function resolveLoginUrl(): string
    {
        if (Route::has('login.manual')) {
            return route('login.manual', [], false);
        }

        foreach (Route::getRoutes() as $route) {
            if (! in_array('POST', $route->methods(), true)) {
                continue;
            }
            $uri = trim($route->uri(), '/');
            if ($uri === 'login' || str_starts_with($uri, 'login/')) {
                return '/' . $uri;
            }
        }

        return '/login/manual';
    }

    /**
     * Bangun payload login — auto pilih field kredensial.
     * Field pertama yang non-empty akan dipakai.
     */
    private function buildLoginPayload(User $user, string $password): array
    {
        foreach (['nim', 'email', 'username', 'identifier'] as $field) {
            $value = data_get($user, $field);
            if ($value !== null && $value !== '') {
                return [$field => $value, 'password' => $password];
            }
        }

        return ['nim' => $user->nim, 'password' => $password];
    }

    /**
     * Reset semua rate limiter relevan untuk mencegah 429 antar-test.
     */
    private function flushRateLimiters(): void
    {
        foreach (['', 'login', 'login.manual', 'manual', 'auth'] as $key) {
            try {
                RateLimiter::clear($key);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
}