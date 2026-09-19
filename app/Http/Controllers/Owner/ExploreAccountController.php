<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ExploreAccountController extends Controller
{
    /**
     * Pastikan hanya Owner (level_id = 1).
     */
    private function authorizeOwner(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->level_id == 1,
            403,
            'Fitur Jelajahi Akun hanya dapat digunakan oleh Owner.'
        );
    }

    /**
     * Halaman utama Explorer.
     */
    public function index()
    {
        $this->authorizeOwner();
        return view('owner.settings.explore.index');
    }

    /**
     * Cari akun berdasarkan NIM atau nama.
     */
    public function search(Request $request)
    {
        $this->authorizeOwner();

        $request->validate([
            'keyword' => 'required|string|max:100',
        ]);

        $keyword = trim($request->keyword);

        $users = User::query()
            ->with('level')
            ->where(function ($query) use ($keyword) {
                $query->where('nim', 'LIKE', "%{$keyword}%")
                    ->orWhere('name', 'LIKE', "%{$keyword}%");
            })
            ->where('level_id', '!=', 1) // Owner tidak bisa dijelajahi
            ->limit(20)
            ->get();

        // Jika hanya 1 hasil, langsung redirect ke halaman verifikasi
        if ($users->count() == 1) {
            $user = $users->first();
            return redirect()->route('owner.settings.explore.verify', ['user_id' => $user->id]);
        }

        return view('owner.settings.explore.results', compact('users', 'keyword'));
    }

/**
 * Halaman verifikasi password Explorer.
 */
public function showVerifyForm(Request $request)
{
    $this->authorizeOwner();

    $userId = $request->input('user_id');
    abort_unless($userId, 400, 'User ID diperlukan.');

    $user = User::find($userId);
    abort_unless($user, 404, 'User tidak ditemukan.');
    abort_if($user->level_id == 1, 403, 'Akun Owner tidak dapat dijelajahi.');

    return view('owner.settings.explore.verify', compact('user'));
}
    /**
     * Proses verifikasi password Explorer.
     */
    public function verify(Request $request)
    {
        $this->authorizeOwner();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => [
                'required',
                'string',
                'regex:/^[a-z0-9]+$/', // hanya huruf kecil dan angka
            ],
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'Password Explorer hanya boleh menggunakan huruf kecil dan angka tanpa spasi.',
            'password_confirmation.same' => 'Konfirmasi password tidak sesuai.',
        ]);

        // Ambil password dari .env
        $explorerPassword = config('explorer.password');

        // Bandingkan secara aman
        abort_unless(
            hash_equals($explorerPassword, $request->password),
            403,
            'Password Explorer salah.'
        );

        $targetUser = User::findOrFail($request->user_id);

        abort_if(
            $targetUser->level_id == 1,
            403,
            'Akun Owner tidak dapat dijelajahi.'
        );

        // Simpan session Explorer
        session([
            'owner_explorer.active' => true,
            'owner_explorer.target_user_id' => $targetUser->id,
            'owner_explorer.target_user_name' => $targetUser->name,
            'owner_explorer.target_user_level' => $targetUser->level_id,
            'owner_explorer.started_at' => now()->timestamp,
        ]);

        // Log aktivitas
        Log::info('Owner mulai menjelajahi akun', [
            'owner_id' => auth()->id(),
            'owner_name' => auth()->user()->name,
            'target_user_id' => $targetUser->id,
            'target_user_name' => $targetUser->name,
            'timestamp' => now(),
        ]);

        return redirect()->route('owner.settings.explore.account', ['user' => $targetUser->id]);
    }

    /**
     * Halaman akun yang sedang dijelajahi.
     */
    public function explore(User $user)
    {
        $this->authorizeOwner();

        // Cek session Explorer
        abort_unless(
            session('owner_explorer.active') === true,
            403,
            'Mode Explorer belum diaktifkan.'
        );

        abort_unless(
            session('owner_explorer.target_user_id') == $user->id,
            403,
            'Akun Explorer tidak valid.'
        );

        abort_if(
            $user->level_id == 1,
            403,
            'Akun Owner tidak dapat dijelajahi.'
        );

        return view('owner.settings.explore.account', compact('user'));
    }

    /**
     * Keluar dari mode Explorer.
     */
    public function exitExplorer()
    {
        $this->authorizeOwner();

        // Log aktivitas
        Log::info('Owner keluar dari mode Explorer', [
            'owner_id' => auth()->id(),
            'owner_name' => auth()->user()->name,
            'target_user_id' => session('owner_explorer.target_user_id'),
            'target_user_name' => session('owner_explorer.target_user_name'),
            'timestamp' => now(),
        ]);

        session()->forget('owner_explorer');

        return redirect()->route('owner.settings.explore.index')
            ->with('success', 'Anda telah keluar dari mode Jelajah Akun.');
    }
}