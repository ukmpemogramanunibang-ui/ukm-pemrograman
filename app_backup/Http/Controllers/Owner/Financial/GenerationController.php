<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\Generation;
use App\Models\Financial\GenerationMembership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class GenerationController
 *
 * Mengelola data generasi (cohort) dan keanggotaan.
 * Dilengkapi dengan fitur target selection (pilih anggota) secara simple dan detail.
 *
 * @package App\Http\Controllers\Owner\Financial
 */
class GenerationController extends Controller
{
    /**
     * Menampilkan daftar semua generasi beserta jumlah anggota aktif.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $generations = Generation::withCount(['activeMemberships'])
            ->orderBy('entry_year', 'desc')
            ->orderBy('name')
            ->get();

        return view('owner.financial.generations.index', compact('generations'));
    }

    /**
     * Menampilkan form untuk membuat generasi baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('owner.financial.generations.create');
    }

    /**
     * Menyimpan data generasi baru ke database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:generations,name',
            'description'  => 'nullable|string|max:1000',
            'entry_year'   => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'start_date'   => 'nullable|date|before_or_equal:end_date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $generation = Generation::create($validated);

        Log::info('Generasi baru dibuat', [
            'generation_id' => $generation->id,
            'name'          => $generation->name,
            'created_by'    => auth()->id()
        ]);

        return redirect()
            ->route('owner.financial.generations.index')
            ->with('success', "Generasi \"{$generation->name}\" berhasil dibuat.")
            ->with('show_target_modal', true)
            ->with('generation_id', $generation->id);
    }

    /**
     * Menampilkan detail generasi beserta statistik dan riwayat anggota.
     *
     * @param Generation $generation
     * @return \Illuminate\View\View
     */
    public function show(Generation $generation)
    {
        $generation->load(['memberships.user', 'feePlans']);

        // Statistik keanggotaan
        $total       = $generation->activeMemberships()->count();
        $completed   = $generation->memberships()->where('status', 'completed')->count();
        $removed     = $generation->memberships()->where('status', 'removed')->count();
        $transferred = $generation->memberships()->where('status', 'transferred')->count();

        $membersByRole = $generation->getMembersByRole();

        // Riwayat 20 perubahan terakhir
        $history = $generation->memberships()
            ->with(['user', 'assigner'])
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('owner.financial.generations.show', compact(
            'generation',
            'total',
            'completed',
            'removed',
            'transferred',
            'membersByRole',
            'history'
        ));
    }

    /**
     * Menampilkan form edit generasi.
     *
     * @param Generation $generation
     * @return \Illuminate\View\View
     */
    public function edit(Generation $generation)
    {
        return view('owner.financial.generations.edit', compact('generation'));
    }

    /**
     * Memperbarui data generasi.
     *
     * @param Request $request
     * @param Generation $generation
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, Generation $generation)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255|unique:generations,name,' . $generation->id,
            'description'  => 'nullable|string|max:1000',
            'entry_year'   => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'start_date'   => 'nullable|date|before_or_equal:end_date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $generation->update($validated);

        Log::info('Generasi diperbarui', [
            'generation_id' => $generation->id,
            'updated_by'    => auth()->id()
        ]);

        return redirect()
            ->route('owner.financial.generations.index')
            ->with('success', "Generasi \"{$generation->name}\" berhasil diperbarui.");
    }

    /**
     * Menghapus generasi (soft delete) jika tidak memiliki anggota aktif atau fee plans.
     *
     * @param Generation $generation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Generation $generation)
    {
        if ($generation->activeMemberships()->count() > 0) {
            return back()->with('error', 'Generasi tidak dapat dihapus karena masih memiliki anggota aktif.');
        }

        if ($generation->feePlans()->count() > 0) {
            return back()->with('error', 'Generasi tidak dapat dihapus karena sudah digunakan dalam skema biaya.');
        }

        $generation->delete();

        Log::info('Generasi dihapus', [
            'generation_id' => $generation->id,
            'deleted_by'    => auth()->id()
        ]);

        return back()->with('success', 'Generasi berhasil dihapus.');
    }

    // ========================================================================
    // TARGET SELECTION – Fitur Memilih Anggota untuk Generasi
    // ========================================================================

    /**
     * Halaman pemilihan target (form) untuk menambahkan anggota ke generasi.
     *
     * @param Generation $generation
     * @return \Illuminate\View\View
     */
    public function targetForm(Generation $generation)
    {
        $users = User::whereIn('level_id', [3, 4, 5])
            ->orderBy('name')
            ->get(['id', 'name', 'nim', 'level_id']);

        return view('owner.financial.generations.target', compact('generation', 'users'));
    }

    /**
     * Menampilkan preview target berdasarkan pilihan role atau individu.
     * Hanya menerima metode POST.
     *
     * @param Request $request
     * @param Generation $generation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
  public function targetPreview(Request $request, Generation $generation)
{
    // Log lengkap
    Log::info('targetPreview - Request', [
        'method' => $request->method(),
        'uri'    => $request->getRequestUri(),
        'data'   => $request->all(),
        'ip'     => $request->ip(),
        'user'   => auth()->id()
    ]);

    // ❌ TOLAK akses GET (seharusnya tidak akan terjadi jika rute sudah benar,
    // tapi tetap aman)
    if (!$request->isMethod('post')) {
        return redirect()
            ->route('owner.financial.generations.target', $generation)
            ->with('error', '⚠️ Akses langsung tidak diizinkan. Silakan gunakan tombol "Preview Target" dari form.');
    }

    $mode    = $request->input('mode', 'simple');
    $roleIds = $request->input('role_ids', []);
    $userIds = $request->input('user_ids', []);

    // ✅ Validasi pilihan
    if (empty($roleIds) && empty($userIds)) {
        return redirect()
            ->route('owner.financial.generations.target', $generation)
            ->with('error', '⚠️ Silakan pilih minimal satu target (role atau individu) sebelum melanjutkan.');
    }

    // Query user berdasarkan mode
    $query = User::whereIn('level_id', [3, 4, 5]);

    if ($mode === 'simple' && !empty($roleIds)) {
        $query->whereIn('level_id', $roleIds);
    } elseif ($mode === 'detail' && !empty($userIds)) {
        $query->whereIn('id', $userIds);
    } else {
        return redirect()
            ->route('owner.financial.generations.target', $generation)
            ->with('error', 'Mode atau data tidak valid.');
    }

    $users = $query->orderBy('name')->get();

    // 🔥 Tambahkan informasi generasi secara detail
    // Tambahkan informasi generasi secara detail
$users->each(function ($user) use ($generation) {
    // Cari keanggotaan aktif di GENERASI INI
    $membership = $generation->memberships()
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->first();

    if ($membership) {
        $user->current_generation_id = $membership->generation_id;
        $user->current_generation_name = $membership->generation->name ?? 'Generasi lain';
        $user->membership_status = 'active';
    } else {
        // Cek apakah user ada di generasi lain (selain yang sedang dipilih)
        $otherMembership = \App\Models\Financial\GenerationMembership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('generation_id', '!=', $generation->id)
            ->first();

        if ($otherMembership) {
            $user->current_generation_id = $otherMembership->generation_id;
            $user->current_generation_name = $otherMembership->generation->name ?? 'Generasi lain';
            $user->membership_status = 'other';
        } else {
            $user->current_generation_id = null;
            $user->current_generation_name = null;
            $user->membership_status = 'none';
        }
    }
});

    return view('owner.financial.generations.target-preview', compact('generation', 'users', 'mode'));
}

    /**
     * Mengeksekusi tindakan terhadap anggota yang dipilih (tambah, hapus, selesaikan, pindah).
     *
     * @param Request $request
     * @param Generation $generation
     * @return \Illuminate\Http\RedirectResponse
     */
   public function targetApply(Request $request, Generation $generation)
{
    $mode = $request->input('mode', 'simple');

    // Ambil user_ids berdasarkan mode
    if ($mode === 'simple') {
        $roleIds = $request->input('role_ids', []);
        if (empty($roleIds)) {
            return back()->with('error', 'Tidak ada role yang dipilih.');
        }
        // Ambil semua user dengan level_id sesuai role yang dipilih
        $userIds = User::whereIn('level_id', $roleIds)->pluck('id')->toArray();
    } else {
        // mode detail
        $userIds = $request->input('user_ids', []);
    }

    // Jika tetap kosong, error
    if (empty($userIds)) {
        return back()->with('error', 'Tidak ada anggota yang dipilih untuk diproses.');
    }

    // Validasi aksi (sama seperti sebelumnya)
    $action = $request->input('action', 'add');
    $reason = $request->input('reason', '');

    $allowedActions = ['add', 'remove', 'complete', 'transfer'];
    if (!in_array($action, $allowedActions)) {
        return back()->with('error', 'Aksi tidak dikenal. Gunakan: add, remove, complete, transfer.');
    }

    if ($action === 'transfer') {
        $targetGenId = $request->input('target_generation_id');
        if (!$targetGenId || !Generation::where('id', $targetGenId)->exists()) {
            return back()->with('error', 'Generasi tujuan tidak ditemukan atau tidak valid.');
        }
    }

    DB::beginTransaction();

    try {
        $processed = 0;
        $errors = [];

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user) {
                $errors[] = "User ID {$userId} tidak ditemukan.";
                continue;
            }

            // Validasi eligibility
            if (!in_array($user->level_id, [3, 4, 5])) {
                $errors[] = "{$user->name} tidak eligible (level tidak sesuai).";
                continue;
            }

            // Cek apakah user sudah aktif di generasi ini
            $existing = $generation->memberships()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existing && $action === 'add') {
                $errors[] = "{$user->name} sudah berada di generasi ini.";
                continue;
            }

            if (!$existing && $action !== 'add') {
                $errors[] = "{$user->name} tidak terdaftar di generasi ini, tidak bisa di-" . $action . ".";
                continue;
            }

            // Eksekusi aksi
            $result = false;
            switch ($action) {
                case 'add':
                    $result = $generation->addUser($user, now(), $reason ?: 'Ditambahkan ke generasi');
                    break;
                case 'remove':
                    $result = $generation->removeUser($user, $reason ?: 'Dikeluarkan dari generasi');
                    break;
                case 'complete':
                    $result = $generation->completeUser($user, $reason ?: 'Masa keanggotaan selesai');
                    break;
                case 'transfer':
                    $targetGen = Generation::find($request->input('target_generation_id'));
                    $result = $generation->transferUser($user, $targetGen, $reason ?: "Dipindahkan ke {$targetGen->name}");
                    break;
            }

            if ($result) {
                $processed++;
            } else {
                $errors[] = "Gagal memproses {$user->name}.";
            }
        }

        DB::commit();

        $message = "✅ Berhasil memproses {$processed} anggota.";
        if (!empty($errors)) {
            $message .= " ⚠️ Namun ada " . count($errors) . " error: " . implode('; ', $errors);
        }

        Log::info('targetApply selesai', [
            'generation_id' => $generation->id,
            'action'        => $action,
            'processed'     => $processed,
            'errors'        => $errors,
            'by'            => auth()->id()
        ]);

        // 🔥 Redirect ke halaman index generasi (daftar semua generasi)
        return redirect()
            ->route('owner.financial.generations.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('targetApply error: ' . $e->getMessage(), [
            'generation_id' => $generation->id,
            'trace'         => $e->getTraceAsString()
        ]);

        return back()->with('error', '❌ Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}

    // ========================================================================
    // API ENDPOINTS untuk AJAX (Target Selection)
    // ========================================================================

    /**
     * Mencari user berdasarkan nama/NIM dan filter role/jabatan.
     * Digunakan oleh mode detail (AJAX).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUsers(Request $request, Generation $generation)
{
    $search  = $request->input('q', '');
    $role    = $request->input('role', '');
    $jabatan = $request->input('jabatan', '');

    $query = User::whereIn('level_id', [3, 4, 5]);

    if ($jabatan) {
        $query->whereHas('jabatans', function ($q) use ($jabatan) {
            $q->where('jabatan_id', $jabatan);
        });
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('nim', 'like', "%{$search}%");
        });
    }

    if ($role) {
        $query->where('level_id', $role);
    }

    $users = $query->limit(20)->get(['id', 'name', 'nim', 'level_id']);

    // 🔥 Tambahkan status keanggotaan untuk setiap user
    $users->each(function ($user) use ($generation) {
        $membership = $generation->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($membership) {
            $user->current_generation_id = $membership->generation_id;
            $user->current_generation_name = $membership->generation->name ?? 'Generasi lain';
            $user->membership_status = 'active';
        } else {
            $otherMembership = GenerationMembership::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('generation_id', '!=', $generation->id)
                ->first();

            if ($otherMembership) {
                $user->current_generation_id = $otherMembership->generation_id;
                $user->current_generation_name = $otherMembership->generation->name ?? 'Generasi lain';
                $user->membership_status = 'other';
            } else {
                $user->current_generation_id = null;
                $user->current_generation_name = null;
                $user->membership_status = 'none';
            }
        }
    });

    return response()->json($users);
}

    /**
     * Mengembalikan daftar role yang eligible (Ketua, Pengurus, Anggota UKM).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function eligibleRoles()
    {
        $roles = [
            ['id' => 3, 'name' => 'Ketua'],
            ['id' => 4, 'name' => 'Pengurus'],
            ['id' => 5, 'name' => 'Anggota UKM'],
        ];

        return response()->json($roles);
    }
}