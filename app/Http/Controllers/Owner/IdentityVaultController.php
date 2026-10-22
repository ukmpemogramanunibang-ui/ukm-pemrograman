<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\IdentityVault\IdentityRegistry;
use App\Models\IdentityVault\CredentialAudit;
use App\Services\IdentityVaultService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class IdentityVaultController extends Controller
{
    protected $vault;

    public function __construct(IdentityVaultService $vault)
    {
        $this->vault = $vault;
    }

    public function index()
    {
        $identities = IdentityRegistry::with('nfcCredentials')->paginate(20);
        $total = IdentityRegistry::count();
        $active = IdentityRegistry::where('status', 'active')->count();
        $blocked = IdentityRegistry::where('status', 'blocked')->count();

        return view('owner.identity-vault.index', compact('identities', 'total', 'active', 'blocked'));
    }

    public function show($uuid)
    {
        $identity = IdentityRegistry::where('credential_uuid', $uuid)->firstOrFail();
        return view('owner.identity-vault.show', compact('identity'));
    }

    public function backup(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::find($request->user_id);

        $identity = IdentityRegistry::where('user_uuid', $user->user_uuid)->first();
        if (!$identity) {
            // Buat identitas baru
            $identity = $this->vault->createIdentity($user);
        } else {
            // Update snapshot
            $this->vault->updateSnapshot($identity, $user);
        }

        $this->vault->logAudit($identity->credential_uuid, $user->user_uuid, 'backup_manual', ['by' => auth()->id()]);

        return redirect()->back()->with('success', 'Backup identitas berhasil.');
    }

    public function recover(Request $request)
    {
        $credentialUuid = $request->input('credential_uuid');
        $identity = IdentityRegistry::where('credential_uuid', $credentialUuid)->first();
        if (!$identity) {
            return redirect()->back()->with('error', 'Identitas tidak ditemukan.');
        }

        $user = $this->vault->recoverIdentity($identity);
        if ($user) {
            $this->vault->logAudit($identity->credential_uuid, $user->user_uuid, 'recovery_manual', ['by' => auth()->id()]);
            return redirect()->back()->with('success', 'Akun berhasil dipulihkan.');
        }

        return redirect()->back()->with('error', 'Gagal memulihkan akun.');
    }

    public function recoverAll()
    {
        $results = $this->vault->recoverAllIdentities();
        $success = array_filter($results, fn($r) => $r === 'success');
        $failed = array_filter($results, fn($r) => $r !== 'success');

        return redirect()->back()->with('info', "Recovery massal selesai: Berhasil " . count($success) . ", Gagal " . count($failed));
    }

    public function audit($uuid = null)
    {
        $query = CredentialAudit::with([]);
        if ($uuid) {
            $query->where('credential_uuid', $uuid);
        }
        $logs = $query->orderBy('created_at', 'desc')->paginate(30);
        return view('owner.identity-vault.audit', compact('logs'));
    }

    public function testRecovery()
    {
        // Simulasi recovery ke database sementara (misal ukm_recovery_test)
        // Di sini kita hanya menjalankan recovery dan menampilkan hasil
        $results = $this->vault->recoverAllIdentities();
        return view('owner.identity-vault.test', compact('results'));
    }
}