<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NfcSession extends Model
{
    use HasFactory;

    // Intent constants
    public const INTENT_LOGIN      = 'login';
    public const INTENT_ATTENDANCE = 'attendance';
    public const INTENT_CASH       = 'cash';
    public const INTENT_EVENT      = 'event';
    public const INTENT_PROFILE    = 'profile';

    protected $fillable = [
        'uuid',
        'session_token',
        'handoff_token',
        'intent',
        'user_id',
        'event_id',
        'return_url',
        'ip_address',
        'user_agent',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (NfcSession $session) {
            if (empty($session->uuid)) {
                $session->uuid = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereNull('used_at')
                     ->where('expires_at', '>', now());
    }

    public function scopeForIntent($query, string $intent)
    {
        return $query->where('intent', $intent);
    }

    // Helpers
    public function isValid(): bool
    {
        return $this->used_at === null
            && $this->expires_at instanceof Carbon
            && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
