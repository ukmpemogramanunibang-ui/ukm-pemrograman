<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionWheel extends Model
{
    protected $fillable = [
        'user_id', 'public_token', 'controller_token_hash', 'title',
        'selection_mode', 'result_mode', 'winner_count', 'duration',
        'sound_mode', 'sound_path', 'status', 'operator_session_id', 'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'winner_count' => 'integer',
        'duration'     => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DecisionWheelEntry::class, 'wheel_id')->orderBy('position');
    }

    public function spins(): HasMany
    {
        return $this->hasMany(DecisionWheelSpin::class, 'wheel_id')->latest('spin_number');
    }

    public function isSpinning(): bool
    {
        return $this->status === 'spinning'
            && $this->locked_until
            && $this->locked_until->isFuture();
    }

    public function isOperator(?string $token): bool
    {
        if (! $token || ! $this->controller_token_hash) return false;
        return hash_equals($this->controller_token_hash, hash('sha256', $token));
    }
}