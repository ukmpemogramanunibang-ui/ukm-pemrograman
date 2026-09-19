<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DivisiController extends Controller
{
    // Semua Divisi
    public function index()
    {
        $divisions = Division::with(['creator', 'members.user'])->get();
        return view('owner.divisi.index', compact('divisions'));
    }

    public function custom(Request $request)
{
    $division = null;
    if ($request->has('id')) {
        $division = Division::findOrFail($request->id);
    }
    $users = User::whereIn('level_id', [3,4,5])
        ->orderBy('name')
        ->get();

    // Siapkan data user untuk JavaScript (format array JSON-friendly)
    $userOptions = $users->map(function ($u) {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'level' => $u->level->name ?? '',
            'nim' => $u->nim ?? '',
        ];
    })->values();

    return view('owner.divisi.custom', compact('division', 'users', 'userOptions'));
}

    // Simpan divisi baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ketua_id' => 'required|exists:users,id',
            'pengurus_ids' => 'nullable|array',
            'pengurus_ids.*' => 'exists:users,id',
            'status' => 'required|in:aktif,nonaktif',
            'max_members' => 'required|integer|min:1',
            'ketua_label' => 'nullable|string|max:50',
            'pengurus_jabatan' => 'nullable|array',
            'pengurus_jabatan.*' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi role ketua
        $ketua = User::find($request->ketua_id);
        if (!$ketua || !in_array($ketua->level_id, [3,4,5])) {
            return redirect()->back()->withErrors(['ketua_id' => 'Ketua harus berasal dari role Ketua, Pengurus, atau Anggota UKM.'])->withInput();
        }

        $pengurusIds = $request->pengurus_ids ?? [];

        // Validasi role pengurus
        if (!empty($pengurusIds)) {
            $invalidPengurus = User::whereIn('id', $pengurusIds)
                ->whereNotIn('level_id', [3,4,5])
                ->exists();
            if ($invalidPengurus) {
                return redirect()->back()->withErrors(['pengurus_ids' => 'Pengurus hanya boleh dari role Ketua, Pengurus, atau Anggota UKM.'])->withInput();
            }

            // Cek duplikat
            if (count($pengurusIds) !== count(array_unique($pengurusIds))) {
                return redirect()->back()->withErrors(['pengurus_ids' => 'Tidak boleh ada pengurus yang sama (duplikat).'])->withInput();
            }

            // Cek ketua tidak boleh jadi pengurus
            if (in_array($request->ketua_id, $pengurusIds)) {
                return redirect()->back()->withErrors(['pengurus_ids' => 'Ketua tidak boleh dipilih sebagai pengurus.'])->withInput();
            }
        }

        // Hitung total anggota
        $totalMembers = 1 + count($pengurusIds);
        if ($totalMembers > $request->max_members) {
            return redirect()->back()->withErrors(['max_members' => 'Jumlah anggota (ketua + pengurus) melebihi batas maksimal yang diizinkan.'])->withInput();
        }

        // Buat divisi
        $division = Division::create([
            'code' => 'DIV-' . strtoupper(Str::random(8)),
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'max_members' => $request->max_members,
            'ketua_label' => $request->ketua_label ?? 'Ketua',
            'created_by' => auth()->id(),
        ]);

        // Tambah ketua
        DivisionMember::create([
            'division_id' => $division->id,
            'user_id' => $request->ketua_id,
            'position' => 'ketua',
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        // Tambah pengurus
        $jabatans = $request->pengurus_jabatan ?? [];
        foreach ($pengurusIds as $index => $pengurusId) {
            $jabatan = $jabatans[$index] ?? null;
            DivisionMember::create([
                'division_id' => $division->id,
                'user_id' => $pengurusId,
                'position' => 'pengurus',
                'custom_position' => $jabatan,
                'status' => 'aktif',
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('owner.divisi.index')->with('success', 'Divisi berhasil dibuat.');
    }

    public function update(Request $request, Division $division)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
            'max_members' => 'required|integer|min:1',
            'ketua_label' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek max_members tidak boleh kurang dari jumlah anggota saat ini
        $currentMembersCount = $division->members()->count();
        if ($request->max_members < $currentMembersCount) {
            return redirect()->back()->withErrors(['max_members' => 'Maksimal anggota tidak boleh kurang dari jumlah anggota saat ini ('.$currentMembersCount.').'])->withInput();
        }

        $division->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'max_members' => $request->max_members,
            'ketua_label' => $request->ketua_label ?? 'Ketua',
        ]);

        return redirect()->route('owner.divisi.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    // Hapus divisi
    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('owner.divisi.index')->with('success', 'Divisi berhasil dihapus.');
    }

    // Halaman anggota divisi
    public function anggota(Request $request)
    {
        $divisions = Division::orderBy('name')->get();
        $selectedDivisionId = $request->input('division_id', $divisions->first()->id ?? null);
        $selectedDivision = null;
        $members = collect();

        if ($selectedDivisionId) {
            $selectedDivision = Division::with('members.user')->find($selectedDivisionId);
            if ($selectedDivision) {
                $members = $selectedDivision->members;
            }
        }

        $allDivisions = Division::orderBy('name')->get();

        return view('owner.divisi.anggota', compact(
            'divisions',
            'selectedDivisionId',
            'selectedDivision',
            'members',
            'allDivisions'
        ));
    }

    // Halaman hak akses
    public function akses(Request $request)
    {
        $divisions = Division::orderBy('name')->get();
        $selectedDivisionId = $request->input('division_id', $divisions->first()->id ?? null);
        $selectedDivision = null;
        $members = collect();

        if ($selectedDivisionId) {
            $selectedDivision = Division::with('members.user')->find($selectedDivisionId);
            if ($selectedDivision) {
                $members = $selectedDivision->members;
            }
        }

        return view('owner.divisi.akses', compact('divisions', 'selectedDivisionId', 'selectedDivision', 'members'));
    }

    // Update permissions (checkbox)
    public function updatePermissions(Request $request, Division $division)
    {
        $permissions = $request->input('permissions', []);
        foreach ($permissions as $memberId => $perms) {
            $member = DivisionMember::find($memberId);
            if ($member && $member->division_id == $division->id) {
                $member->can_view = isset($perms['view']) ? 1 : 0;
                $member->can_announcement = isset($perms['announcement']) ? 1 : 0;
                $member->can_message = isset($perms['message']) ? 1 : 0;
                $member->can_view_members = isset($perms['view_members']) ? 1 : 0;
                $member->can_manage_members = isset($perms['manage_members']) ? 1 : 0;
                $member->can_manage_content = isset($perms['manage_content']) ? 1 : 0;
                $member->save();
            }
        }

        return redirect()->back()->with('success', 'Hak akses berhasil diperbarui.');
    }

    // Halaman pengumuman divisi
    public function pengumuman()
    {
        $divisions = Division::orderBy('name')->get();
        return view('owner.divisi.pengumuman', compact('divisions'));
    }

    // Simpan pengumuman
    public function storeAnnouncement(Request $request)
    {
        // Logika simpan pengumuman (mirip dengan kelompok)
        // ...
        return redirect()->back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    // Halaman pesan divisi
    public function pesan()
    {
        $divisions = Division::orderBy('name')->get();
        return view('owner.divisi.pesan', compact('divisions'));
    }

    public function invite(Request $request)
{
    $divisionId = $request->input('division_id');
    $division = null;
    $users = collect();

    $divisions = Division::orderBy('name')->get();

    if ($divisionId) {
        $division = Division::find($divisionId);

        // ✅ HANYA user dengan level_id 3,4,5
        $users = User::whereIn('level_id', [3,4,5])
            ->with(['level', 'divisionMembers' => function ($q) {
                $q->with('division');
            }])
            ->orderBy('name')
            ->get();

        // Tambahkan status keanggotaan
        $users = $users->map(function ($user) use ($divisionId) {
            $membership = $user->divisionMembers->first(function ($dm) use ($divisionId) {
                return $dm->division_id == $divisionId;
            });
            $user->is_member = !is_null($membership);
            $user->member_position = $membership ? $membership->position : null;
            $user->member_status = $membership ? $membership->status : null;

            $user->other_divisions = $user->divisionMembers
                ->where('division_id', '!=', $divisionId)
                ->map(function ($dm) {
                    return $dm->division->name . ' (' . $dm->position . ')';
                })
                ->implode(', ');

            return $user;
        });
    }

    return view('owner.divisi.invite', compact('division', 'users', 'divisions', 'divisionId'));
}

   public function addMember(Request $request, Division $division)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'position' => 'required|in:ketua,pengurus,anggota',
        'status' => 'nullable|in:aktif,pending',
    ]);

    // ✅ Cegah role terlarang
    $user = User::find($request->user_id);
    if (!$user || !in_array($user->level_id, [3,4,5])) {
        return redirect()->back()->withErrors(['user_id' => 'User tidak valid. Hanya Ketua, Pengurus, atau Anggota UKM yang dapat ditambahkan.']);
    }

    // Cek apakah user sudah menjadi anggota
    if ($division->members()->where('user_id', $request->user_id)->exists()) {
        return redirect()->back()->with('error', 'User sudah menjadi anggota divisi ini.');
    }

    // Cek kapasitas
    $max = $division->max_members;
    if ($max && $division->members()->count() >= $max) {
        return redirect()->back()->with('error', 'Divisi ini sudah mencapai batas maksimal anggota.');
    }

    DivisionMember::create([
        'division_id' => $division->id,
        'user_id' => $request->user_id,
        'position' => $request->position,
        'status' => $request->status ?? 'aktif',
        'joined_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
}

    // Keluarkan anggota
    public function removeMember(Division $division, $userId)
    {
        $member = DivisionMember::where('division_id', $division->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Jangan izinkan menghapus ketua jika hanya satu-satunya
        if ($member->position == 'ketua' && $division->members()->where('position', 'ketua')->count() <= 1) {
            return redirect()->back()->with('error', 'Tidak dapat mengeluarkan ketua terakhir.');
        }

        $member->delete();
        return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan.');
    }

    // Update role anggota
    public function updateMember(Request $request, Division $division, $memberId)
    {
        $member = DivisionMember::where('division_id', $division->id)
            ->findOrFail($memberId);

        $request->validate([
            'position' => 'required|in:ketua,pengurus,anggota',
        ]);

        // Jika ingin menjadikan ketua, pastikan tidak ada ketua lain
        if ($request->position == 'ketua') {
            $existingKetua = $division->members()->where('position', 'ketua')->first();
            if ($existingKetua && $existingKetua->id != $member->id) {
                // Turunkan ketua lama menjadi anggota
                $existingKetua->position = 'anggota';
                $existingKetua->save();
            }
        }

        $member->position = $request->position;
        $member->save();

        return redirect()->back()->with('success', 'Peran anggota berhasil diperbarui.');
    }

    // Pindahkan anggota ke divisi lain
    public function moveMember(Request $request, Division $division, $memberId)
    {
        $member = DivisionMember::where('division_id', $division->id)
            ->findOrFail($memberId);

        $request->validate([
            'target_division_id' => 'required|exists:divisions,id|different:' . $division->id,
        ]);

        $targetDivision = Division::find($request->target_division_id);

        // Cek apakah sudah ada di target
        if ($targetDivision->members()->where('user_id', $member->user_id)->exists()) {
            return redirect()->back()->with('error', 'User sudah menjadi anggota divisi target.');
        }

        // Pindahkan dengan membuat entri baru di target
        DivisionMember::create([
            'division_id' => $targetDivision->id,
            'user_id' => $member->user_id,
            'position' => $member->position,
            'status' => $member->status,
            'joined_at' => now(),
        ]);

        // Hapus dari divisi asal
        $member->delete();

        return redirect()->back()->with('success', 'Anggota berhasil dipindahkan.');
    }

    // API: jumlah anggota divisi (untuk real-time)
    public function membersCount(Division $division)
    {
        return response()->json(['count' => $division->members()->count()]);
    }
}