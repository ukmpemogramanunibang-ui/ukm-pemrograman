<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Financial\PenaltyRule;

class PenaltyRuleSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            ['name' => 'Peringatan 1', 'level' => 1, 'start_day' => 1, 'end_day' => 1, 'fixed_amount' => 0, 'daily_amount' => 0, 'max_amount' => 0],
            ['name' => 'Peringatan 2', 'level' => 2, 'start_day' => 2, 'end_day' => 2, 'fixed_amount' => 0, 'daily_amount' => 0, 'max_amount' => 0],
            ['name' => 'Denda Awal', 'level' => 3, 'start_day' => 3, 'end_day' => 3, 'fixed_amount' => 3000, 'daily_amount' => 0, 'max_amount' => 0],
            ['name' => 'Denda Harian', 'level' => 4, 'start_day' => 4, 'end_day' => 10, 'fixed_amount' => 0, 'daily_amount' => 1000, 'max_amount' => 7000],
        ];

        foreach ($rules as $rule) {
            PenaltyRule::create($rule);
        }
    }
}