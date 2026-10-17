<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Level;
use App\Notifications\RoleChangedNotification;
use App\Notifications\KetuaTransferredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MemberManagementController extends Controller
{
    /* ═══════════════════════════════════════════════════════════
       INDEX — Daftar anggota dengan filter
       ═══════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        try {
            $query = User::query()
                ->whereNull('deleted_at')
                ->with(['level', 'university', 'major', 'jabatans']);

            // Default: only anggota (level_id = 5)
            if (!$request->filled('level')) {
                $query->where('level_id', 5);
            } elseif ($request->input('level') !== 'all') {
                $query->where('level_id', (int) $request->input('level'));
            }

            // Search
            if ($q = $request->input('q')) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('nim', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            }

            // Status filter
            if ($status = $request->input('status')) {
                $query->where('is_active', $status === 'active');
            }

            // Gender filter
            if ($gender = $request->input('gender')) {
                $query->where('gender', $gender);
            }

            $members = $query->latest()->paginate(20);

        } catch (\Throwable $e) {
            Log::warning('[Ketua] manajemen-anggota error', ['error' => $e->getMessage()]);
            $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        // Stats
        $stats = [
            'total'     => User::whereNull('deleted_at')->where('level_id', 5)->count(),
            'active'    => User::whereNull('deleted_at')->where('level_id', 5)->where('is_active', true)->count(),
            'male'      => User::whereNull('deleted_at')->where('level_id', 5)->where('gender', 'L')->count(),
            'female'    => User::whereNull('deleted_at')->where('level_id', 5)->where('gender', 'P')->count(),
            'pengurus'  => User::whereNull('deleted_at')->where('level_id', 4)->count(),
            'ketua'     => User::whereNull('deleted_at')->where('level_id', 3)->count(),
        ];

        // Roles tersedia untuk manage role
        $availableRoles = $this->getAvailableRoles();
        $jabatans = Jabatan::orderBy('name')->get();

        // Cek apakah user saat ini ketua
        $currentUser = Auth::user();
        $isKetua = (int) $currentUser->level_id === 3;

        return view('ketua.manajemen-anggota.index', compact(
            'members', 'stats', 'availableRoles', 'jabatans', 'isKetua'
        ));
    }

    /* ═══════════════════════════════════════════════════════════
       DATA JSON — Untuk AJAX
       ═══════════════════════════════════════════════════════════ */
    public function data(Request $request)
    {
        try {
            $query = User::query()
                ->whereNull('deleted_at')
                ->with(['level', 'jabatans', 'university', 'major']);

            if ($request->filled('level') && $request->input('level') !== 'all') {
                $query->where('level_id', (int) $request->input('level'));
            }

            $users = $query->latest()->limit(500)->get()->map(function ($u) {
                return [
                    'id'             => $u->id,
                    'name'           => $u->name,
                    'nim'            => $u->nim,
                    'email'          => $u->email,
                    'avatar_url'     => $u->avatar_url,
                    'level_id'       => $u->level_id,
                    'level_name'     => optional($u->level)->name,
                    'is_active'      => (bool) $u->is_active,
                    'is_verified'    => (bool) ($u->is_verified ?? false),
                    'gender'         => $u->gender,
                    'university'     => optional($u->university)->name,
                    'major'          => optional($u->major)->name,
                    'has_pin'        => !empty($u->pin),
                    'has_password'   => !empty($u->password),
                    'total_points'   => (int) ($u->total_points ?? 0),
                    'jabatans'       => $u->jabatans->map(fn($j) => ['id' => $j->id, 'name' => $j->name])->toArray(),
                ];
            });

            return response()->json($users);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       SHOW — Detail user
       ═══════════════════════════════════════════════════════════ */
    public function show(int $id)
    {
        $member = User::with(['level', 'university', 'major', 'jabatans'])->findOrFail($id);
        return view('ketua.manajemen-anggota.show', compact('member'));
    }

    /* ═══════════════════════════════════════════════════════════
       BULK ACTION — Export / Delete / Block / Freeze / Alumni
       ═══════════════════════════════════════════════════════════ */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'action'     => 'required|in:export,delete,block,unblock,freeze,unfreeze,alumni',
            'reason'     => 'nullable|string|max:500',
        ]);

        $userIds = $validated['user_ids'];
        $action  = $validated['action'];
        $reason  = $validated['reason'] ?? null;

        // Tidak boleh include diri sendiri (ketua) atau owner
        $protected = User::whereIn('id', $userIds)
            ->whereIn('level_id', [1, 2]) // owner, superadmin
            ->pluck('id')
            ->toArray();

        if (in_array(Auth::id(), $userIds)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat memilih akun sendiri.'], 422);
        }

        if (!empty($protected)) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengubah akun Owner/Superadmin.'], 422);
        }

        try {
            DB::beginTransaction();

            $affected = 0;
            switch ($action) {
                case 'export':
                    return $this->exportSelected($userIds);

                case 'delete':
                    User::whereIn('id', $userIds)->delete();
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil dihapus.";
                    break;

                case 'block':
                    User::whereIn('id', $userIds)->update(['is_active' => false]);
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil diblokir.";
                    break;

                case 'unblock':
                    User::whereIn('id', $userIds)->update(['is_active' => true]);
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil diaktifkan.";
                    break;

                case 'freeze':
                    User::whereIn('id', $userIds)->update([
                        'is_active' => false,
                        'locked_until' => now()->addDays(30),
                    ]);
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil dibekukan 30 hari.";
                    break;

                case 'unfreeze':
                    User::whereIn('id', $userIds)->update([
                        'is_active' => true,
                        'locked_until' => null,
                    ]);
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil diaktifkan kembali.";
                    break;

                case 'alumni':
                    // Set level to anggota umum atau status alumni
                    $alumniLevel = Level::where('name', 'like', '%alumni%')->first();
                    if ($alumniLevel) {
                        User::whereIn('id', $userIds)->update(['level_id' => $alumniLevel->id]);
                    } else {
                        User::whereIn('id', $userIds)->update(['is_active' => false]);
                    }
                    $affected = count($userIds);
                    $msg = "{$affected} akun berhasil dialumnikan.";
                    break;
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $msg, 'affected' => $affected]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Ketua] bulk action error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       MANAGE ROLE — Ubah level & jabatan
       ═══════════════════════════════════════════════════════════ */
    public function manageRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids'           => 'required|array|min:1',
            'user_ids.*'         => 'exists:users,id',
            'action'             => 'required|in:promote,demote,set_level,add_position,remove_position,transfer_ketua',
            'target_level_id'    => 'nullable|integer|exists:levels,id',
            'jabatan_ids'        => 'nullable|array',
            'jabatan_ids.*'      => 'exists:jabatans,id',
            'comment'            => 'nullable|string|max:1000',
            'confirm_ketua_transfer' => 'nullable|boolean',
        ]);

        $userIds = $validated['user_ids'];
        $action  = $validated['action'];

        // Proteksi akun owner / superadmin
        $protected = User::whereIn('id', $userIds)->whereIn('level_id', [1, 2])->count();
        if ($protected > 0) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengubah akun Owner/Superadmin.'], 422);
        }

        // Kalau ada user yang sama dengan diri sendiri & mencoba set ke bukan ketua
        if (in_array(Auth::id(), $userIds) && $action !== 'transfer_ketua') {
            return response()->json(['success' => false, 'message' => 'Gunakan menu Transfer Ketua untuk memindahkan jabatan Anda.'], 422);
        }

        try {
            DB::beginTransaction();

            $ketuaLevelId = Level::where('name', 'ketua')->value('id') 
                         ?? Level::where('id', 3)->value('id');

            /* ═══ VALIDASI KHUSUS: TARGET KETUA ═══ */
            if ($action === 'set_level' && (int) $validated['target_level_id'] === (int) $ketuaLevelId) {
                return $this->handleKetuaPromotion($userIds, $validated);
            }

            /* ═══ TRANSFER KETUA (dari ketua saat ini ke user baru) ═══ */
            if ($action === 'transfer_ketua') {
                return $this->handleKetuaTransfer($userIds, $validated);
            }

            /* ═══ AKSI LAIN (promote/demote/set_level/add_position/remove_position) ═══ */
            $affected = 0;
            $message = '';

            foreach ($userIds as $uid) {
                $user = User::find($uid);
                if (!$user) continue;

                switch ($action) {
                    case 'promote':
                        $newLevel = $this->getNextLevelUp($user->level_id);
                        if ($newLevel && $newLevel->id != $ketuaLevelId) {
                            $user->update(['level_id' => $newLevel->id]);
                            $affected++;
                        }
                        break;

                    case 'demote':
                        $newLevel = $this->getNextLevelDown($user->level_id);
                        if ($newLevel) {
                            $user->update(['level_id' => $newLevel->id]);
                            $affected++;
                        }
                        break;

                    case 'set_level':
                        if (!empty($validated['target_level_id'])) {
                            $user->update(['level_id' => $validated['target_level_id']]);
                            $affected++;
                        }
                        break;

                    case 'add_position':
                        if (!empty($validated['jabatan_ids'])) {
                            foreach ($validated['jabatan_ids'] as $jid) {
                                DB::table('jabatan_user')->updateOrInsert(
                                    ['user_id' => $uid, 'jabatan_id' => $jid],
                                    ['status' => 'active', 'start_date' => now(), 'created_at' => now(), 'updated_at' => now()]
                                );
                            }
                            $affected++;
                        }
                        break;

                    case 'remove_position':
                        if (!empty($validated['jabatan_ids'])) {
                            DB::table('jabatan_user')
                                ->where('user_id', $uid)
                                ->whereIn('jabatan_id', $validated['jabatan_ids'])
                                ->delete();
                            $affected++;
                        }
                        break;
                }

                // Kirim notifikasi ke user
                try {
                    if (method_exists($user, 'notify')) {
                        $user->notify(new RoleChangedNotification(
                            $action,
                            Auth::user()->name,
                            $validated['comment'] ?? null
                        ));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Notif fail', ['error' => $e->getMessage()]);
                }
            }

            DB::commit();

            $msg = match($action) {
                'promote'         => "{$affected} user berhasil dinaikkan pangkatnya.",
                'demote'          => "{$affected} user berhasil diturunkan pangkatnya.",
                'set_level'       => "{$affected} user berhasil diubah levelnya.",
                'add_position'    => "{$affected} user berhasil ditambahkan jabatannya.",
                'remove_position' => "{$affected} user berhasil dikurangi jabatannya.",
                default           => "{$affected} user berhasil diperbarui.",
            };

            return response()->json(['success' => true, 'message' => $msg, 'affected' => $affected]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Ketua] manage role error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       HANDLE KETUA PROMOTION — Cek konflik ketua
       ═══════════════════════════════════════════════════════════ */
    protected function handleKetuaPromotion(array $userIds, array $data)
    {
        $ketuaLevelId = Level::where('name', 'ketua')->value('id') 
                     ?? Level::where('id', 3)->value('id');

        // Cek apakah salah satu user sudah jadi ketua
        $alreadyKetua = User::whereIn('id', $userIds)->where('level_id', $ketuaLevelId)->exists();
        if ($alreadyKetua) {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih sudah memiliki role Ketua.',
                'conflict_type' => 'already_ketua',
            ], 422);
        }

        // Cek apakah sudah ada ketua lain (selain user yang dipilih)
        $existingKetua = User::where('level_id', $ketuaLevelId)
            ->whereNotIn('id', $userIds)
            ->whereNull('deleted_at')
            ->first();

        if ($existingKetua) {
            return response()->json([
                'success' => false,
                'message' => "Sudah ada Ketua aktif: {$existingKetua->name}. Harus diturunkan terlebih dahulu atau gunakan fitur Transfer Ketua.",
                'conflict_type' => 'multiple_ketua',
                'existing_ketua' => [
                    'id'    => $existingKetua->id,
                    'name'  => $existingKetua->name,
                    'email' => $existingKetua->email,
                ],
            ], 422);
        }

        // Aman: update semua user jadi ketua
        DB::beginTransaction();
        try {
            foreach ($userIds as $uid) {
                $user = User::find($uid);
                if ($user) {
                    $user->update(['level_id' => $ketuaLevelId]);
                    try {
                        $user->notify(new RoleChangedNotification('set_ketua', Auth::user()->name, $data['comment'] ?? null));
                    } catch (\Throwable $e) {}
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($userIds) . ' user berhasil dijadikan Ketua.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       HANDLE KETUA TRANSFER — Pindah jabatan ketua
       ═══════════════════════════════════════════════════════════ */
    protected function handleKetuaTransfer(array $userIds, array $data)
    {
        $currentUser = Auth::user();
        $ketuaLevelId = Level::where('name', 'ketua')->value('id') 
                     ?? Level::where('id', 3)->value('id');

        // Cek apakah user saat ini benar ketua
        if ((int) $currentUser->level_id !== (int) $ketuaLevelId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan Ketua aktif. Tidak dapat mentransfer jabatan.',
            ], 422);
        }

        if (empty($userIds)) {
            return response()->json(['success' => false, 'message' => 'Pilih user penerima jabatan.'], 422);
        }

        if (count($userIds) > 1) {
            return response()->json(['success' => false, 'message' => 'Hanya dapat transfer ke 1 user.'], 422);
        }

        $targetId = $userIds[0];
        if ($targetId === $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat transfer ke diri sendiri.'], 422);
        }

        $targetUser = User::find($targetId);
        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'User target tidak ditemukan.'], 422);
        }

        // Cek target bukan owner / superadmin
        if (in_array((int) $targetUser->level_id, [1, 2])) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat transfer ke Owner/Superadmin.'], 422);
        }

        $comment = $data['comment'] ?? 'Transfer jabatan Ketua.';

        DB::beginTransaction();
        try {
            // 1) Turunkan ketua saat ini (jadi pengurus inti = level 4)
            $pengurusLevelId = Level::where('name', 'pengurus')->value('id') 
                            ?? Level::where('id', 4)->value('id');

            $currentUser->update(['level_id' => $pengurusLevelId]);

            // 2) Naikkan target ke ketua
            $targetUser->update(['level_id' => $ketuaLevelId]);

            // 3) Kirim notifikasi ke target
            try {
                if (method_exists($targetUser, 'notify')) {
                    $targetUser->notify(new KetuaTransferredNotification(
                        Auth::user()->name,
                        $comment
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning('Notif transfer fail', ['error' => $e->getMessage()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Jabatan Ketua berhasil ditransfer ke {$targetUser->name}. Anda akan logout otomatis.",
                'redirect_to_login' => true,
                'logout_url' => route('logout'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       EXPORT SELECTED — CSV download
       ═══════════════════════════════════════════════════════════ */
    protected function exportSelected(array $userIds)
    {
        $users = User::whereIn('id', $userIds)->with(['level', 'university', 'major'])->get();

        $filename = 'anggota-export-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($handle, ['ID', 'Nama', 'NIM', 'Email', 'Telepon', 'Level', 'Universitas', 'Prodi', 'Status']);
            foreach ($users as $u) {
                fputcsv($handle, [
                    $u->id,
                    $u->name,
                    $u->nim,
                    $u->email,
                    $u->phone,
                    optional($u->level)->name,
                    optional($u->university)->name,
                    optional($u->major)->name,
                    $u->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ═══════════════════════════════════════════════════════════
       HELPERS
       ═══════════════════════════════════════════════════════════ */
    protected function getAvailableRoles()
    {
        return Level::whereIn('id', [3, 4, 5, 6])
            ->orderBy('id')
            ->get()
            ->map(fn($l) => ['id' => $l->id, 'name' => $l->name])
            ->toArray();
    }

    protected function getNextLevelUp(int $currentLevelId)
    {
        // Owner=1, Superadmin=2, Ketua=3, Pengurus=4, Anggota=5, Pengunjung=6
        // Promote: 6→5, 5→4, 4→3 (dengan validasi ketua di handleKetuaPromotion)
        if ($currentLevelId <= 3) return null;
        return Level::find($currentLevelId - 1);
    }

    protected function getNextLevelDown(int $currentLevelId)
    {
        if ($currentLevelId >= 6) return null;
        return Level::find($currentLevelId + 1);
    }
}