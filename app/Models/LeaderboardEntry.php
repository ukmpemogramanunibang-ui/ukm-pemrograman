<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    use HasFactory;

    protected $fillable = ['leaderboard_id', 'player_id', 'rank', 'score'];

    public function leaderboard()
    {
        return $this->belongsTo(Leaderboard::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}