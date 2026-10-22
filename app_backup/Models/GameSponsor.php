<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'enabled', 'name', 'address', 'website_url',
        'tagline', 'description'
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function media()
    {
        return $this->hasMany(GameSponsorMedia::class)->orderBy('sort_order');
    }
}