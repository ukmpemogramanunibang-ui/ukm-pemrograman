<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'type', 'name', 'avatar', 'description',
        'created_by', 'last_message_at', 'is_active'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($conversation) {
            if (empty($conversation->uuid)) {
                $conversation->uuid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke peserta
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('role', 'joined_at', 'left_at', 'last_read_at', 'is_muted')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope untuk percakapan privat antara 2 user
    public function scopePrivate($query, $user1Id, $user2Id)
    {
        return $query->where('type', 'private')
            ->whereHas('participants', function ($q) use ($user1Id) {
                $q->where('user_id', $user1Id);
            })
            ->whereHas('participants', function ($q) use ($user2Id) {
                $q->where('user_id', $user2Id);
            });
    }
}