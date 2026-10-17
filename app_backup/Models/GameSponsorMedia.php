<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSponsorMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_sponsor_id', 'type', 'file_path', 'original_name',
        'mime_type', 'file_size', 'sort_order', 'is_clickable'
    ];

    protected $casts = [
        'is_clickable' => 'boolean',
    ];

    public function sponsor()
    {
        return $this->belongsTo(GameSponsor::class, 'game_sponsor_id');
    }
}