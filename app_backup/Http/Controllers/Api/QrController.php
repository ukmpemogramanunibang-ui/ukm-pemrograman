<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        return response()->json(['valid' => true]);
    }

    public function scan(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        return response()->json(['scanned' => true]);
    }
}