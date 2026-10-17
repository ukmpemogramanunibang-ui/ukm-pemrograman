<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    use HasFactory;

    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'device_name',
        'browser',
        'platform',
        'country',
        'city',
        'login_at',
        'logout_at',
        'status',
        'failure_reason',
        'session_id',
        'is_suspicious',
    ];

    protected $casts = [
        'login_at'      => 'datetime',
        'logout_at'     => 'datetime',
        'is_suspicious' => 'boolean',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* =========================================================
     | SCOPES
     ========================================================= */

    public function scopeSuccessful($q)
    {
        return $q->where('status', 'success');
    }

    public function scopeFailed($q)
    {
        return $q->where('status', 'failed');
    }

    public function scopeRecent($q, int $days = 7)
    {
        return $q->where('login_at', '>=', now()->subDays($days));
    }

    public function scopeSuspicious($q)
    {
        return $q->where('is_suspicious', true);
    }

    /* =========================================================
     | ACCESSORS
     ========================================================= */

    public function getDeviceLabelAttribute(): string
    {
        return trim(
            ($this->device_name ?? $this->device_type ?? 'Unknown') .
            ($this->platform ? " • {$this->platform}" : '')
        );
    }

    public function getLocationLabelAttribute(): string
    {
        return collect([$this->city, $this->country])
            ->filter()
            ->implode(', ') ?: 'Tidak diketahui';
    }
}