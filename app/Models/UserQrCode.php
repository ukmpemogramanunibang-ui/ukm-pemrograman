<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserQrCode extends Model
{
    use SoftDeletes;

    protected $table = 'user_qr_codes';

    protected $fillable = [
        'qr_uuid',
        'token',
        'user_id',
        'qr_image',
        'qr_path',
        'is_active',
        'is_revoked',
        'generated_at',
        'expired_at',
        'first_used_at',
        'last_used_at',
        'scan_count',
        'last_scan_ip',
        'last_scan_device',
        'device_name',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'revoked_at',
        'revoked_by',
        'notes',
        'status',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expired_at' => 'datetime',
        'first_used_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
        'is_revoked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk URL gambar QR
    public function getQrUrlAttribute()
    {
        if ($this->qr_path) {
            return asset('storage/' . $this->qr_path);
        }
        return null;
    }
}