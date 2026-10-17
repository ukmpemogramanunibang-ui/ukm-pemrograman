<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'photo',
        'status', 'created_by', 'max_members',
    ];

    protected $casts = [
        'max_members' => 'integer',
    ];

    /* =========================
     | RELATIONS
     * ========================= */

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function activeMembers()
    {
        return $this->hasMany(GroupMember::class)->where('status', 'aktif');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

public function announcements()
{
    return $this->belongsToMany(
        GroupAnnouncement::class,
        'group_announcement_targets',
        'group_id',
        'announcement_id'   // ← WAJIB eksplisit
    )->withPivot('is_read', 'read_at')->withTimestamps();
}

    /* =========================
     | HELPERS — CAPACITY
     * ========================= */

    public function getMemberCountAttribute(): int
    {
        // Prioritas: withCount('members_count') yang sudah dihitung DB
        return (int) ($this->attributes['members_count']
            ?? $this->activeMembers()->count());
    }

    public function getCapacityPercentageAttribute(): ?int
    {
        if (! $this->max_members || $this->max_members <= 0) {
            return null;
        }
        return (int) min(100, round(($this->member_count / $this->max_members) * 100));
    }

    public function getIsNearFullAttribute(): bool
    {
        $pct = $this->capacity_percentage;
        return $pct !== null && $pct >= 80 && $pct < 100;
    }

    public function getIsFullAttribute(): bool
    {
        if (! $this->max_members || $this->max_members <= 0) {
            return false;
        }
        return $this->member_count >= $this->max_members;
    }

    public function getIsEmptyAttribute(): bool
    {
        return $this->member_count === 0;
    }

    /* =========================
     | HEALTH STATUS
     * ========================= */

    public function getHealthStatusAttribute(): array
    {
        if ($this->is_empty) {
            return [
                'key'   => 'empty',
                'label' => 'Kosong',
                'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                'dot'   => 'bg-gray-400',
            ];
        }

        if ($this->is_full) {
            return [
                'key'   => 'full',
                'label' => 'Penuh',
                'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'dot'   => 'bg-red-500',
            ];
        }

        if ($this->is_near_full) {
            return [
                'key'   => 'near_full',
                'label' => 'Hampir Penuh',
                'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                'dot'   => 'bg-orange-500',
            ];
        }

        return [
            'key'   => 'healthy',
            'label' => 'Sehat',
            'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'dot'   => 'bg-green-500',
        ];
    }

    /* =========================
     | STATUS BADGE
     * ========================= */

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'aktif'    => [
                'label' => 'Aktif',
                'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            ],
            'nonaktif' => [
                'label' => 'Nonaktif',
                'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            ],
            'arsip'    => [
                'label' => 'Arsip',
                'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            ],
            default    => [
                'label' => ucfirst($this->status ?? '-'),
                'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            ],
        };
    }

    /* =========================
     | KETUA
     * ========================= */

    public function getKetuaAttribute(): ?GroupMember
    {
        if (! $this->relationLoaded('members')) {
            return null;
        }
        return $this->members->firstWhere('position', 'ketua');
    }

    public function getKetuaNameAttribute(): ?string
    {
        return $this->ketua?->user?->name;
    }
}