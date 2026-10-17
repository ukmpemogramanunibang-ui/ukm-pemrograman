<?php

namespace App\Models\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LmsAuditLog extends Model
{
    protected $table = 'lms_audit_logs';

    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'actor_id',
        'actor_role',
        'changes',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /* ═══════════════════════════════════════════════════════
       RELASI
       ═══════════════════════════════════════════════════════ */

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    /* ═══════════════════════════════════════════════════════
       SCOPES
       ═══════════════════════════════════════════════════════ */

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /* ═══════════════════════════════════════════════════════
       ACCESSORS
       ═══════════════════════════════════════════════════════ */

    public function getActorNameAttribute(): string
    {
        return $this->actor?->name ?? 'Sistem';
    }

    public function getActorAvatarAttribute(): ?string
    {
        $path = $this->actor?->avatar_url;
        return $path ? asset('storage/' . $path) : null;
    }

    /**
     * Meta action — label, icon, color untuk badge UI.
     */
    public function getActionMetaAttribute(): array
    {
        $action = strtolower($this->action ?? 'unknown');

        $map = [
            'group_created'     => ['label' => 'Grup Dibuat',    'icon' => 'fa-plus-circle',    'color' => 'emerald'],
            'group_updated'     => ['label' => 'Grup Diubah',    'icon' => 'fa-edit',           'color' => 'amber'],
            'group_deleted'     => ['label' => 'Grup Dihapus',   'icon' => 'fa-trash-alt',      'color' => 'rose'],
            'item_created'      => ['label' => 'Item Dibuat',    'icon' => 'fa-plus-circle',    'color' => 'emerald'],
            'item_updated'      => ['label' => 'Item Diubah',    'icon' => 'fa-edit',           'color' => 'amber'],
            'item_deleted'      => ['label' => 'Item Dihapus',   'icon' => 'fa-trash-alt',      'color' => 'rose'],
            'submission_created'=> ['label' => 'Kumpul Tugas',   'icon' => 'fa-paper-plane',    'color' => 'blue'],
            'submission_reviewed'=>['label' => 'Review Tugas',   'icon' => 'fa-check-double',   'color' => 'indigo'],
            'submission_approved'=>['label' => 'Disetujui',      'icon' => 'fa-check-circle',   'color' => 'emerald'],
            'submission_rejected'=>['label' => 'Ditolak',        'icon' => 'fa-times-circle',   'color' => 'rose'],
        ];

        return $map[$action] ?? [
            'label' => ucwords(str_replace('_', ' ', $action)),
            'icon'  => 'fa-circle',
            'color' => 'gray',
        ];
    }
}