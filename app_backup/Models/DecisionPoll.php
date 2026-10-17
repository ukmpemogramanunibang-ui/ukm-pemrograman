<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionPoll extends Model
{
    protected $fillable = [
        'user_id', 'public_token', 'title', 'description',
        'allow_multiple', 'max_choices', 'is_anonymous', 'closes_at', 'status',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'is_anonymous'   => 'boolean',
        'closes_at'      => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(DecisionPollOption::class, 'poll_id')->orderBy('position');
    }

    public function totalVotes(): int
    {
        return (int) $this->options()->sum('votes');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open'
            && (! $this->closes_at || $this->closes_at->isFuture());
    }
}