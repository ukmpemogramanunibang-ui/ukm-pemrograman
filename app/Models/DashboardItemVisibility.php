<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardItemVisibility extends Model
{
    protected $table = 'dashboard_item_user_visibility';

    protected $fillable = [
        'user_id',
        'dashboard_item_id',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class, 'dashboard_item_id');
    }
}