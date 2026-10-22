<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountCheckLog extends Model
{
    protected $table = 'account_check_logs';
    public $timestamps = false;

    protected $fillable = [
        'reference_id', 'method', 'privacy_tier', 'result_code', 'ok',
        'identifier_hash', 'ip_hash', 'user_agent_hash',
        'matched_user_id', 'duration_ms', 'country', 'region',
    ];

    protected $casts = [
        'ok'          => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function matchedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }
}