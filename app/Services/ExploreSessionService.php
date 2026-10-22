<?php

namespace App\Services;

use App\Models\ImpersonationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExploreSessionService
{
    public const MAX_SESSIONS   = 7;
    public const LIFETIME_HOURS = 8;

    /** Daftar semua sesi aktif owner. */
    public function listActive(int $ownerId): Collection
    {
        return ImpersonationToken::query()
            ->where('owner_id', $ownerId)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->with(['target:id,name,avatar_url,level_id,nim'])
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id')
            ->get();
    }

    public function countActive(int $ownerId): int
    {
        return $this->listActive($ownerId)->count();
    }

    public function create(User $owner, User $target, string $roleSlug, Request $request): ImpersonationToken
    {
        return ImpersonationToken::create([
            'owner_id'         => $owner->id,
            'target_id'        => $target->id,
            'token'            => Str::random(48),
            'role_slug'        => $roleSlug,
            'expires_at'       => now()->addHours(self::LIFETIME_HOURS),
            'last_activity_at' => now(),
            'is_active'        => true,
            'ip_address'       => $request->ip(),
            'user_agent'       => substr((string) $request->userAgent(), 0, 500),
        ]);
    }

    public function deactivate(ImpersonationToken $token): void
    {
        $token->update(['is_active' => false]);
    }

    public function deactivateAll(int $ownerId): void
    {
        ImpersonationToken::where('owner_id', $ownerId)->update(['is_active' => false]);
    }

    public function touch(ImpersonationToken $token): void
    {
        $token->update(['last_activity_at' => now()]);
    }

    /** Cari token valid berdasarkan URL. */
    public function findValid(int $ownerId, string $token, string $roleSlug): ?ImpersonationToken
    {
        return ImpersonationToken::query()
            ->where('owner_id', $ownerId)
            ->where('token', $token)
            ->where('role_slug', $roleSlug)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();
    }
}