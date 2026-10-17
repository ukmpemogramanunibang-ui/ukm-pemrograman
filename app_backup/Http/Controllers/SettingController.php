<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
public function index()
{
    $settings = GameSetting::all()->pluck('value', 'key');
    return view('owner.game-center.settings.index', compact('settings'));
}

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            GameSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
        }
        return redirect()->route('owner.game.settings.index')->with('success', 'Pengaturan disimpan.');
    }
}