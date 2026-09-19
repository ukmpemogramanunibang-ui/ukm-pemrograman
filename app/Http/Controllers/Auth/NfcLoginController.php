<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\IdentityVaultService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NfcLoginController extends Controller
{
    protected $vault;

    public function __construct(IdentityVaultService $vault)
    {
        $this->vault = $vault;
    }

    public function showForm()
    {
        return view('auth.nfc-login');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'nfc_identifier' => 'required|string',
            'signature' => 'required|string',
        ]);

        $result = $this->vault->verifyNfc($request->nfc_identifier, $request->signature);
        if (!$result['valid']) {
            return response()->json(['error' => $result['message']], 400);
        }

        // Simpan credential_uuid di session untuk langkah berikutnya
        session(['nfc_credential_uuid' => $result['credential_uuid']]);

        return response()->json([
            'valid' => true,
            'user_uuid' => $result['user_uuid'],
            'requires_pin' => false, // nanti bisa diatur berdasarkan role
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentialUuid = session('nfc_credential_uuid');
        if (!$credentialUuid) {
            return redirect()->route('login')->withErrors('Sesi NFC tidak valid.');
        }

        $identity = \App\Models\IdentityVault\IdentityRegistry::where('credential_uuid', $credentialUuid)->first();
        if (!$identity) {
            return redirect()->route('login')->withErrors('Identitas tidak ditemukan.');
        }

        // Cari user berdasarkan user_uuid
        $user = User::where('user_uuid', $identity->user_uuid)->first();
        if (!$user) {
            // Recovery otomatis jika user hilang
            $user = $this->vault->recoverIdentity($identity);
        }

        if (!$user || !$user->is_active) {
            return redirect()->route('login')->withErrors('Akun tidak aktif.');
        }

        // Verifikasi password (atau PIN)
        if ($request->has('password')) {
            if (!Hash::check($request->password, $user->password)) {
                $this->vault->logAudit($credentialUuid, $user->user_uuid, 'nfc_login_failed', ['reason' => 'wrong_password']);
                return back()->withErrors('Password salah.');
            }
        }

        // Login sukses
        Auth::login($user, $request->boolean('remember'));
        $this->vault->logAudit($credentialUuid, $user->user_uuid, 'nfc_login_success');

        return redirect()->intended($user->getDashboardRoute());
    }
}