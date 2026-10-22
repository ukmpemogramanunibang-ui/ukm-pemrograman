<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class OrganizationContext
{
    const SESSION_KEY = 'active_organization_id';
    const CACHE_TTL   = 300; // 5 menit

    public static function set(int $organizationId): void
    {
        Session::put(self::SESSION_KEY, $organizationId);
        Cache::forget(self::cacheKey($organizationId));
    }

    public static function get(): ?Organization
    {
        $id = Session::get(self::SESSION_KEY);
        if (!$id) {
            return self::recoverFromUser();
        }

        return Cache::remember(self::cacheKey($id), self::CACHE_TTL, function () use ($id) {
            return Organization::where('id', $id)
                ->where('status', 'active')
                ->first();
        });
    }

    public static function id(): ?int
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function clear(): void
    {
        $id = self::id();
        if ($id) Cache::forget(self::cacheKey($id));
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Recovery: kalau session hilang, ambil dari user's first active org.
     */
    private static function recoverFromUser(): ?Organization
    {
        $user = auth()->user();
        if (!$user) return null;

        $first = $user->activeOrganizations()->first();
        if ($first) {
            self::set($first->id);
            return $first;
        }

        return null;
    }

    public static function forUser(User $user)
    {
        return $user->activeOrganizations()->get();
    }

    public static function authorize(User $user, int $orgId, string $minRole = 'member'): bool
    {
        $hierarchy = ['member' => 1, 'pj' => 2, 'officer' => 3, 'leader' => 4];

        if (in_array((int) $user->level_id, [1, 2], true)) return true;

        $membership = $user->organizationMemberships()
            ->where('organization_id', $orgId)
            ->where('membership_status', 'active')
            ->first();

        if (!$membership) return false;
        return ($hierarchy[$membership->access_role] ?? 0) >= ($hierarchy[$minRole] ?? 0);
    }

    private static function cacheKey(int $organizationId): string
    {
        return "org:context:{$organizationId}";
    }
}