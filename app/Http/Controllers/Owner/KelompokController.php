<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\GroupAnnouncement;

class KelompokController extends Controller
{
    // Halaman Semua Kelompok
    public function index()
    {
        $user = Auth::user();
        if (in_array($user->level_id, [1, 2])) {
            $groups = Group::with('members.user')->get();
        } else {
            $groups = Group::whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'aktif');
            })->with('members.user')->get();
        }
        return view('owner.kelompok.index', compact('groups'));
    }

    public function custom()
    {
        return view('owner.kelompok.custom');
    }

    public function akses(Request $request)
{
    $user = Auth::user();

    // Ambil daftar kelompok
    if (in_array($user->level_id, [1, 2])) {
        $groups = Group::all();
    } else {
        $groups = Group::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'aktif');
        })->get();
    }

    $selectedGroupId = $request->group_id ?? ($groups->first()?->id ?? null);
    $selectedGroup = $selectedGroupId ? Group::with('members')->find($selectedGroupId) : null;

    // 🔥 PERBAIKAN: Hanya tampilkan level 1-5 (Owner, Superadmin, Ketua, Pengurus, Anggota UKM)
    // KECUALIKAN user dengan nama "Anggota Umum" (jika ada)
    $users = User::whereIn('level_id', [1, 2, 3, 4, 5])
        ->where('name', '!=', 'Anggota Umum') // 🔥 Tambahkan filter ini
        ->with('level')
        ->get();

    // Tandai status keanggotaan dan permission
    if ($selectedGroup) {
        $memberMap = [];
        foreach ($selectedGroup->members as $member) {
            $memberMap[$member->user_id] = $member;
        }
        foreach ($users as $u) {
            if (isset($memberMap[$u->id])) {
                $u->is_member = true;
                $u->member = $memberMap[$u->id];
                $u->can_view = $memberMap[$u->id]->can_view ?? true;
                $u->can_announcement = $memberMap[$u->id]->can_announcement ?? false;
                $u->can_message = $memberMap[$u->id]->can_message ?? true;
                $u->can_manage_members = $memberMap[$u->id]->can_manage_members ?? false;
                $u->position = $memberMap[$u->id]->position;
            } else {
                $u->is_member = false;
                $u->member = null;
                $u->can_view = false;
                $u->can_announcement = false;
                $u->can_message = false;
                $u->can_manage_members = false;
                $u->position = null;
            }
        }
    }

    return view('owner.kelompok.akses', compact('groups', 'selectedGroup', 'selectedGroupId', 'users'));
}

   public function anggota(Request $request, $groupId = null)
{
    $user = Auth::user();

    if (in_array($user->level_id, [1, 2])) {
        $groups = Group::all();
    } else {
        $groups = Group::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'aktif');
        })->get();
    }

    // Jika ada groupId dari parameter, gunakan; jika tidak dari request
    $selectedGroupId = $groupId ?? $request->group_id ?? ($groups->first()?->id ?? null);
    $selectedGroup = $selectedGroupId ? Group::with(['members' => function($q) {
        $q->whereHas('user', function($uq) {
            $uq->whereIn('level_id', [3, 4, 5]);
        });
    }, 'members.user'])->find($selectedGroupId) : null;

    $members = $selectedGroup ? $selectedGroup->members : collect();
    $allGroups = Group::all();

    return view('owner.kelompok.anggota', compact('groups', 'selectedGroup', 'selectedGroupId', 'members', 'allGroups'));
}

    public function pengumuman(Request $request)
{
    $user = Auth::user();

    if (in_array($user->level_id, [1, 2])) {
        $groups = Group::all();
    } else {
        $groups = Group::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'aktif');
        })->get();
    }

    $selectedGroupId = $request->group_id ?? ($groups->first()?->id ?? null);
    $selectedGroup = $selectedGroupId ? Group::find($selectedGroupId) : null;

    if ($selectedGroup) {
        $announcements = GroupAnnouncement::whereHas('groups', function($q) use ($selectedGroupId) {
            $q->where('group_id', $selectedGroupId);
        })
        ->with('creator')
        ->orderBy('created_at', 'desc')
        ->get();
    } else {
        $announcements = collect();
    }

    return view('owner.kelompok.pengumuman', compact('groups', 'selectedGroup', 'selectedGroupId', 'announcements'));
}
public function storeAnnouncement(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'group_ids' => 'required|array|min:1',
        'group_ids.*' => 'exists:groups,id',
        'is_important' => 'nullable|boolean',
    ]);

    $announcement = GroupAnnouncement::create([
        'title' => $request->title,
        'content' => $request->content,
        'created_by' => Auth::id(),
        'is_important' => $request->boolean('is_important', false),
        'status' => 'published',
        'published_at' => now(),
    ]);

    // Attach ke kelompok target
    $announcement->groups()->attach($request->group_ids);

    return redirect()->back()->with('success', 'Pengumuman berhasil dibuat.');
}
    public function pesan()
    {
        return view('owner.kelompok.pesan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $group = Group::create([
            'code' => 'KLP-' . strtoupper(Str::random(6)),
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        // 🔥 Hanya tambahkan pembuat sebagai ketua jika level_id 3,4,5 (bukan Owner/Superadmin)
        $user = Auth::user();
        if (in_array($user->level_id, [3, 4, 5])) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'position' => 'ketua',
                'status' => 'aktif',
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('owner.kelompok.index')->with('success', 'Kelompok berhasil dibuat.');
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('owner.kelompok.index')->with('success', 'Kelompok berhasil diupdate.');
    }

    public function destroy(Group $group)
    {
        $group->members()->delete();
        $group->delete();
        return redirect()->route('owner.kelompok.index')->with('success', 'Kelompok dihapus.');
    }

    public function addMember(Request $request, Group $group)
{
    // ... validasi ...

    $hasKetua = $group->members()->where('position', 'ketua')->exists();
    $position = $hasKetua ? ($request->position ?? 'anggota') : 'ketua';

    // Set permission berdasarkan posisi
    $can_view = true;
    $can_announcement = ($position == 'ketua') ? true : false;
    $can_message = ($position == 'ketua' || $position == 'pengurus') ? true : false;
    $can_manage_members = ($position == 'ketua') ? true : false;

    GroupMember::create([
        'group_id' => $group->id,
        'user_id' => $user->id,
        'position' => $position,
        'status' => 'aktif',
        'joined_at' => now(),
        'can_view' => $can_view,
        'can_announcement' => $can_announcement,
        'can_message' => $can_message,
        'can_manage_members' => $can_manage_members,
    ]);

    return redirect()->back()->with('success', 'Anggota berhasil ditambahkan' . ($position == 'ketua' ? ' sebagai Ketua.' : '.'));
}

    // Update role anggota
    public function updateMember(Request $request, Group $group, $memberId)
    {
        $request->validate([
            'position' => 'required|in:ketua,pengurus,anggota',
        ]);

        $member = GroupMember::where('group_id', $group->id)->findOrFail($memberId);

        // Cegah jika hanya ada satu ketua dan mau diubah ke role lain
        if ($member->position == 'ketua' && $request->position != 'ketua') {
            $ketuaCount = $group->members()->where('position', 'ketua')->count();
            if ($ketuaCount <= 1) {
                return redirect()->back()->with('error', 'Tidak dapat mengubah posisi ketua terakhir.');
            }
        }

        $member->position = $request->position;
        $member->save();

        return redirect()->back()->with('success', 'Role anggota berhasil diupdate.');
    }

    // Pindahkan anggota ke kelompok lain
    public function moveMember(Request $request, Group $group, $memberId)
    {
        $request->validate([
            'target_group_id' => 'required|exists:groups,id|different:group_id',
        ]);

        $member = GroupMember::where('group_id', $group->id)->findOrFail($memberId);
        $targetGroup = Group::find($request->target_group_id);

        // Cek apakah user sudah menjadi anggota di target group
        $exists = GroupMember::where('group_id', $targetGroup->id)
            ->where('user_id', $member->user_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'User sudah menjadi anggota di kelompok target.');
        }

        $newMember = $member->replicate();
        $newMember->group_id = $targetGroup->id;
        $newMember->save();

        $member->delete();

        return redirect()->back()->with('success', 'Anggota berhasil dipindahkan ke kelompok ' . $targetGroup->name);
    }

    public function removeMember(Group $group, $userId)
{
    $member = GroupMember::where('group_id', $group->id)
        ->where('user_id', $userId)
        ->first();

    if (!$member) {
        return redirect()->back()->with('error', 'Anggota tidak ditemukan.');
    }

    $user = Auth::user();

    // 🔒 Cek izin: hanya owner (level 1) atau user dengan permission can_manage_members yang boleh mengeluarkan
    $hasPermission = false;
    if ($user->level_id == 1) {
        $hasPermission = true; // Owner selalu punya izin
    } else {
        // Cek apakah user yang login memiliki permission can_manage_members di kelompok ini
        $userMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();
        if ($userMember && $userMember->can_manage_members) {
            $hasPermission = true;
        }
    }

    if (!$hasPermission) {
        return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengeluarkan anggota.');
    }

    // 🔒 Cegah mengeluarkan Owner (level 1) dan Superadmin (level 2) jika mereka menjadi anggota (seharusnya tidak)
    $targetUser = User::find($userId);
    if (in_array($targetUser->level_id, [1, 2])) {
        return redirect()->back()->with('error', 'Owner dan Superadmin tidak dapat dikeluarkan dari kelompok.');
    }

    // Cegah menghapus ketua jika hanya satu
    if ($member->position == 'ketua' && $group->members()->where('position', 'ketua')->count() <= 1) {
        return redirect()->back()->with('error', 'Tidak dapat menghapus ketua terakhir.');
    }

    $member->delete();

    return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan.');
}

    public function updatePermissions(Request $request, Group $group)
{
    $request->validate([
        'permissions' => 'required|array',
        'permissions.*.user_id' => 'required|exists:users,id',
        'permissions.*.can_view' => 'boolean',
        'permissions.*.can_announcement' => 'boolean',
        'permissions.*.can_message' => 'boolean',
        'permissions.*.can_manage_members' => 'boolean',
    ]);

    foreach ($request->permissions as $perm) {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', $perm['user_id'])
            ->first();

        if ($member) {
            $member->update([
                'can_view' => $perm['can_view'] ?? false,
                'can_announcement' => $perm['can_announcement'] ?? false,
                'can_message' => $perm['can_message'] ?? false,
                'can_manage_members' => $perm['can_manage_members'] ?? false,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Hak akses berhasil diperbarui.');
}

    // Halaman Invite
    public function invite(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->level_id, [1, 2])) {
            $groups = Group::all();
        } else {
            $groups = Group::whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'aktif');
            })->get();
        }

        $selectedGroupId = $request->group_id ?? ($groups->first()?->id ?? null);
        $selectedGroup = $selectedGroupId ? Group::with('members')->find($selectedGroupId) : null;

        // Hanya tampilkan user dengan level 3,4,5
        $users = User::whereIn('level_id', [3, 4, 5])
            ->with('level')
            ->get();

        if ($selectedGroup) {
            $memberIds = $selectedGroup->members->pluck('user_id')->toArray();
            foreach ($users as $u) {
                $u->is_member = in_array($u->id, $memberIds);
                $u->member_position = $selectedGroup->members->firstWhere('user_id', $u->id)?->position ?? null;
            }
        } else {
            foreach ($users as $u) {
                $u->is_member = false;
                $u->member_position = null;
            }
        }

        return view('owner.kelompok.invite', compact('groups', 'selectedGroup', 'selectedGroupId', 'users'));
    }
    public function membersCount(Group $group)
{
    $count = $group->members()->whereHas('user', function($q) {
        $q->whereIn('level_id', [3, 4, 5]); // hanya yang valid
    })->count();
    return response()->json(['count' => $count]);
}
}