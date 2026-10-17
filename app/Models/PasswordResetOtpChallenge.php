<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtpChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'otp_hash',
        'expires_at',
        'attempts',
        'max_attempts',
        'last_sent_at',
        'verified_at',
        'invalidated_at',
        'ip_hash',
        'user_agent',
    ];

    protected $casts = [
        'expires_at'     => 'datetime',
        'last_sent_at'   => 'datetime',
        'verified_at'    => 'datetime',
        'invalidated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isInvalidated(): bool
    {
        return $this->invalidated_at !== null;
    }

    public function isActive(): bool
    {
        return ! $this->isExpired()
            && ! $this->isVerified()
            && ! $this->isInvalidated()
            && $this->attempts < $this->max_attempts;
    }

    public function canResend(): bool
    {
        if (! $this->last_sent_at) {
            return true;
        }

        return $this->last_sent_at->diffInSeconds(now()) >= 60;
    }
}