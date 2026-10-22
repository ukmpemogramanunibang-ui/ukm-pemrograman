<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatedByObserver
{
    public function creating(Model $model): void
    {
        if (empty($model->created_by) && Auth::check()) {
            $model->created_by = Auth::id();
        }
    }
}