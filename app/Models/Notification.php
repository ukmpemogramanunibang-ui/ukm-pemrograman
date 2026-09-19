<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'sender_id', 'type', 'priority', 'title', 'message',
        'data', 'link', 'action_url', 'icon', 'color_class',
        'read_at', 'archived_at', 'expires_at',
        'reference_type', 'reference_id', 'conversation_id',
        'is_starred',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_starred' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reference()
    {
        return $this->morphTo(null, 'reference_type', 'reference_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsArchived(): void
    {
        if (is_null($this->archived_at)) {
            $this->update(['archived_at' => now()]);
        }
    }

    public function toggleStar(): void
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    public function getIsArchivedAttribute(): bool
    {
        return !is_null($this->archived_at);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getColorClassAttribute(): string
    {
        $map = [
            'chat'          => 'bg-green-500/20 text-green-400',
            'task'          => 'bg-blue-500/20 text-blue-400',
            'announcement'  => 'bg-purple-500/20 text-purple-400',
            'incoming_mail' => 'bg-cyan-500/20 text-cyan-400',
            'outgoing_mail' => 'bg-indigo-500/20 text-indigo-400',
            'minutes'       => 'bg-yellow-500/20 text-yellow-400',
            'agenda'        => 'bg-orange-500/20 text-orange-400',
            'room'          => 'bg-pink-500/20 text-pink-400',
            'booking'       => 'bg-rose-500/20 text-rose-400',
            'attendance'    => 'bg-emerald-500/20 text-emerald-400',
            'member'        => 'bg-teal-500/20 text-teal-400',
            'system'        => 'bg-gray-500/20 text-gray-400',
            'security'      => 'bg-red-500/20 text-red-400',
            'default'       => 'bg-gray-500/20 text-gray-400', // ✅ diperbaiki
        ];
        return $map[$this->type] ?? $map['default'];
    }

    public function getIconAttribute($value): string
    {
        $map = [
            'chat'          => 'fas fa-comment-dots',
            'task'          => 'fas fa-tasks',
            'announcement'  => 'fas fa-bullhorn',
            'incoming_mail' => 'fas fa-inbox',
            'outgoing_mail' => 'fas fa-paper-plane',
            'minutes'       => 'fas fa-sticky-note',
            'agenda'        => 'fas fa-calendar-check',
            'room'          => 'fas fa-door-open',
            'booking'       => 'fas fa-calendar-plus',
            'attendance'    => 'fas fa-user-check',
            'member'        => 'fas fa-user-plus',
            'system'        => 'fas fa-cog',
            'security'      => 'fas fa-shield-alt',
            'default'       => 'fas fa-bell', // ✅ diperbaiki
        ];
        return $value ?? ($map[$this->type] ?? 'fas fa-bell');
    }

    public function getPriorityBadgeAttribute(): string
    {
        $map = [
            'low'    => 'bg-gray-200 text-gray-600',
            'normal' => 'bg-blue-100 text-blue-700',
            'high'   => 'bg-orange-100 text-orange-700',
            'urgent' => 'bg-red-100 text-red-700',
        ];
        return $map[$this->priority] ?? $map['normal'];
    }
}