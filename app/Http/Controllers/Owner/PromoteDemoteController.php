<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PromoteDemoteController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->level_id == 1, 403);
        return view('owner.promote-demote');
    }

    public function getData()
    {
        abort_unless(auth()->user()->level_id == 1, 403);

        $users = User::with(['level', 'jabatans'])
            ->orderBy('id')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nim' => $user->nim,
                    'name' => $user->name,
                    'email' => $user->email,
                    'level_name' => $user->level->name ?? 'unknown',
                    'level_id' => $user->level_id,
                    'jabatan_names' => $user->jabatans->pluck('name')->implode(', '),
                    'total_points' => (int) $user->total_points,
                    'is_active' => (bool) $user->is_active,
                    'has_pin' => !empty($user->pin),
                ];
            });

        return response()->json($users);
    }

    public function getJabatans()
    {
        abort_unless(auth()->user()->level_id == 1, 403);

        $jabatans = Jabatan::orderBy('name')->get(['id', 'name']);
        return response()->json($jabatans);
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->level_id == 1, 403);

        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:promote,demote,add_position,remove_position',
            'target_role' => 'nullable|string|in:superadmin,ketua,pengurus,anggota,anggota_umum,pengunjung',
            'jabatan_ids' => 'nullable|array',
            'jabatan_ids.*' => 'exists:jabatans,id',
            'pin_superadmin' => 'nullable|string',
            'new_password' => 'nullable|string|min:4|max:255',
            'new_pin' => 'nullable|string|min:4|max:10',
            'update_security' => 'nullable|boolean',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $isSuperadminTarget = $request->target_role === 'superadmin';
        $isPromote = $request->action === 'promote';
        $isAddPosition = $request->action === 'add_position';
        $isDemote = $request->action === 'demote';
        $isRemovePosition = $request->action === 'remove_position';

        // 🔥 Cek Owner
        if ($users->contains('level_id', 1)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Tidak dapat mengubah akun Owner!'
            ], 400);
        }

        // 🔥 Cek Duplikat Role (user tidak bisa dipromosikan ke role yang sudah dimiliki)
        if ($isPromote || $isAddPosition) {
            foreach ($users as $user) {
                if ($user->level_id == 2 && $isSuperadminTarget) {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Superadmin. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
                if ($user->level_id == 3 && $request->target_role === 'ketua') {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Ketua. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
                if ($user->level_id == 4 && $request->target_role === 'pengurus') {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Pengurus. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
                if ($user->level_id == 5 && $request->target_role === 'anggota') {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Anggota UKM. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
                if ($user->level_id == 6 && $request->target_role === 'anggota_umum') {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Anggota Umum. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
                if ($user->level_id == 7 && $request->target_role === 'pengunjung') {
                    return response()->json([
                        'success' => false,
                        'message' => '⚠️ User "' . $user->name . '" sudah menjadi Pengunjung. Tidak dapat memilih role yang sama.'
                    ], 400);
                }
            }
        }

        // 🔥 Verifikasi Superadmin menggunakan password Owner
        if (($isPromote || $isAddPosition) && $isSuperadminTarget) {
            $owner = auth()->user();
            if (!Hash::check($request->pin_superadmin, $owner->password)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Password Owner salah!'
                ], 400);
            }
        }

        // 🔥 Jika target Superadmin, PIN wajib diisi
        if (($isPromote || $isAddPosition) && $isSuperadminTarget) {
            $newPin = $request->input('new_pin');
            if (empty($newPin) || strlen($newPin) < 4) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ PIN wajib diisi minimal 4 karakter untuk Superadmin.'
                ], 400);
            }
        }

        $levelMap = [
            'superadmin' => 2,
            'ketua' => 3,
            'pengurus' => 4,
            'anggota' => 5,
            'anggota_umum' => 6,
            'pengunjung' => 7,
        ];

        try {
            DB::beginTransaction();

            foreach ($users as $user) {
                $isSuperadminBefore = $user->level_id == 2;
                $isSuperadminAfter = false;

                // 1. Promote / Demote
                if ($isPromote || $isDemote) {
                    $targetLevelId = $levelMap[$request->target_role] ?? null;
                    if ($targetLevelId && $user->level_id != $targetLevelId) {
                        if ($targetLevelId == 1) continue;
                        $user->level_id = $targetLevelId;
                        $isSuperadminAfter = $targetLevelId == 2;
                    }
                }

                // 🔥 Jika Superadmin diturunkan (demote) atau dihapus jabatannya, reset PIN
                if ($isDemote && $isSuperadminBefore && !$isSuperadminAfter) {
                    $user->pin = null;
                }

                // 🔥 Jika remove_position pada Superadmin, reset PIN
                if ($isRemovePosition && $isSuperadminBefore) {
                    $user->pin = null;
                }

                // 2. Tambah / Hapus Jabatan
                if ($isAddPosition && !empty($request->jabatan_ids)) {
                    $currentIds = $user->jabatans()->pluck('jabatan_id')->toArray();
                    $newIds = array_diff($request->jabatan_ids, $currentIds);
                    foreach ($newIds as $jabatanId) {
                        $user->jabatans()->attach($jabatanId, [
                            'is_primary' => false,
                            'status' => 'active',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                            'created_by' => auth()->id(),
                        ]);
                    }
                }

                if ($isRemovePosition && !empty($request->jabatan_ids)) {
                    // Jika user Superadmin, reset PIN
                    if ($user->level_id == 2) {
                        $user->pin = null;
                    }
                    $user->jabatans()->detach($request->jabatan_ids);
                }

                // 3. Update security (password & PIN) jika checkbox dicentang
                if ($request->boolean('update_security')) {
                    $newPass = $request->input('new_password');
                    $newPin = $request->input('new_pin');

                    if (!empty($newPass)) {
                        $user->password = Hash::make($newPass);
                    }
                    if (!empty($newPin)) {
                        $user->pin = Hash::make($newPin);
                    }
                }

                // 🔥 Jika promote ke Superadmin dan security tidak dicentang, tetap set PIN
                if ($isPromote && $isSuperadminTarget) {
                    $newPin = $request->input('new_pin');
                    if (!empty($newPin)) {
                        $user->pin = Hash::make($newPin);
                    }
                }

                $user->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Berhasil memperbarui ' . count($users) . ' akun.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PromoteDemote Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}