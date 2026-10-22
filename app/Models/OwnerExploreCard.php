<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerExploreCard extends Model
{
    protected $fillable = ['folder_id', 'user_id', 'last_explored_at', 'explore_count'];

    protected $casts = [
        'last_explored_at' => 'datetime',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(OwnerExploreFolder::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}