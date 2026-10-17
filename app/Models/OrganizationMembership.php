<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'user_id', 'access_role', 'membership_status',
        'position_label', 'jabatan_id', 'period_id',
        'joined_at', 'left_at', 'permissions',
    ];

    protected $casts = [
        'joined_at'   => 'date',
        'left_at'     => 'date',
        'permissions' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    public function period()
    {
        return $this->belongsTo(OrganizationalPeriod::class, 'period_id');
    }

    public function scopeActive($q)
    {
        return $q->where('membership_status', 'active');
    }

    public function scopeLeaders($q)
    {
        return $q->where('access_role', 'leader')->where('membership_status', 'active');
    }
}