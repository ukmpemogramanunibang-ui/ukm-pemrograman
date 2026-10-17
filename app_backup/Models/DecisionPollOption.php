<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionPollOption extends Model
{
    protected $fillable = ['poll_id', 'label', 'votes', 'position'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(DecisionPoll::class, 'poll_id');
    }
}