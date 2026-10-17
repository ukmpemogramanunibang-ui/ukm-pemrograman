<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Player extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'nim', 'email', 'password', 'rank', 'rating', 'xp',
        'win_rate', 'total_matches', 'wins', 'losses', 'draws',
        'streak', 'status', 'last_active_at'
    ];

    protected $hidden = ['password'];

    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function matchesAsPlayer1()
    {
        return $this->hasMany(GameMatch::class, 'player1_id');
    }

    public function matchesAsPlayer2()
    {
        return $this->hasMany(GameMatch::class, 'player2_id');
    }

    public function matches()
    {
        return GameMatch::query()
            ->where('player1_id', $this->id)
            ->orWhere('player2_id', $this->id);
    }

    public function wonMatches()
    {
        return $this->hasMany(GameMatch::class, 'winner_id');
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'player_achievements')->withTimestamps();
    }

    public function leaderboardEntries()
    {
        return $this->hasMany(LeaderboardEntry::class);
    }

    public function updateRating($newRating)
    {
        $this->rating = $newRating;
        $this->rank = $this->calculateRank($newRating);
        $this->save();
    }

    private function calculateRank($rating)
    {
        if ($rating >= 2800) return 'Grandmaster';
        if ($rating >= 2400) return 'Master';
        if ($rating >= 2000) return 'Diamond';
        if ($rating >= 1600) return 'Platinum';
        if ($rating >= 1200) return 'Gold';
        if ($rating >= 800) return 'Silver';
        return 'Bronze';
    }
}