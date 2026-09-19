<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;

class LogHttpRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ---------- 1. Abaikan aset statis & debug ----------
        $path = $request->path();
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $staticExtensions = [
            'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
            'woff', 'woff2', 'ttf', 'eot', 'map', 'webp', 'avif'
        ];

        $ignorePatterns = ['debugbar', 'telescope', '_debugbar', 'favicon', 'robots'];

        if (in_array($extension, $staticExtensions) || $this->containsAny($path, $ignorePatterns)) {
            return $next($request);
        }

        // ---------- 2. Mulai pengukuran durasi ----------
        $start = microtime(true);

        // ---------- 3. Eksekusi request ----------
        $response = $next($request);

        // ---------- 4. Hitung durasi ----------
        $durationMs = round((microtime(true) - $start) * 1000, 2); // dalam milidetik

        // ---------- 5. Kumpulkan data request ----------
        $user = Auth::user();
        $userId = $user->id ?? null;
        $userName = $user->name ?? 'Guest';
        $role = $user ? $user->roles->pluck('name')->implode(', ') : 'guest';
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $method = $request->method();
        $fullUrl = $request->fullUrl();
        $routeName = optional($request->route())->getName() ?? 'unnamed';
        $statusCode = $response->getStatusCode();
        $referer = $request->header('referer', '-');
        $sessionId = $request->session()->getId();
        $body = $this->sanitizeBody($request->except([
            'password', 'pin', '_token', 'password_confirmation', 'card_number'
        ]));

        $logData = [
            'user_id'     => $userId,
            'user_name'   => $userName,
            'role'        => $role,
            'ip'          => $ip,
            'user_agent'  => $userAgent,
            'method'      => $method,
            'url'         => $fullUrl,
            'route'       => $routeName,
            'status'      => $statusCode,
            'duration_ms' => $durationMs,
            'referer'     => $referer,
            'session_id'  => $sessionId,
            'body'        => $body,
        ];

        // ---------- 6. Simpan ke database (tabel audit_logs) ----------
        AuditLog::create([
            'user_id'     => $userId,
            'action'      => 'http_request',
            'description' => json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
        ]);

        // ---------- 7. Tulis juga ke log file (opsional) ----------
        Log::channel('daily')->info('HTTP Request', $logData);

        return $response;
    }

    // ---------- Helper ----------
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (stripos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private function sanitizeBody(array $input): array
    {
        $sensitive = ['password', 'pin', 'token', 'credit_card', 'card_number'];
        foreach ($sensitive as $field) {
            if (array_key_exists($field, $input)) {
                $input[$field] = '*****';
            }
        }
        return $input;
    }
}