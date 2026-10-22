<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\Jabatan;
use App\Exports\PengurusExport;   // ← buat ini juga (lihat step 3)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class PengurusController extends Controller
{
public function index(Request $request)
{
    $me = auth()->id();

    $officials = User::query()
        ->with(['level', 'jabatans', 'university', 'major'])
        ->whereIn('level_id', [3, 4])
        ->whereNull('deleted_at')
        ->when($request->q, function ($q, $search) {
            $q->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$search}%")
                ->orWhere('nim', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        })
        ->when($request->level, fn ($q, $level) => $q->where('level_id', $level))
        ->when($request->status === 'active',   fn ($q) => $q->where('is_active', true))
        ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
        ->orderByRaw('CASE WHEN level_id = 3 THEN 0 ELSE 1 END')
        ->orderBy('name')
        ->paginate(20)
        ->withQueryString();

    $availableRoles = Level::orderBy('id')->get();
    $jabatans       = Jabatan::orderBy('name')->get();

    /* ⬇️⬇️⬇️ TAMBAHKAN INI ⬇️⬇️⬇️ */
    $eligibleCandidates = User::query()
        ->whereIn('level_id', [4, 5])           // Pengurus & Anggota
        ->where('is_active', true)
        ->whereNull('deleted_at')
        ->where('id', '!=', $me)
        ->orderByRaw('CASE WHEN level_id = 4 THEN 0 ELSE 1 END')
        ->orderBy('name')
        ->get(['id', 'name', 'nim', 'email', 'level_id', 'avatar_url'])
        ->map(fn ($u) => [
            'id'         => (int) $u->id,
            'name'       => $u->name,
            'nim'        => $u->nim ?? '-',
            'email'      => $u->email,
            'level_id'   => (int) $u->level_id,
            'level_name' => (int) $u->level_id === 4 ? 'Pengurus' : 'Anggota',
            'avatar_url' => $u->avatar_url ? asset('storage/' . $u->avatar_url) : null,
            'initial'    => strtoupper(mb_substr($u->name, 0, 1)),
        ])
        ->values()
        ->toArray();
    /* ⬆️⬆️⬆️ TAMBAHKAN INI ⬆️⬆️⬆️ */

    return view('ketua.manajemen-pengurus.index', compact(
        'officials', 'availableRoles', 'jabatans', 'eligibleCandidates'
    ));
}
public function setupPin(Request $request)
{
    $validated = $request->validate([
        'current_pin' => 'nullable|string|min:6|max:20',
        'new_pin'     => 'required|string|min:6|max:20|confirmed',
    ]);

    $me = auth()->user();

    if ($me->hasCustomPin()) {
        if (!$me->verifyPin($validated['current_pin'] ?? null)) {
            return response()->json(['success' => false, 'message' => 'PIN lama salah.'], 422);
        }
    } else {
        if (!$me->verifyPin($validated['current_pin'] ?? null)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN default salah. Gunakan 12345678.',
            ], 422);
        }
    }

    $me->update(['pin' => $validated['new_pin']]);

    return response()->json([
        'success' => true,
        'message' => 'PIN keamanan berhasil diperbarui.',
    ]);
}
    /* ═══════════════════════════════════════════════════════
       BULK ACTION — Delete & Export
       ═══════════════════════════════════════════════════════ */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'action'     => 'required|in:delete,export',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();

        /* ─── DELETE ─── */
        if ($validated['action'] === 'delete') {
            $me = auth()->id();
            $deleted = 0;
            $skipped = 0;

            foreach ($users as $user) {
                // proteksi: tidak bisa hapus diri sendiri
                if ($user->id === $me) {
                    $skipped++;
                    continue;
                }
                // proteksi: tidak bisa hapus ketua aktif
                if ((int) $user->level_id === 3) {
                    $skipped++;
                    continue;
                }
                $user->delete();
                $deleted++;
            }

            $msg = "{$deleted} pengurus berhasil dihapus.";
            if ($skipped > 0) {
                $msg .= " {$skipped} data dilewati (diri sendiri / ketua aktif).";
            }

            return response()->json([
                'success' => $deleted > 0,
                'message' => $msg,
            ]);
        }

        /* ─── EXPORT ─── */
        if ($validated['action'] === 'export') {
            return Excel::download(
                new PengurusExport($users),
                'pengurus-' . now()->format('YmdHis') . '.xlsx'
            );
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak dikenal.'], 422);
    }
public function manageRole(Request $request)
{
    $validated = $request->validate([
        'user_ids'        => 'required|array|min:1',
        'user_ids.*'      => 'integer|exists:users,id',
        'action'          => 'required|in:set_level,promote,demote,add_position,remove_position,transfer_ketua',
        'target_level_id' => 'nullable|integer|exists:levels,id',
        'jabatan_ids'     => 'nullable|array',
        'jabatan_ids.*'   => 'integer|exists:jabatans,id',
        'comment'         => 'nullable|string|max:1000',
        'pin'             => 'nullable|string|min:6|max:20',
        'candidate_id'    => 'nullable|integer|exists:users,id',
        // Untuk update keamanan
        'update_security' => 'nullable|boolean',
        'new_password'    => 'nullable|string|min:6|max:255',
        'new_pin'         => 'nullable|string|min:6|max:20',
    ]);

    $action  = $validated['action'];
    $userIds = $validated['user_ids'];
    $authUser = auth()->user();

    /* ═══════════════════════════════════════════════════════
       KHUSUS TRANSFER KETUA
       PENTING: Jangan pakai auth()->user() karena bisa jadi Owner yang explore
       Ambil ketua ASLI dari DB
       ═══════════════════════════════════════════════════════ */
    if ($action === 'transfer_ketua') {

        // 1. Validasi kandidat
        $candidateId = $validated['candidate_id'] ?? null;
        if (!$candidateId) {
            return response()->json([
                'success'    => false,
                'error_type' => 'no_candidate',
                'message'    => 'Silakan pilih 1 calon ketua baru.',
            ], 422);
        }

        // 2. Ambil ketua AKTIF saat ini dari DB
        $currentKetua = User::where('level_id', 3)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$currentKetua) {
            return response()->json([
                'success'    => false,
                'error_type' => 'no_current_ketua',
                'message'    => 'Tidak ada ketua aktif yang bisa ditransfer.',
            ], 422);
        }

        // 3. Tidak boleh transfer ke diri sendiri
        if ((int) $candidateId === (int) $currentKetua->id) {
            return response()->json([
                'success'    => false,
                'error_type' => 'self_transfer',
                'message'    => 'Kandidat sudah menjadi Ketua saat ini.',
            ], 422);
        }

        // 4. Verifikasi PIN dari ketua yang login (kalau bukan owner explore)
        //    Kalau owner explore, skip PIN check (atau pakai PIN owner)
        $pinCheckUser = $authUser;
        if (!$pinCheckUser->verifyPin($validated['pin'] ?? null)) {
            return response()->json([
                'success'    => false,
                'error_type' => 'invalid_pin',
                'message'    => 'PIN keamanan salah.',
            ], 422);
        }

        // 5. Komentar wajib
        if (empty(trim($validated['comment'] ?? ''))) {
            return response()->json([
                'success'    => false,
                'error_type' => 'no_comment',
                'message'    => 'Alasan transfer wajib diisi.',
            ], 422);
        }

        // 6. Ambil kandidat
        $candidate = User::where('id', $candidateId)
            ->whereIn('level_id', [4, 5])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->first();

        if (!$candidate) {
            return response()->json([
                'success'    => false,
                'error_type' => 'invalid_candidate',
                'message'    => 'Kandidat tidak valid atau tidak aktif.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            /* ═══ STEP 1: Demote ketua lama dengan BYPASS guard ═══ */
            $currentKetua->_bypass_ketua_guard = true;
            $currentKetua->update(['level_id' => 4]);
            unset($currentKetua->_bypass_ketua_guard);

            /* ═══ STEP 2: Promote kandidat dengan BYPASS guard ═══ */
            // (karena ketua lama sudah di-demote, harusnya OK, 
            //  tapi kita bypass saja untuk keamanan)
            $candidate->_bypass_ketua_guard = true;
            $candidate->update(['level_id' => 3]);
            unset($candidate->_bypass_ketua_guard);

            /* ═══ STEP 3: Audit log ═══ */
            \Log::warning('[KETUA TRANSFER]', [
                'from_id'     => $currentKetua->id,
                'from_name'   => $currentKetua->name,
                'to_id'       => $candidate->id,
                'to_name'     => $candidate->name,
                'reason'      => $validated['comment'],
                'executed_by' => $authUser->id,
                'executor'    => $authUser->name,
                'ip'          => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'at'          => now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'success'           => true,
                'message'           => "Jabatan Ketua berhasil ditransfer ke {$candidate->name}.",
                'redirect_to_login' => true,
                'redirect_url'      => route('login'),
                'transfer_to'       => [
                    'name'  => $candidate->name,
                    'email' => $candidate->email,
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('[KETUA TRANSFER FAILED]', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Transfer gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════
       AKSI LAIN: set_level, promote, demote, add/remove_position
       ═══════════════════════════════════════════════════════ */

    // Cek konflik Ketua
    if (in_array($action, ['set_level', 'promote'])
        && (int) ($validated['target_level_id'] ?? 0) === 3) {

        $existing = User::where('level_id', 3)
            ->whereNotIn('id', $userIds)
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return response()->json([
                'success'        => false,
                'conflict_type'  => 'multiple_ketua',
                'message'        => 'Sudah ada Ketua aktif. Gunakan Transfer Jabatan Ketua.',
                'existing_ketua' => [
                    'name'  => $existing->name,
                    'email' => $existing->email,
                ],
            ]);
        }
    }

    DB::beginTransaction();
    try {
        $users = User::whereIn('id', $userIds)->get();

        switch ($action) {

            case 'set_level':
                foreach ($users as $user) {
                    // Bypass guard kalau target jadi level 3
                    if ((int) $validated['target_level_id'] === 3) {
                        $user->_bypass_ketua_guard = true;
                    }
                    $user->update(['level_id' => $validated['target_level_id']]);
                    unset($user->_bypass_ketua_guard);
                }
                $msg = "Level {$users->count()} user berhasil diubah.";
                break;

            case 'promote':
                foreach ($users as $user) {
                    $currentLevel = (int) $user->level_id;
                    $newLevel = match ($currentLevel) {
                        6 => 5,  // Pengunjung → Anggota Umum
                        5 => 4,  // Anggota → Pengurus
                        4 => 3,  // Pengurus → Ketua
                        default => $currentLevel,
                    };
                    if ($newLevel === 3) {
                        $user->_bypass_ketua_guard = true;
                    }
                    $user->update(['level_id' => $newLevel]);
                    unset($user->_bypass_ketua_guard);
                }
                $msg = "{$users->count()} user berhasil dipromosikan.";
                break;

            case 'demote':
                foreach ($users as $user) {
                    $currentLevel = (int) $user->level_id;
                    $newLevel = match ($currentLevel) {
                        3 => 4,  // Ketua → Pengurus
                        4 => 5,  // Pengurus → Anggota
                        5 => 6,  // Anggota → Pengunjung
                        default => $currentLevel,
                    };
                    $user->update(['level_id' => $newLevel]);
                }
                $msg = "{$users->count()} user berhasil diturunkan.";
                break;

            case 'add_position':
                $jabatanIds = $validated['jabatan_ids'] ?? [];
                foreach ($users as $user) {
                    $user->jabatans()->syncWithoutDetaching($jabatanIds);
                }
                $msg = "Jabatan berhasil ditambahkan ke {$users->count()} user.";
                break;

            case 'remove_position':
                $jabatanIds = $validated['jabatan_ids'] ?? [];
                foreach ($users as $user) {
                    $user->jabatans()->detach($jabatanIds);
                }
                $msg = "Jabatan berhasil dikurangi dari {$users->count()} user.";
                break;

            default:
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Aksi tidak dikenal.',
                ], 422);
        }

        /* ═══ Update keamanan opsional ═══ */
        if (!empty($validated['update_security'])) {
            foreach ($users as $user) {
                $updates = [];
                if (!empty($validated['new_password'])) {
                    $updates['password'] = bcrypt($validated['new_password']);
                }
                if (!empty($validated['new_pin'])) {
                    $updates['pin'] = $validated['new_pin']; // auto-hash via mutator
                }
                if ($updates) $user->update($updates);
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => $msg]);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('[MANAGE ROLE FAILED]', [
            'action' => $action,
            'error'  => $e->getMessage(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ], 500);
    }
}
    /* ═══════════════════════════════════════════════════════
       CREATE & SHOW (placeholder)
       ═══════════════════════════════════════════════════════ */
    public function create()
    {
        // return view('ketua.manajemen-pengurus.create', [...]);
        return view('ketua.manajemen-pengurus.create');
    }

    public function show($id)
    {
        $official = User::with(['university', 'major', 'jabatans', 'level'])->findOrFail($id);
        return view('ketua.manajemen-pengurus.show', compact('official'));
    }
}