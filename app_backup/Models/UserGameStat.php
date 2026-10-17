<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'game_id', 'total_plays', 'total_score',
        'best_score', 'avg_score', 'last_metrics', 'last_played_at'
    ];

    protected $casts = [
        'last_metrics' => 'array',
        'last_played_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}