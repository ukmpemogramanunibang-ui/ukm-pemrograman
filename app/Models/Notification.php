<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;                    // ← WAJIB ADA
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $table = 'notifications';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id', 'user_id', 'type',
        'title', 'message', 'icon', 'link',
        'source', 'type_slug', 'category',
        'priority', 'severity',
        'action_url', 'deep_link',
        'requires_action', 'actioned_at',
        'actor_id', 'entity_type', 'entity_id',
        'related_type', 'related_id',
        'group_key', 'dedup_key',
        'data',
        'read_at', 'seen_at', 'starred_at', 'archived_at',
        'event_type', 'version', 'source_deleted_at',
    ];

    protected $casts = [
        'data'              => 'array',
        'requires_action'   => 'boolean',
        'read_at'           => 'datetime',
        'seen_at'           => 'datetime',
        'starred_at'        => 'datetime',
        'archived_at'       => 'datetime',
        'actioned_at'       => 'datetime',
        'source_deleted_at' => 'datetime',
        'version'           => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $notif) {
            if (empty($notif->id)) {
                $notif->id = (string) Str::uuid();
            }
        });
    }

    /* =========================================================
     | RELATIONS
     ========================================================= */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /* =========================================================
     | SCOPES
     ========================================================= */
    public function scopeUnread(Builder $q): Builder   { return $q->whereNull('read_at'); }
    public function scopeRead(Builder $q): Builder     { return $q->whereNotNull('read_at'); }
    public function scopeStarred(Builder $q): Builder  { return $q->whereNotNull('starred_at'); }
    public function scopeArchived(Builder $q): Builder { return $q->whereNotNull('archived_at'); }
    public function scopeActive(Builder $q): Builder   { return $q->whereNull('archived_at'); }

    public function scopeAction(Builder $q): Builder
    {
        return $q->whereIn('priority', ['urgent', 'high'])->whereNull('read_at');
    }

    public function scopeForUser(Builder $q, $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /* =========================================================
     | ACCESSORS
     ========================================================= */
    public function getIsReadAttribute(): bool     { return ! is_null($this->read_at); }
    public function getIsStarredAttribute(): bool  { return ! is_null($this->starred_at); }
    public function getIsArchivedAttribute(): bool { return ! is_null($this->archived_at); }
    public function getIsUrgentAttribute(): bool   { return in_array($this->priority, ['urgent', 'high'], true); }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'rose',
            'high'   => 'amber',
            'low'    => 'gray',
            default  => 'violet',
        };
    }

    /* =========================================================
     | HELPERS
     ========================================================= */
    public function resolveIcon(): string
    {
        $raw = $this->getRawOriginal('icon');
        if (! empty($raw)) return $raw;

        $map = [
            'task.created'   => 'fa-plus-circle',
            'task.deadline'  => 'fa-hourglass-half',
            'task.overdue'   => 'fa-exclamation-triangle',
            'task.submitted' => 'fa-paper-plane',
            'task.graded'    => 'fa-award',
            'task.comment'   => 'fa-comment-dots',
            'task.reminder'  => 'fa-bell',
            'group.created'  => 'fa-folder-plus',
            'group.member'   => 'fa-user-plus',
            'chat.message'   => 'fa-comments',
            'announcement'   => 'fa-bullhorn',
            'system'         => 'fa-info-circle',
        ];
        return $map[$this->category ?? ''] ?? 'fa-bell';
    }

    public function markAsRead(): bool
    {
        $updates = [];
        if (is_null($this->seen_at)) $updates['seen_at'] = now();
        if (is_null($this->read_at)) $updates['read_at'] = now();
        if (empty($updates)) return true;
        return $this->forceFill($updates)->save();
    }

    public function toggleStar(): bool
    {
        return $this->forceFill([
            'starred_at' => $this->starred_at ? null : now(),
        ])->save();
    }

    public function archive(): bool
    {
        return $this->forceFill(['archived_at' => now()])->save();
    }

    public function unarchive(): bool
    {
        return $this->forceFill(['archived_at' => null])->save();
    }
}