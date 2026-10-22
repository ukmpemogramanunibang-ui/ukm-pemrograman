<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ImpersonationToken extends Model
{
    protected $fillable = [
        'token', 'owner_id', 'target_user_id', 'role_slug',
        'ip_address', 'user_agent', 'used_at', 'expires_at', 'revoked_at',
    ];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public static function generate(int $ownerId, int $targetUserId, string $roleSlug, ?string $ip = null, ?string $ua = null): self
    {
        return static::create([
            'token'          => Str::random(48),
            'owner_id'       => $ownerId,
            'target_user_id' => $targetUserId,
            'role_slug'      => $roleSlug,
            'ip_address'     => $ip,
            'user_agent'     => $ua ? substr($ua, 0, 500) : null,
            'expires_at'     => now()->addHours(2),
        ]);
    }

    public function isValid(): bool
    {
        if ($this->revoked_at !== null) return false;
        if ($this->expires_at && now()->greaterThan($this->expires_at)) return false;
        return true;
    }

    public function markUsed(): void
    {
        if (! $this->used_at) $this->update(['used_at' => now()]);
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }

    public function owner()  { return $this->belongsTo(User::class, 'owner_id'); }
    public function target() { return $this->belongsTo(User::class, 'target_user_id'); }
}