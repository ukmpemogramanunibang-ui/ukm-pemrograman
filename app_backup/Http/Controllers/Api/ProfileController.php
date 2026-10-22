<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        // TODO: sesuaikan
        return response()->json(['updated' => true]);
    }

    public function uploadAvatar(Request $request)
    {
        return response()->json(['ok' => true]);
    }

    public function registerDevice(Request $request)
    {
        return response()->json(['ok' => true]);
    }
}