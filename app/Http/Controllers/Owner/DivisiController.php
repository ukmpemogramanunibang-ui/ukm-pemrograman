<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DivisiController extends Controller
{
    /* =========================================================
     | INDEX — Semua Divisi
     * ========================================================= */
    public function index()
    {
        $divisions = Division::query()
            ->with([
                'creator:id,name',
                'members' => fn ($q) => $q->with('user:id,name,avatar_url,level_id'),
                'members.user.level:id,name',
            ])
            ->withCount('members')
            ->orderBy('name')
            ->get();

        return view('owner.divisi.index', compact('divisions'));
    }

    /* =========================================================
     | CUSTOM — Form buat/edit divisi
     * ========================================================= */
    public function custom(Request $request)
    {
        $division = null;

        if ($request->filled('id')) {
            $division = Division::with([
                'creator:id,name',
                'members.user:id,name,nim,level_id',
                'members.user.level:id,name',
            ])->findOrFail($request->id);
        }

        $users = User::query()
            ->whereIn('level_id', [3, 4, 5])
            ->with('level:id,name')       // ✅ EAGER LOAD
            ->orderBy('name')
            ->get();

        // JSON-friendly untuk Alpine/JS
        $userOptions = $users->map(fn ($u) => [
            'id'    => $u->id,
            'name'  => $u->name,
            'level' => $u->level->name ?? '',
            'nim'   => $u->nim ?? '',
        ])->values();

        return view('owner.divisi.custom', compact('division', 'users', 'userOptions'));
    }

    /* =========================================================
     | STORE — Buat divisi baru
     * ========================================================= */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255|unique:divisions,name',
            'description'           => 'nullable|string|max:1000',
            'ketua_id'              => 'required|exists:users,id',
            'pengurus_ids'          => 'nullable|array|max:20',
            'pengurus_ids.*'        => 'exists:users,id',
            'status'                => 'required|in:aktif,nonaktif',
            'max_members'           => 'required|integer|min:1|max:999',
            'ketua_label'           => 'nullable|string|max:50',
            'pengurus_jabatan'      => 'nullable|array',
            'pengurus_jabatan.*'    => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $ketua = User::find($request->ketua_id);
        if (! $ketua || ! in_array($ketua->level_id, [3, 4, 5], true)) {
            return back()->withErrors([
                'ketua_id' => 'Ketua harus berasal dari level Ketua, Pengurus, atau Anggota UKM.',
            ])->withInput();
        }

        $pengurusIds = $request->input('pengurus_ids', []);

        if (! empty($pengurusIds)) {
            $invalid = User::whereIn('id', $pengurusIds)
                ->whereNotIn('level_id', [3, 4, 5])
                ->exists();

            if ($invalid) {
                return back()->withErrors([
                    'pengurus_ids' => 'Pengurus hanya boleh dari level Ketua, Pengurus, atau Anggota UKM.',
                ])->withInput();
            }

            if (count($pengurusIds) !== count(array_unique($pengurusIds))) {
                return back()->withErrors([
                    'pengurus_ids' => 'Tidak boleh ada pengurus duplikat.',
                ])->withInput();
            }

            if (in_array((int) $request->ketua_id, array_map('intval', $pengurusIds), true)) {
                return back()->withErrors([
                    'pengurus_ids' => 'Ketua tidak boleh dipilih sebagai pengurus.',
                ])->withInput();
            }
        }

        $total = 1 + count($pengurusIds);
        if ($total > (int) $request->max_members) {
            return back()->withErrors([
                'max_members' => "Jumlah anggota (ketua + pengurus = {$total}) melebihi batas maksimal.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $pengurusIds) {
            $division = Division::create([
                'code'         => $this->generateCode(),
                'name'         => $request->name,
                'description'  => $request->description,
                'status'       => $request->status,
                'max_members'  => $request->max_members,
                'ketua_label'  => $request->ketua_label ?? 'Ketua',
                'created_by'   => auth()->id(),
            ]);

            // Ketua
            DivisionMember::create([
                'division_id' => $division->id,
                'user_id'     => $request->ketua_id,
                'position'    => 'ketua',
                'status'      => 'aktif',
                'joined_at'   => now(),
            ]);

            // Pengurus
            $jabatans = $request->input('pengurus_jabatan', []);
            foreach ($pengurusIds as $i => $uid) {
                DivisionMember::create([
                    'division_id'     => $division->id,
                    'user_id'         => $uid,
                    'position'        => 'pengurus',
                    'custom_position' => $jabatans[$i] ?? null,
                    'status'          => 'aktif',
                    'joined_at'       => now(),
                ]);
            }
        });

        return redirect()
            ->route('owner.divisi.index')
            ->with('success', 'Divisi berhasil dibuat.');
    }

    /* =========================================================
     | UPDATE
     * ========================================================= */
    public function update(Request $request, Division $division)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:divisions,name,' . $division->id],
            'description'  => ['nullable', 'string', 'max:1000'],
            'status'       => ['required', 'in:aktif,nonaktif'],
            'max_members'  => ['required', 'integer', 'min:1', 'max:999'],
            'ketua_label'  => ['nullable', 'string', 'max:50'],
        ]);

        $currentCount = $division->members()->count();
        if ((int) $data['max_members'] < $currentCount) {
            return back()->withErrors([
                'max_members' => "Maksimal anggota tidak boleh kurang dari jumlah anggota saat ini ({$currentCount}).",
            ])->withInput();
        }

        $division->update([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'status'       => $data['status'],
            'max_members'  => $data['max_members'],
            'ketua_label'  => $data['ketua_label'] ?? 'Ketua',
        ]);

        return redirect()
            ->route('owner.divisi.index')
            ->with('success', 'Divisi berhasil diperbarui.');
    }

    /* =========================================================
     | DESTROY
     * ========================================================= */
    public function destroy(Division $division)
    {
        DB::transaction(function () use ($division) {
            $division->members()->delete();
            $division->delete();
        });

        return redirect()
            ->route('owner.divisi.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }

    /* =========================================================
     | ANGGOTA — Daftar anggota divisi
     * ========================================================= */
    public function anggota(Request $request)
    {
        $divisions = Division::orderBy('name')->get(['id', 'name']);

        $selectedDivisionId = $request->input('division_id', $divisions->first()?->id);
        $selectedDivision   = null;
        $members            = collect();

        if ($selectedDivisionId) {
            $selectedDivision = Division::with([
                'members.user:id,name,nim,avatar_url,level_id,is_active',
                'members.user.level:id,name',        // ✅ EAGER LOAD
            ])->find($selectedDivisionId);

            if ($selectedDivision) {
                $members = $selectedDivision->members;
            }
        }

        $allDivisions = Division::orderBy('name')->get(['id', 'name']);

        return view('owner.divisi.anggota', compact(
            'divisions', 'selectedDivisionId', 'selectedDivision', 'members', 'allDivisions'
        ));
    }

    /* =========================================================
     | AKSES — Atur hak akses
     * ========================================================= */
    public function akses(Request $request)
    {
        $divisions = Division::orderBy('name')->get(['id', 'name']);

        $selectedDivisionId = $request->input('division_id', $divisions->first()?->id);
        $selectedDivision   = null;
        $members            = collect();

        if ($selectedDivisionId) {
            $selectedDivision = Division::with([
                'members.user:id,name,nim,avatar_url,level_id',
                'members.user.level:id,name',        // ✅ EAGER LOAD
            ])->find($selectedDivisionId);

            if ($selectedDivision) {
                $members = $selectedDivision->members;
            }
        }

        return view('owner.divisi.akses', compact(
            'divisions', 'selectedDivisionId', 'selectedDivision', 'members'
        ));
    }

    /* =========================================================
     | UPDATE PERMISSIONS — Bulk
     * ========================================================= */
    public function updatePermissions(Request $request, Division $division)
    {
        $permissions = $request->input('permissions', []);

        if (! is_array($permissions)) {
            return back()->with('error', 'Format permissions tidak valid.');
        }

        DB::transaction(function () use ($permissions, $division) {
            $memberIds = array_map('intval', array_keys($permissions));

            $members = DivisionMember::where('division_id', $division->id)
                ->whereIn('id', $memberIds)
                ->get()
                ->keyBy('id');

            foreach ($permissions as $memberId => $perms) {
                $member = $members->get((int) $memberId);
                if (! $member) continue;

                $member->update([
                    'can_view'           => isset($perms['view']),
                    'can_announcement'   => isset($perms['announcement']),
                    'can_message'        => isset($perms['message']),
                    'can_view_members'   => isset($perms['view_members']),
                    'can_manage_members' => isset($perms['manage_members']),
                    'can_manage_content' => isset($perms['manage_content']),
                ]);
            }
        });

        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }

    /* =========================================================
     | PENGUMUMAN & PESAN — Placeholder
     * ========================================================= */
    public function pengumuman()
    {
        $divisions = Division::orderBy('name')->get(['id', 'name']);
        return view('owner.divisi.pengumuman', compact('divisions'));
    }

    public function storeAnnouncement(Request $request)
    {
        // TODO: implement (mirror dari kelompok)
        return back()->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function pesan()
    {
        $divisions = Division::orderBy('name')->get(['id', 'name']);
        return view('owner.divisi.pesan', compact('divisions'));
    }

    /* =========================================================
     | INVITE — Halaman tambah anggota
     * ========================================================= */
    public function invite(Request $request)
    {
        $divisionId = $request->input('division_id');
        $division   = null;
        $users      = collect();

        $divisions = Division::orderBy('name')->get(['id', 'name']);

        if ($divisionId) {
            $division = Division::withCount('members')->find($divisionId);

            $users = User::query()
                ->whereIn('level_id', [3, 4, 5])
                ->with([
                    'level:id,name',
                    'divisionMembers.division:id,name',
                ])
                ->orderBy('name')
                ->get();

            $users = $users->map(function ($user) use ($divisionId) {
                $membership = $user->divisionMembers->firstWhere('division_id', (int) $divisionId);

                $user->is_member       = ! is_null($membership);
                $user->member_position = $membership?->position;
                $user->member_status   = $membership?->status;

                $user->other_divisions = $user->divisionMembers
                    ->where('division_id', '!=', (int) $divisionId)
                    ->map(fn ($dm) => ($dm->division->name ?? '-') . ' (' . $dm->position . ')')
                    ->implode(', ');

                return $user;
            });
        }

        return view('owner.divisi.invite', compact(
            'division', 'users', 'divisions', 'divisionId'
        ));
    }

    /* =========================================================
     | ADD MEMBER — Direct add
     * ========================================================= */
    public function addMember(Request $request, Division $division)
    {
        $data = $request->validate([
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'position' => ['required', 'in:ketua,pengurus,anggota'],
            'status'   => ['nullable', 'in:aktif,pending'],
        ]);

        $user = User::find($data['user_id']);
        if (! $user || ! in_array($user->level_id, [3, 4, 5], true)) {
            return back()->withErrors([
                'user_id' => 'User tidak valid. Hanya level 3-5 yang dapat ditambahkan.',
            ]);
        }

        if ($division->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User sudah menjadi anggota divisi ini.');
        }

        $max = $division->max_members;
        if ($max && $division->members()->count() >= $max) {
            return back()->with('error', 'Divisi sudah mencapai batas maksimal anggota.');
        }

        DivisionMember::create([
            'division_id' => $division->id,
            'user_id'     => $user->id,
            'position'    => $data['position'],
            'status'      => $data['status'] ?? 'aktif',
            'joined_at'   => now(),
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    /* =========================================================
     | REMOVE MEMBER
     * ========================================================= */
    public function removeMember(Division $division, $userId)
    {
        $member = DivisionMember::where('division_id', $division->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($member->position === 'ketua'
            && $division->members()->where('position', 'ketua')->count() <= 1) {
            return back()->with('error', 'Tidak dapat mengeluarkan ketua terakhir.');
        }

        $member->delete();

        return back()->with('success', 'Anggota berhasil dikeluarkan.');
    }

    /* =========================================================
     | UPDATE MEMBER — Ganti posisi
     * ========================================================= */
    public function updateMember(Request $request, Division $division, $memberId)
    {
        $data = $request->validate([
            'position' => ['required', 'in:ketua,pengurus,anggota'],
        ]);

        $member = DivisionMember::where('division_id', $division->id)
            ->findOrFail($memberId);

        DB::transaction(function () use ($member, $division, $data) {
            if ($data['position'] === 'ketua') {
                $existing = $division->members()
                    ->where('position', 'ketua')
                    ->where('id', '!=', $member->id)
                    ->first();

                $existing?->update(['position' => 'anggota']);
            }

            $member->update(['position' => $data['position']]);
        });

        return back()->with('success', 'Peran anggota berhasil diperbarui.');
    }

    /* =========================================================
     | MOVE MEMBER — Pindah ke divisi lain
     * ========================================================= */
    public function moveMember(Request $request, Division $division, $memberId)
    {
        $data = $request->validate([
            'target_division_id' => ['required', 'integer', 'exists:divisions,id', 'different:' . $division->id],
        ]);

        $member        = DivisionMember::where('division_id', $division->id)->findOrFail($memberId);
        $targetDivision = Division::findOrFail($data['target_division_id']);

        if ($targetDivision->members()->where('user_id', $member->user_id)->exists()) {
            return back()->with('error', 'User sudah menjadi anggota divisi target.');
        }

        $max = $targetDivision->max_members;
        if ($max && $targetDivision->members()->count() >= $max) {
            return back()->with('error', 'Divisi target sudah penuh.');
        }

        DB::transaction(function () use ($member, $targetDivision) {
            DivisionMember::create([
                'division_id' => $targetDivision->id,
                'user_id'     => $member->user_id,
                'position'    => $member->position,
                'status'      => $member->status,
                'joined_at'   => now(),
            ]);
            $member->delete();
        });

        return back()->with('success', 'Anggota berhasil dipindahkan.');
    }

    /* =========================================================
     | MEMBERS COUNT — AJAX
     * ========================================================= */
    public function membersCount(Division $division)
    {
        return response()->json([
            'count' => $division->members()->count(),
        ]);
    }

    /* =========================================================
     | INTERNAL
     * ========================================================= */
    private function generateCode(): string
    {
        do {
            $code = 'DIV-' . strtoupper(Str::random(8));
        } while (Division::where('code', $code)->exists());

        return $code;
    }
}