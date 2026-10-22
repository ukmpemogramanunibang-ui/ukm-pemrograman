<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class GoogleDriveController extends Controller
{
    public function redirect()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $client = $this->getClient();
        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('secretary.dashboard')->with('error', 'Gagal autentikasi Google Drive.');
        }
        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            return redirect()->route('secretary.dashboard')->with('error', 'Error: ' . $token['error']);
        }
        // Simpan token ke session user (atau database)
        $user = Auth::user();
        $user->google_drive_token = json_encode($token);
        $user->save();

        return redirect()->route('secretary.archives.index')->with('success', 'Berhasil terhubung dengan Google Drive.');
    }

    public function revoke()
    {
        $user = Auth::user();
        $user->google_drive_token = null;
        $user->save();
        return redirect()->route('secretary.archives.index')->with('success', 'Koneksi Google Drive dicabut.');
    }

    private function getClient()
    {
        $client = new Client();
        $client->setApplicationName('UKM UNICODE');
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->setScopes([Drive::DRIVE_FILE]);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Jika user sudah punya token, set ke client
        $user = Auth::user();
        if ($user && $user->google_drive_token) {
            $token = json_decode($user->google_drive_token, true);
            $client->setAccessToken($token);
            if ($client->isAccessTokenExpired()) {
                $client->refreshToken($token['refresh_token']);
                $user->google_drive_token = json_encode($client->getAccessToken());
                $user->save();
            }
        }
        return $client;
    }
}