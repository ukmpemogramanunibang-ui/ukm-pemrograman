<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserQrRecoverySnapshot extends Model
{
    protected $table = 'user_qr_recovery_snapshots';

    protected $fillable = [
        'recovery_uuid',
        'user_uuid',
        'original_user_id',
        'member_number',
        'nim',
        'name',
        'email',
        'phone',
        'level_id',
        'status_id',
        'ukm_id',
        'qr_uuid',
        'qr_token',
        'avatar_url',
        'profile_snapshot',
        'credential_snapshot',  // akan berisi string terenkripsi
        'is_recoverable',
        'last_snapshot_at',
    ];

    protected $casts = [
        'profile_snapshot'  => 'array',   // JSON → array
        'credential_snapshot' => 'string', // simpan sebagai string (encrypted)
        'is_recoverable'    => 'boolean',
        'last_snapshot_at'  => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($snapshot) {
            if (empty($snapshot->recovery_uuid)) {
                $snapshot->recovery_uuid = (string) Str::uuid();
            }
        });
    }
}