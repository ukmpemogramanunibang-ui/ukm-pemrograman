<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionWheelSpin extends Model
{
    protected $fillable = [
        'wheel_id', 'operator_user_id', 'spin_number', 'duration',
        'winner_id', 'winner_ids', 'start_angle', 'target_angle',
        'started_at', 'completed_at', 'status',
    ];

    protected $casts = [
        'winner_ids'   => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'start_angle'  => 'float',
        'target_angle' => 'float',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(DecisionWheel::class, 'wheel_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(DecisionWheelEntry::class, 'winner_id');
    }

    /** Sedang berjalan (started + duration > now) */
    public function isActive(): bool
    {
        if ($this->status !== 'running') return false;
        if (! $this->started_at) return false;

        return $this->started_at->copy()->addSeconds($this->duration)->isFuture();
    }

/** Milidetik sejak spin dimulai */
public function elapsedMs(): int
{
    if (! $this->started_at) return 0;

    return max(0, (int) (now()->getTimestampMs() - $this->started_at->getTimestampMs()));
}

/** Milidetik sisa */
public function remainingMs(): int
{
    return max(0, ($this->duration * 1000) - $this->elapsedMs());
}
}