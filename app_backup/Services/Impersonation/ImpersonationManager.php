<?php

namespace App\Services\Impersonation;

class ImpersonationManager
{
    public const SESSION_KEY    = 'impersonations';
    public const MAX_CONCURRENT = 5;
    public const TTL_HOURS      = 2;

    public static function all(): array
    {
        $all = session(self::SESSION_KEY, []);
        return is_array($all) ? $all : [];
    }

    public static function get(string $token): ?array
    {
        return self::all()[$token] ?? null;
    }

    public static function put(string $token, array $data): void
    {
        $all = self::all();

        // Batasi jumlah — kalau lebih, buang yang paling lama
        if (count($all) >= self::MAX_CONCURRENT && ! isset($all[$token])) {
            $oldest = null;
            $oldestTime = PHP_INT_MAX;
            foreach ($all as $k => $v) {
                $t = $v['started_at'] ?? 0;
                if ($t < $oldestTime) { $oldestTime = $t; $oldest = $k; }
            }
            if ($oldest) unset($all[$oldest]);
        }

        $data['started_at'] = $data['started_at'] ?? now()->timestamp;
        $data['expires_at'] = $data['expires_at'] ?? now()->addHours(self::TTL_HOURS)->timestamp;

        $all[$token] = $data;
        session([self::SESSION_KEY => $all]);
    }

    public static function forget(string $token): void
    {
        $all = self::all();
        unset($all[$token]);
        session([self::SESSION_KEY => $all]);
    }

    public static function flush(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function cleanupExpired(): void
    {
        $all = self::all();
        $now = now()->timestamp;
        $changed = false;

        foreach ($all as $k => $v) {
            $exp = $v['expires_at'] ?? 0;
            if ($exp > 0 && $exp < $now) { unset($all[$k]); $changed = true; }
        }

        if ($changed) session([self::SESSION_KEY => $all]);
    }

    public static function count(): int  { return count(self::all()); }
    public static function isActive(): bool { return self::count() > 0; }

    public static function findByTarget(int $targetUserId): ?array
    {
        foreach (self::all() as $token => $data) {
            if ((int) ($data['target_user_id'] ?? 0) === $targetUserId) {
                return array_merge(['token' => $token], $data);
            }
        }
        return null;
    }
}