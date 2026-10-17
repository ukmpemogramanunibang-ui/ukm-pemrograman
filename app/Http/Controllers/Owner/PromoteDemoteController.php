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
use Illuminate\Validation\Rule;

class PromoteDemoteController extends Controller
{
    /* ═══════════════════════════════════════════════════════
       MAPPING ROLE KEY → NAMA LEVEL DI DATABASE
       ═══════════════════════════════════════════════════════ */
    private const ROLE_TO_LEVEL_NAME = [
        'superadmin'   => 'superadmin',
        'ketua'        => 'ketua',
        'pengurus'     => 'pengurus',
        'pj'           => 'pj',
        'anggota'      => 'anggota',
        'anggota_umum' => 'anggota_umum',
        'pengunjung'   => 'guest',
    ];

    private const OWNER_LEVEL_ID      = 1;
    private const SUPERADMIN_LEVEL_ID = 2;
    private const KETUA_LEVEL_ID      = 3;

    /* ═══════════════════════════════════════════════════════
       VIEW
       ═══════════════════════════════════════════════════════ */
    public function index()
    {
        $this->authorizeOwner();
        return view('owner.promote-demote');
    }

    /* ═══════════════════════════════════════════════════════
       DATA USER (dengan pivot jabatan lengkap)
       ═══════════════════════════════════════════════════════ */
    public function getData()
    {
        $this->authorizeOwner();

        $users = User::with([
                'level',
                'jabatans' => function ($q) {
                    $q->withPivot([
                        'is_primary',
                        'status',
                        'start_date',
                        'end_date',
                        'approved_at',
                    ])->orderByPivot('is_primary', 'desc');
                },
            ])
            ->orderByRaw('CASE WHEN level_id = 1 THEN 0 ELSE 1 END')
            ->orderBy('level_id')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $primary = $user->jabatans->firstWhere('pivot.is_primary', true)
                        ?? $user->jabatans->first();

                return [
                    'id'             => $user->id,
                    'nim'            => $user->nim,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'profile_photo'  => $user->avatar_url
                                        ? asset('storage/' . $user->avatar_url)
                                        : null,
                    'level_id'       => (int) $user->level_id,
                    'level_name'     => $user->level->name ?? 'unknown',
                    'level_display'  => $user->level->display_name ?? null,
                    'jabatan_names'  => $user->jabatans->pluck('name')->values()->all(),

                    /* ⬇️ detail lengkap per jabatan ⬇️ */
                    'jabatans'       => $user->jabatans->map(fn ($j) => [
                        'id'         => $j->id,
                        'name'       => $j->name,
                        'is_primary' => (bool) ($j->pivot->is_primary ?? false),
                        'status'     => $j->pivot->status ?? 'active',
                        'start_date' => $j->pivot->start_date,
                        'end_date'   => $j->pivot->end_date,
                        'approved_at'=> $j->pivot->approved_at,
                    ])->values()->all(),

                    /* shortcut untuk cell tabel */
                    'primary_jabatan' => $primary ? [
                        'name'       => $primary->name,
                        'is_primary' => (bool) ($primary->pivot->is_primary ?? false),
                        'start_date' => $primary->pivot->start_date,
                        'end_date'   => $primary->pivot->end_date,
                    ] : null,

                    'total_points'   => (int) ($user->total_points ?? 0),
                    'is_active'      => (bool) $user->is_active,
                    'has_pin'        => !empty($user->pin),
                    'has_password'   => !empty($user->password),
                ];
            });

        return response()->json($users);
    }

    /* ═══════════════════════════════════════════════════════
       DATA JABATAN (untuk modal)
       ═══════════════════════════════════════════════════════ */
    public function getJabatans()
    {
        $this->authorizeOwner();

        return response()->json(
            Jabatan::orderBy('name')->get(['id', 'name'])
        );
    }

    /* ═══════════════════════════════════════════════════════
       UPDATE (Naik/Turun Level, Add/Remove Position, Update Security)
       ═══════════════════════════════════════════════════════ */
    public function update(Request $request)
    {
        $this->authorizeOwner();

        $validated = $this->validateRequest($request);

        $users = User::with('jabatans')
            ->whereIn('id', $validated['user_ids'])
            ->get();

        if ($users->isEmpty()) {
            return $this->fail('User tidak ditemukan.');
        }

        /* ─── Proteksi Owner ─── */
        if ($users->contains('level_id', self::OWNER_LEVEL_ID)) {
            return $this->fail('Tidak dapat mengubah akun Owner.');
        }

        $action             = $validated['action'];
        $targetRole         = $validated['target_role'] ?? null;
        $isPromote          = $action === 'promote';
        $isDemote           = $action === 'demote';
        $isAddPosition      = $action === 'add_position';
        $isRemovePosition   = $action === 'remove_position';
        $isSuperadminTarget = $targetRole === 'superadmin';

        /* ─── Resolve target level ID ─── */
        $targetLevelId = $this->resolveTargetLevelId($targetRole);

        if ($isPromote || $isDemote) {
            if (!$targetRole) {
                return $this->fail('Target level wajib dipilih untuk aksi naik/turun.');
            }
            if (!$targetLevelId) {
                return $this->fail("Level '{$targetRole}' belum terdaftar. Jalankan seeder terlebih dahulu.");
            }
            if ($targetLevelId === self::OWNER_LEVEL_ID) {
                return $this->fail('Target level tidak boleh Owner.');
            }
        }

        /* ─── Cek duplikat (promote saja) ─── */
        if ($isPromote && $targetLevelId) {
            $dupe = $users->firstWhere('level_id', $targetLevelId);
            if ($dupe) {
                return $this->fail("User \"{$dupe->name}\" sudah berada di level tersebut.");
            }
        }

        /* ─── Verifikasi password Owner untuk promosi Superadmin ─── */
        if ($isSuperadminTarget && ($isPromote || $isAddPosition)) {
            $ownerPassword = (string) ($validated['pin_superadmin'] ?? '');
            if (!Hash::check($ownerPassword, auth()->user()->password)) {
                return $this->fail('Password Owner salah.');
            }
        }

        /* ─── PIN wajib untuk Superadmin baru ─── */
        if ($isPromote && $isSuperadminTarget) {
            $pin = $validated['new_pin'] ?? null;
            if (empty($pin) || strlen($pin) < 4) {
                return $this->fail('PIN minimal 4 karakter wajib untuk Superadmin baru.');
            }
        }

        /* ─── Jabatan wajib untuk aksi jabatan ─── */
        if (($isAddPosition || $isRemovePosition) && empty($validated['jabatan_ids'])) {
            return $this->fail('Pilih minimal satu jabatan.');
        }

        /* ═══════════════════════════════════════════════════════
           EKSEKUSI
           ═══════════════════════════════════════════════════════ */
        try {
            DB::beginTransaction();

            $processed = 0;
            $skipped   = 0;
            $details   = [];

            $flags = [
                'is_promote'           => $isPromote,
                'is_demote'            => $isDemote,
                'is_add_position'      => $isAddPosition,
                'is_remove_position'   => $isRemovePosition,
                'is_superadmin_target' => $isSuperadminTarget,
            ];

            foreach ($users as $user) {
                $result = $this->applyChanges($user, $validated, $targetLevelId, $flags);
                if ($result['changed']) {
                    $processed++;
                    $details[] = "{$user->name}: " . implode(', ', $result['notes']);
                } else {
                    $skipped++;
                }
            }

            /* ─── Audit log ─── */
            Log::info('[PromoteDemote]', [
                'actor_id'    => auth()->id(),
                'actor_name'  => auth()->user()->name,
                'action'      => $action,
                'target_role' => $targetRole,
                'user_count'  => $processed,
                'details'     => $details,
                'ip'          => $request->ip(),
            ]);

            DB::commit();

            $message = "✅ Berhasil memperbarui {$processed} akun.";
            if ($skipped > 0) {
                $message .= " {$skipped} akun dilewati (tidak ada perubahan).";
            }

            return response()->json([
                'success'   => true,
                'message'   => $message,
                'processed' => $processed,
                'skipped'   => $skipped,
                'details'   => $details,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[PromoteDemote] FAILED', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
                'actor'   => auth()->id(),
                'payload' => $request->except(['pin_superadmin', 'new_password', 'new_pin']),
            ]);

            return response()->json([
                'success' => false,
                'message' => '❌ Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ═══════════════════════════════════════════════════════
       PRIVATE HELPERS
       ═══════════════════════════════════════════════════════ */

    private function authorizeOwner(): void
    {
        abort_unless(
            auth()->check() && (int) auth()->user()->level_id === self::OWNER_LEVEL_ID,
            403
        );
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'user_ids'         => ['required', 'array', 'min:1'],
            'user_ids.*'       => ['integer', 'exists:users,id'],
            'action'           => ['required', Rule::in([
                'promote', 'demote', 'add_position', 'remove_position',
            ])],
            'target_role'      => ['nullable', 'string', Rule::in(array_keys(self::ROLE_TO_LEVEL_NAME))],
            'jabatan_ids'      => ['nullable', 'array'],
            'jabatan_ids.*'    => ['integer', 'exists:jabatans,id'],
            'pin_superadmin'   => ['nullable', 'string', 'max:255'],
            'new_password'     => ['nullable', 'string', 'min:4', 'max:255'],
            'new_pin'          => ['nullable', 'string', 'min:4', 'max:10'],
            'update_security'  => ['nullable', 'boolean'],
        ]);
    }

    private function resolveTargetLevelId(?string $role): ?int
    {
        if (!$role) return null;
        $levelName = self::ROLE_TO_LEVEL_NAME[$role] ?? $role;
        return Level::where('name', $levelName)->value('id');
    }

    /**
     * Terapkan perubahan. Return ['changed' => bool, 'notes' => array]
     */
    private function applyChanges(User $user, array $validated, ?int $targetLevelId, array $flags): array
    {
        $notes   = [];
        $changed = false;
        $isSuperadminBefore = (int) $user->level_id === self::SUPERADMIN_LEVEL_ID;

        /* ─────────────────────────────────────────────────────
           1. PROMOTE / DEMOTE — ganti level
           ───────────────────────────────────────────────────── */
        if (($flags['is_promote'] || $flags['is_demote']) && $targetLevelId) {
            if ((int) $user->level_id !== (int) $targetLevelId) {
                $user->level_id = $targetLevelId;
                $changed = true;
                $notes[] = "level → {$targetLevelId}";

                // Turun dari Superadmin → reset PIN
                if ($isSuperadminBefore && $targetLevelId !== self::SUPERADMIN_LEVEL_ID) {
                    $user->pin = null;
                    $notes[] = 'PIN direset';
                }
            }
        }

        /* ─────────────────────────────────────────────────────
           2. TAMBAH JABATAN
           ───────────────────────────────────────────────────── */
        $jabatanIds = $validated['jabatan_ids'] ?? [];

        if ($flags['is_add_position'] && !empty($jabatanIds)) {
            $existingIds = $user->jabatans->pluck('id')->all();
            $newIds      = array_diff($jabatanIds, $existingIds);

            foreach ($newIds as $jabatanId) {
                $isPrimary = $user->jabatans->isEmpty() && empty($newIds === [$jabatanId]);

                $user->jabatans()->attach($jabatanId, [
                    'is_primary'  => $isPrimary,
                    'status'      => 'active',
                    'start_date'  => now()->toDateString(),
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'created_by'  => auth()->id(),
                ]);
                $changed = true;
            }

            if ($changed) {
                $notes[] = count($newIds) . ' jabatan ditambah';
                $user->load('jabatans');
            }
        }

        /* ─────────────────────────────────────────────────────
           3. KURANGI JABATAN
           ───────────────────────────────────────────────────── */
        if ($flags['is_remove_position'] && !empty($jabatanIds)) {
            $detached = $user->jabatans()->detach($jabatanIds);

            if ($detached > 0) {
                $changed = true;
                $notes[] = "{$detached} jabatan dihapus";

                if ($isSuperadminBefore) {
                    $user->pin = null;
                    $notes[] = 'PIN direset';
                }

                $user->load('jabatans');
            }
        }

        /* ─────────────────────────────────────────────────────
           4. UPDATE SECURITY (manual)
           ───────────────────────────────────────────────────── */
        if (!empty($validated['update_security'])) {
            $newPass = $validated['new_password'] ?? null;
            $newPin  = $validated['new_pin'] ?? null;

            if (!empty($newPass)) {
                $user->password = Hash::make($newPass);
                $changed = true;
                $notes[] = 'password baru';
            }
            if (!empty($newPin)) {
                $user->pin = Hash::make($newPin);
                $changed = true;
                $notes[] = 'PIN baru';
            }
        }

        /* ─────────────────────────────────────────────────────
           5. PIN WAJIB untuk Superadmin baru
           ───────────────────────────────────────────────────── */
        if ($flags['is_promote'] && $flags['is_superadmin_target']) {
            $newPin = $validated['new_pin'] ?? null;
            if (!empty($newPin)) {
                $user->pin = Hash::make($newPin);
                $changed = true;
                $notes[] = 'PIN superadmin';
            }
        }

        if ($changed) {
            $user->save();
        }

        return ['changed' => $changed, 'notes' => $notes];
    }

    private function fail(string $message, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}