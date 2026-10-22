<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'period', 'calculated_at'];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function entries()
    {
        return $this->hasMany(LeaderboardEntry::class)->orderBy('rank');
    }
}