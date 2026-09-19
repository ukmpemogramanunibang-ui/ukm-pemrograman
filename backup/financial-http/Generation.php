<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Generation extends Model
{
    protected $fillable = ['code', 'name', 'entry_year', 'description', 'is_active'];

    public function users()
    {
        return $this->hasMany(User::class, 'generation_id');
    }

    public function feePlans()
    {
        return $this->hasMany(FeePlan::class);
    }

    public function getActiveFeePlan()
    {
        return $this->feePlans()->where('status', 'active')->where('effective_from', '<=', now())->latest('effective_from')->first();
    }
}