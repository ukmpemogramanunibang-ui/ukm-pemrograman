<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupAnnouncement extends Model
{
    protected $fillable = [
        'title', 'content', 'created_by',
        'type', 'priority', 'status',
        'is_important', 'is_pinned', 'require_acknowledgement',
        'scheduled_at', 'published_at', 'expires_at',
        'action_url', 'attachment_path',
        'recipient_count', 'read_count', 'ack_count',
    ];

    protected $casts = [
        'is_important'             => 'boolean',
        'is_pinned'                => 'boolean',
        'require_acknowledgement'  => 'boolean',
        'scheduled_at'             => 'datetime',
        'published_at'             => 'datetime',
        'expires_at'               => 'datetime',
    ];

    /* =========================
     | RELATIONS
     * ========================= */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

public function groups()
{
    return $this->belongsToMany(
        Group::class,
        'group_announcement_targets',
        'announcement_id',   // ← SESUAI STRUKTUR DB
        'group_id'
    )->withPivot('is_read', 'read_at')->withTimestamps();
}

    /* =========================
     | SCOPES
     * ========================= */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForGroup($query, int $groupId)
    {
        return $query->whereHas('groups', fn ($q) => $q->where('group_id', $groupId));
    }

    /* =========================
     | HELPERS
     * ========================= */

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'urgent'    => ['label' => 'Darurat',  'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',       'dot' => 'bg-rose-500'],
            'high'      => ['label' => 'Tinggi',   'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300', 'dot' => 'bg-orange-500'],
            'important' => ['label' => 'Penting',  'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',   'dot' => 'bg-amber-500'],
            default     => ['label' => 'Normal',   'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',       'dot' => 'bg-slate-400'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'published' => ['label' => 'Dipublikasikan', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
            'scheduled' => ['label' => 'Terjadwal',      'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
            'draft'     => ['label' => 'Draft',          'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'],
            'expired'   => ['label' => 'Kedaluwarsa',    'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
            'archived'  => ['label' => 'Arsip',          'class' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'],
            default     => ['label' => ucfirst($this->status ?? '-'), 'class' => 'bg-slate-100 text-slate-600'],
        };
    }

    public function getTypeMetaAttribute(): array
    {
        $map = [
            'rapat_bulanan'    => ['label' => 'Rapat Bulanan',    'icon' => 'fa-calendar-check', 'color' => 'blue'],
            'rapat_tahunan'    => ['label' => 'Rapat Tahunan',    'icon' => 'fa-calendar-star',  'color' => 'indigo'],
            'rapat_kegiatan'   => ['label' => 'Rapat Kegiatan',   'icon' => 'fa-users',          'color' => 'violet'],
            'perubahan_jadwal' => ['label' => 'Perubahan Jadwal', 'icon' => 'fa-clock-rotate-left', 'color' => 'amber'],
            'deadline'         => ['label' => 'Deadline',         'icon' => 'fa-hourglass-end',  'color' => 'rose'],
            'presensi'         => ['label' => 'Presensi',         'icon' => 'fa-clipboard-check', 'color' => 'emerald'],
            'kegiatan'         => ['label' => 'Kegiatan',         'icon' => 'fa-flag',           'color' => 'cyan'],
            'informasi'        => ['label' => 'Informasi',        'icon' => 'fa-circle-info',    'color' => 'slate'],
            'darurat'          => ['label' => 'Darurat',          'icon' => 'fa-triangle-exclamation', 'color' => 'red'],
        ];

        return $map[$this->type] ?? ['label' => ucfirst(str_replace('_', ' ', $this->type ?? 'Umum')), 'icon' => 'fa-bullhorn', 'color' => 'gray'];
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'published' && ! $this->isExpired();
    }
}