<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id', 'type', 'category', 'priority', 'source',
        'title', 'message', 'icon', 'action_url', 'action_label',
        'meta', 'read_at', 'starred_at', 'archived_at',
    ];

    protected $casts = [
        'meta'        => 'array',
        'read_at'     => 'datetime',
        'starred_at'  => 'datetime',
        'archived_at' => 'datetime',
    ];

    /* ── Relations ── */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Scopes ── */
    public function scopeUnread($q)        { return $q->whereNull('read_at'); }
    public function scopeForUser($q, $uid) { return $q->where('user_id', $uid); }
    public function scopeNotArchived($q)   { return $q->whereNull('archived_at'); }
    public function scopeArchived($q)      { return $q->whereNotNull('archived_at'); }
    public function scopeStarred($q)       { return $q->whereNotNull('starred_at'); }

    /* ── Helpers ── */
    public function isUnread(): bool   { return $this->read_at === null; }
    public function isStarred(): bool  { return $this->starred_at !== null; }
    public function isArchived(): bool { return $this->archived_at !== null; }

    public function iconOrDefault(): string
    {
        return $this->icon ?: match($this->type) {
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'danger'  => 'fa-times-circle',
            default   => 'fa-bell',
        };
    }

    public function colorClass(): string
    {
        return match($this->type) {
            'success' => 'emerald',
            'warning' => 'amber',
            'danger'  => 'red',
            default   => 'blue',
        };
    }

    /* ═══════════════════════════════════════════════════════════
     |  STATIC PUSHER — pushTo() (bukan push() karena Model::push ada)
     ═══════════════════════════════════════════════════════════ */
    public static function pushTo(int $userId, array $data): self
    {
        return static::create([
            'user_id'      => $userId,
            'type'         => $data['type']         ?? 'info',
            'category'     => $data['category']     ?? 'general',
            'priority'     => $data['priority']     ?? 'normal',
            'source'       => $data['source']       ?? 'system',
            'title'        => $data['title']        ?? 'Notifikasi',
            'message'      => $data['message']      ?? null,
            'icon'         => $data['icon']         ?? null,
            'action_url'   => $data['action_url']   ?? null,
            'action_label' => $data['action_label'] ?? null,
            'meta'         => $data['meta']         ?? null,
        ]);
    }

    public static function send(int $userId, array $data): self
    {
        return static::pushTo($userId, $data);
    }
}