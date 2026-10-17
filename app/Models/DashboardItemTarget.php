<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardItemTarget extends Model
{
    protected $fillable = [
        'dashboard_item_id', 'role_id', 'user_id',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(DashboardItem::class, 'dashboard_item_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}