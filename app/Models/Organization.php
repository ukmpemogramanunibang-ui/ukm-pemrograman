<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'slug', 'short_name', 'type', 'scope_type',
        'parent_id', 'university_id', 'major_id', 'status',
        'logo_url', 'description', 'settings', 'owner_id',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($org) {
            if (empty($org->uuid)) {
                $org->uuid = (string) Str::uuid();
            }
        });
    }

    // ── Relations ──
    public function memberships()
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_memberships')
            ->withPivot(['access_role', 'membership_status', 'position_label', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function activeMembers()
    {
        return $this->members()->wherePivot('membership_status', 'active');
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // ── Scopes ──
    public function scopeActive($q)     { return $q->where('status', 'active'); }
    public function scopeOfType($q, $t) { return $q->where('type', $t); }

    // ── Helpers ──
    public function isActive(): bool { return $this->status === 'active'; }

    public function hasModule(string $module): bool
    {
        $settings = $this->settings ?? [];
        return (bool) ($settings['modules'][$module] ?? false);
    }
}