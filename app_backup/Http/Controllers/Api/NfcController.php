<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcCredential;
use App\Models\NfcSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NfcController extends Controller
{
    public function ping()
    {
        return response()->json([
            'success' => true,
            'message' => 'UNICODE API is alive',
            'time'    => now()->toIso8601String(),
            'env'     => app()->environment(),
        ]);
    }

    public function resolve(Request $request)
    {
        $validated = $request->validate([
            'uid'           => 'required|string|max:64',
            'session_token' => 'required|string|max:64',
        ]);

        $uid          = strtoupper(preg_replace('/[\s\-:]+/', '', $validated['uid']));
        $sessionToken = $validated['session_token'];

        Log::info('NFC resolve request', [
            'uid'           => $uid,
            'session_token' => $sessionToken,
            'ip'            => $request->ip(),
        ]);

        $credential = NfcCredential::with('user')
            ->where('nfc_identifier', $uid)
            ->active()
            ->first();

        if (! $credential) {
            return response()->json([
                'success'    => false,
                'message'    => 'Kartu tidak terdaftar atau tidak aktif.',
                'error_code' => 'CARD_NOT_FOUND',
            ], 404);
        }

        if (! $credential->user) {
            return response()->json([
                'success'    => false,
                'message'    => 'Kartu tidak terhubung ke user manapun.',
                'error_code' => 'USER_NOT_FOUND',
            ], 404);
        }

        if (! $credential->user->is_active) {
            return response()->json([
                'success'    => false,
                'message'    => 'Akun user tidak aktif.',
                'error_code' => 'USER_INACTIVE',
            ], 403);
        }

        $session = NfcSession::where('session_token', $sessionToken)
            ->pending()
            ->first();

        if (! $session) {
            $session = NfcSession::create([
                'session_token' => $sessionToken,
                'intent'        => NfcSession::INTENT_LOGIN,
                'return_url'    => config('app.url') . '/nfc/callback',
                'expires_at'    => now()->addMinutes(10),
                'ip_address'    => $request->ip(),
                'user_agent'    => substr($request->userAgent() ?? '', 0, 500),
            ]);
        }

        $handoffToken = Str::random(64);

        DB::transaction(function () use ($session, $credential, $handoffToken) {
            $session->update([
                'handoff_token' => $handoffToken,
                'user_id'       => $credential->user_id,
            ]);

            $credential->update([
                'last_used_at' => now(),
                'scan_count'   => ($credential->scan_count ?? 0) + 1,
            ]);
        });

        return response()->json([
            'success'       => true,
            'intent'        => $session->intent,
            'handoff_token' => $handoffToken,
            'redirect_url'  => $session->return_url,
            'expires_at'    => $session->expires_at->toIso8601String(),
            'user' => [
                'id'         => $credential->user->id,
                'user_uuid'  => $credential->user->user_uuid,
                'name'       => $credential->user->name,
                'nim'        => $credential->user->nim,
                'email'      => $credential->user->email,
                'avatar_url' => $credential->user->avatar_url,
            ],
        ], 200);
    }
}
