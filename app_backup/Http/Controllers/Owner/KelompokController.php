<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\GroupMember;
use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\GroupAnnouncementService;

class KelompokController extends Controller
{
    /* =========================================================
     | CONSTANT — single source of truth
     * ========================================================= */

    /** Level yang dianggap "anggota kelompok" sebenarnya. */
    private const MEMBER_LEVELS = [3, 4, 5];

    /** Level yang selalu dianggap "top management" (lihat semua). */
    private const ADMIN_LEVELS  = [1, 2];

    /** Posisi yang valid dalam sebuah kelompok. */
    private const POSITIONS     = ['ketua', 'pengurus', 'anggota'];

    /** Status membership yang valid. */
    private const MEMBER_STATUS = ['aktif', 'nonaktif', 'pending'];

    /** Batas maksimum bulk add. */
    private const BULK_LIMIT    = 100;


    /* =========================================================
     | INDEX — Semua Kelompok (Command Center)
     * ========================================================= */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Group::query()
            ->with([
                'members' => fn ($q) => $this->scopeActiveMembers($q)
                    ->with(['user:id,name,avatar_url,level_id']),
                'creator:id,name',
            ])
            ->withCount([
                'members as members_count' => fn ($q) => $this->scopeActiveMembers($q),
            ]);

        // Non-admin hanya melihat kelompok yang diikutinya
        if (! $this->isAdmin($user)) {
            $query->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'aktif');
            });
        }

        $this->applySearch($query, $request->input('q'));
        $this->applyStatusFilter($query, $request->input('status'));
        $this->applyHealthFilter($query, $request->input('health'));
        $this->applySort($query, $request->input('sort', 'latest'));

        $perPage = $this->sanitizePerPage($request->input('per_page', 12));
        $groups  = $query->paginate($perPage)->withQueryString();

        $stats = $this->buildStats($user);
        $filters = $this->buildFiltersState($request);

        return view('owner.kelompok.index', compact('groups', 'stats', 'filters'));
    }


    /* =========================================================
     | MEMBERS COUNT — AJAX endpoint
     * ========================================================= */
    public function membersCounts(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = GroupMember::query()
            ->select('group_id', DB::raw('COUNT(*) as total'))
            ->where('status', 'aktif')
            ->whereHas('user', fn ($q) => $q->whereIn('level_id', self::MEMBER_LEVELS))
            ->groupBy('group_id');

        if (! $this->isAdmin($user)) {
            $query->whereIn('group_id', function ($sub) use ($user) {
                $sub->select('group_id')
                    ->from('group_members')
                    ->where('user_id', $user->id)
                    ->where('status', 'aktif');
            });
        }

        return response()->json($query->pluck('total', 'group_id')->toArray());
    }


    /* =========================================================
     | CUSTOM — Form buat kelompok
     * ========================================================= */
    public function custom()
    {
        return view('owner.kelompok.custom');
    }


    /* =========================================================
     | STORE — Buat kelompok baru
     * ========================================================= */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:aktif,nonaktif'],
            'max_members' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $group = DB::transaction(function () use ($data) {
            $group = Group::create([
                'code'        => $this->generateGroupCode(),
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'status'      => $data['status'],
                'created_by'  => Auth::id(),
                'max_members' => $data['max_members'] ?? null,
            ]);

            // Pembuat otomatis jadi Ketua (jika level 3/4/5)
            $user = Auth::user();
            if (in_array($user->level_id, self::MEMBER_LEVELS, true)) {
                $this->attachMember($group, $user, 'ketua');
            }

            return $group;
        });

        return redirect()
            ->route('owner.kelompok.index')
            ->with('success', "Kelompok \"{$group->name}\" berhasil dibuat.");
    }


    /* =========================================================
     | UPDATE — Edit kelompok
     * ========================================================= */
    public function update(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:groups,name,' . $group->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:aktif,nonaktif'],
            'max_members' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        // Cegah penurunan kapasitas di bawah jumlah anggota saat ini
        if (! empty($data['max_members'])) {
            $current = $this->countActiveMembers($group);
            if ($data['max_members'] < $current) {
                return back()
                    ->withInput()
                    ->with('error', "Kapasitas tidak boleh lebih kecil dari jumlah anggota saat ini ({$current}).");
            }
        }

        $group->update($data);

        return redirect()
            ->route('owner.kelompok.index')
            ->with('success', 'Kelompok berhasil diperbarui.');
    }


    /* =========================================================
     | DESTROY — Hapus kelompok (soft: nonaktifkan, hard: hapus)
     * ========================================================= */
    public function destroy(Group $group): RedirectResponse
    {
        // Cegah hapus kelompok yang punya anggota aktif
        if ($this->countActiveMembers($group) > 0) {
            return back()->with(
                'error',
                'Tidak dapat menghapus kelompok yang masih memiliki anggota aktif. Keluarkan anggota terlebih dahulu.'
            );
        }

        DB::transaction(function () use ($group) {
            $group->members()->delete();
            $group->delete();
        });

        return redirect()
            ->route('owner.kelompok.index')
            ->with('success', 'Kelompok berhasil dihapus.');
    }


    /* =========================================================
     | ANGGOTA — Kelola daftar anggota kelompok
     * ========================================================= */
    public function anggota(Request $request, $groupId = null)
    {
        $user = Auth::user();

        $groups = $this->accessibleGroupsQuery($user)->orderBy('name')->get();

        $selectedGroupId = $groupId ?? $request->input('group_id') ?? $groups->first()?->id;
        $selectedGroup   = $selectedGroupId ? $this->loadGroupWithMembers($selectedGroupId) : null;

        // Cache total poin per user (hindari N+1)
        if ($selectedGroup) {
            $this->attachPointCacheToMembers($selectedGroup);
        }

        $members = $selectedGroup ? $selectedGroup->members : collect();
        $stats   = $this->buildMemberStats($members);
        $allGroups = Group::orderBy('name')->get();

        return view('owner.kelompok.anggota', compact(
            'groups', 'selectedGroup', 'selectedGroupId',
            'members', 'allGroups', 'stats'
        ));
    }


    /* =========================================================
     | AKSES — Atur hak akses per anggota
     * ========================================================= */
    public function akses(Request $request)
    {
        $user = Auth::user();

        $groups = $this->accessibleGroupsQuery($user)->orderBy('name')->get();

        $selectedGroupId = $request->input('group_id') ?? $groups->first()?->id;
        $selectedGroup   = $selectedGroupId
            ? Group::with('members')->find($selectedGroupId)
            : null;

        $users = User::query()
            ->whereIn('level_id', array_merge(self::ADMIN_LEVELS, self::MEMBER_LEVELS))
            ->where('name', '!=', 'Anggota Umum')
            ->with('level')
            ->orderBy('name')
            ->get();

        if ($selectedGroup) {
            $memberMap = $selectedGroup->members->keyBy('user_id');
            $users->each(function ($u) use ($memberMap) {
                $m = $memberMap->get($u->id);
                $u->is_member          = (bool) $m;
                $u->member             = $m;
                $u->can_view           = $m?->can_view ?? false;
                $u->can_announcement   = $m?->can_announcement ?? false;
                $u->can_message        = $m?->can_message ?? false;
                $u->can_manage_members = $m?->can_manage_members ?? false;
                $u->position           = $m?->position;
            });
        }

        return view('owner.kelompok.akses', compact(
            'groups', 'selectedGroup', 'selectedGroupId', 'users'
        ));
    }


    /* =========================================================
     | INVITE — Halaman tambah anggota (Direct Add)
     * ========================================================= */
    public function invite(Request $request)
    {
        $user = Auth::user();

        $groups = $this->accessibleGroupsQuery($user)
            ->withCount([
                'members as members_count' => fn ($q) => $this->scopeActiveMembers($q),
            ])
            ->orderBy('name')
            ->get();

        $selectedGroupId = $request->input('group_id') ?? $groups->first()?->id;

        $selectedGroup = $selectedGroupId
            ? Group::with([
                'members' => fn ($q) => $this->scopeActiveMembers($q)
                    ->with('user:id,name,avatar_url,level_id'),
            ])->withCount([
                'members as members_count' => fn ($q) => $this->scopeActiveMembers($q),
            ])->find($selectedGroupId)
            : null;

        // Kandidat user: level 3/4/5, akun aktif
        $users = User::query()
            ->whereIn('level_id', self::MEMBER_LEVELS)
            ->where('is_active', true)
            ->with([
                'level:id,name',
                'activeGroups:id,name,status',
            ])
            ->orderBy('name')
            ->get();

        $uiState = $this->buildInviteUiState($selectedGroup, $users);

        return view('owner.kelompok.invite', array_merge(
            compact('groups', 'selectedGroup', 'selectedGroupId', 'users'),
            $uiState
        ));
    }


    /* =========================================================
     | ADD MEMBER — Direct Add (single)
     * ========================================================= */
    public function addMember(Request $request, Group $group)
    {
        $data = $request->validate([
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'position' => ['nullable', 'in:' . implode(',', self::POSITIONS)],
        ]);

        $targetUser = User::findOrFail($data['user_id']);

        if ($error = $this->validateMemberAddition($group, $targetUser)) {
            return $this->respond($request, $group, false, $error);
        }

        // Tentukan posisi final
        $position = $this->resolvePosition($group, $data['position'] ?? null);

        try {
            DB::transaction(function () use ($group, $targetUser, $position) {
                $this->attachMember($group, $targetUser, $position);
                $this->logGroupActivity($group, $targetUser, $position);
                $this->notifyGroupAddition($group, $targetUser, $position);
            });
        } catch (\Throwable $e) {
            return $this->respond(
                $request, $group, false,
                "Gagal menambahkan {$targetUser->name}: {$e->getMessage()}"
            );
        }

        $msg = $position === 'ketua'
            ? "{$targetUser->name} berhasil ditambahkan sebagai Ketua kelompok."
            : "{$targetUser->name} berhasil ditambahkan ke kelompok {$group->name}.";

        return $this->respond($request, $group, true, $msg);
    }


    /* =========================================================
     | ADD MEMBERS BULK — Tambah banyak anggota sekaligus
     * ========================================================= */
    public function addMembersBulk(Request $request, Group $group)
    {
        $data = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1', 'max:' . self::BULK_LIMIT],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'position'   => ['nullable', 'in:' . implode(',', self::POSITIONS)],
        ]);

        if ($group->status !== 'aktif') {
            return $this->respond(
                $request, $group, false,
                "Kelompok \"{$group->name}\" tidak dalam status aktif."
            );
        }

        $result = ['added' => 0, 'skipped' => 0, 'reasons' => []];

        DB::transaction(function () use ($data, $group, &$result) {
            // Snapshot data sekali agar tidak query berulang
            $hasKetua      = $this->groupHasKetua($group);
            $existingIds   = $this->getActiveMemberIds($group, $data['user_ids']);
            $currentCount  = $this->countActiveMembers($group);
            $maxMembers    = $this->capacityLimit($group);
            $position      = $hasKetua ? ($data['position'] ?? 'anggota') : 'ketua';

            foreach ($data['user_ids'] as $uid) {
                if (in_array($uid, $existingIds, true)) {
                    $result['skipped']++;
                    $result['reasons'][] = "User #{$uid} sudah menjadi anggota";
                    continue;
                }

                if ($maxMembers !== null && $currentCount >= $maxMembers) {
                    $result['skipped']++;
                    $result['reasons'][] = 'Kapasitas penuh';
                    continue;
                }

                $target = User::find($uid);
                if (! $target) {
                    $result['skipped']++;
                    $result['reasons'][] = "User #{$uid} tidak ditemukan";
                    continue;
                }

                if (! $target->is_active) {
                    $result['skipped']++;
                    $result['reasons'][] = "{$target->name} tidak aktif";
                    continue;
                }

                if (! in_array($target->level_id, self::MEMBER_LEVELS, true)) {
                    $result['skipped']++;
                    $result['reasons'][] = "{$target->name} levelnya tidak diizinkan";
                    continue;
                }

                $this->attachMember($group, $target, $position);

                $result['added']++;
                $currentCount++;

                // Setelah ketua pertama, sisanya jadi anggota
                if (! $hasKetua) {
                    $hasKetua = true;
                    $position = 'anggota';
                }
            }
        });

        $msg = "{$result['added']} anggota berhasil ditambahkan";
        if ($result['skipped'] > 0) {
            $msg .= " • {$result['skipped']} dilewati";
        }

        return $this->respond($request, $group, true, $msg, [
            'added'   => $result['added'],
            'skipped' => $result['skipped'],
            'reasons' => $result['reasons'],
        ]);
    }


    /* =========================================================
     | UPDATE MEMBER — Ubah posisi anggota
     * ========================================================= */
    public function updateMember(Request $request, Group $group, $memberId): RedirectResponse
    {
        $data = $request->validate([
            'position' => ['required', 'in:' . implode(',', self::POSITIONS)],
        ]);

        $member = GroupMember::where('group_id', $group->id)->findOrFail($memberId);

        // Cegah ubah ketua terakhir jadi bukan-ketua
        if ($member->position === 'ketua' && $data['position'] !== 'ketua'
            && $this->countKetua($group) <= 1) {
            return back()->with('error', 'Tidak dapat mengubah posisi ketua terakhir.');
        }

        $member->update(['position' => $data['position']]);

        return back()->with('success', 'Posisi anggota berhasil diubah.');
    }


    /* =========================================================
     | MOVE MEMBER — Pindahkan ke kelompok lain
     * ========================================================= */
    public function moveMember(Request $request, Group $group, $memberId): RedirectResponse
    {
        $data = $request->validate([
            'target_group_id' => ['required', 'integer', 'exists:groups,id', 'different:group_id'],
        ]);

        $member      = GroupMember::where('group_id', $group->id)->findOrFail($memberId);
        $targetGroup = Group::findOrFail($data['target_group_id']);

        // Cegah pindah ketua terakhir
        if ($member->position === 'ketua' && $this->countKetua($group) <= 1) {
            return back()->with('error', 'Tidak dapat memindahkan ketua terakhir.');
        }

        // Cegah duplikasi di target
        $exists = GroupMember::where('group_id', $targetGroup->id)
            ->where('user_id', $member->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User sudah menjadi anggota di kelompok target.');
        }

        // Cek kapasitas target
        if ($limit = $this->capacityLimit($targetGroup)) {
            if ($this->countActiveMembers($targetGroup) >= $limit) {
                return back()->with('error', "Kelompok \"{$targetGroup->name}\" sudah penuh.");
            }
        }

        DB::transaction(function () use ($member, $targetGroup) {
            $newMember = $member->replicate(['created_at', 'updated_at']);
            $newMember->group_id  = $targetGroup->id;
            $newMember->joined_at = now();
            $newMember->save();
            $member->delete();
        });

        return back()->with('success', "Anggota berhasil dipindahkan ke kelompok {$targetGroup->name}.");
    }


    /* =========================================================
     | REMOVE MEMBER — Keluarkan dari kelompok
     * ========================================================= */
    public function removeMember(Request $request, Group $group, $userId): RedirectResponse
    {
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        if (! $member) {
            return back()->with('error', 'Anggota tidak ditemukan.');
        }

        if (! $this->canManageMembers($group)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengeluarkan anggota.');
        }

        $targetUser = User::find($userId);
        if (! $targetUser) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        if (in_array($targetUser->level_id, self::ADMIN_LEVELS, true)) {
            return back()->with('error', 'Owner dan Superadmin tidak dapat dikeluarkan dari kelompok.');
        }

        if ($member->position === 'ketua' && $this->countKetua($group) <= 1) {
            return back()->with('error', 'Tidak dapat menghapus ketua terakhir.');
        }

        $member->delete();

        return back()->with('success', 'Anggota berhasil dikeluarkan dari kelompok.');
    }


    /* =========================================================
     | UPDATE PERMISSIONS — Bulk update hak akses anggota
     * ========================================================= */
    public function updatePermissions(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate([
            'permissions'                         => ['required', 'array'],
            'permissions.*.user_id'               => ['required', 'integer', 'exists:users,id'],
            'permissions.*.can_view'              => ['nullable', 'boolean'],
            'permissions.*.can_announcement'      => ['nullable', 'boolean'],
            'permissions.*.can_message'           => ['nullable', 'boolean'],
            'permissions.*.can_manage_members'    => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $group) {
            $members = GroupMember::where('group_id', $group->id)
                ->whereIn('user_id', array_column($data['permissions'], 'user_id'))
                ->get()
                ->keyBy('user_id');

            foreach ($data['permissions'] as $perm) {
                if ($member = $members->get($perm['user_id'])) {
                    $member->update([
                        'can_view'           => (bool) ($perm['can_view'] ?? false),
                        'can_announcement'   => (bool) ($perm['can_announcement'] ?? false),
                        'can_message'        => (bool) ($perm['can_message'] ?? false),
                        'can_manage_members' => (bool) ($perm['can_manage_members'] ?? false),
                    ]);
                }
            }
        });

        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }


    /* =========================================================
     | PENGUMUMAN — Daftar pengumuman kelompok
     * ========================================================= */
public function pengumuman(Request $request, GroupAnnouncementService $service)
{
    $user = Auth::user();

    /* Kelompok yang bisa diakses */
    $groups = $this->accessibleGroupsQuery($user)
        ->withCount([
            'members as members_count' => fn ($q) => $this->scopeActiveMembers($q),
        ])
        ->orderBy('name')
        ->get();

    /* Filter & query pengumuman */
    $q       = $request->input('q', '');
    $status  = $request->input('status', '');
    $type    = $request->input('type', '');
    $groupId = $request->input('group_id');

    $announcements = GroupAnnouncement::query()
        ->with(['creator:id,name', 'groups:id,name,code'])
        ->when($groupId, fn ($q2) => $q2->forGroup((int) $groupId))
        ->when($q, fn ($q2) => $q2->where(function ($sq) use ($q) {
            $sq->where('title', 'like', "%{$q}%")
               ->orWhere('content', 'like', "%{$q}%");
        }))
        ->when($status, fn ($q2) => $q2->where('status', $status))
        ->when($type, fn ($q2) => $q2->where('type', $type))
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->orderByDesc('created_at')
        ->paginate(15)
        ->withQueryString();

    /* Stats */
    $base = GroupAnnouncement::query();

    $stats = [
        'total'     => (clone $base)->count(),
        'published' => (clone $base)->where('status', 'published')->count(),
        'scheduled' => (clone $base)->where('status', 'scheduled')->count(),
        'draft'     => (clone $base)->where('status', 'draft')->count(),
        'expired'   => (clone $base)->where('status', 'expired')->count(),
        'urgent'    => (clone $base)->where('priority', 'urgent')->where('status', 'published')->count(),
    ];

    /* Templates */
    $templates = $this->announcementTemplates();

    return view('owner.kelompok.pengumuman', compact(
        'groups', 'announcements', 'stats', 'templates',
        'q', 'status', 'type', 'groupId'
    ));
}
protected function announcementTemplates(): array
{
    return [
        'rapat_bulanan' => [
            'label'    => 'Rapat Bulanan',
            'icon'     => 'fa-calendar-check',
            'title'    => 'Rapat Bulanan UNICODE',
            'content'  => "Halo anggota UNICODE,\n\nDiberitahukan bahwa akan dilaksanakan rapat bulanan pada:\n\nHari/Tanggal: \nWaktu: \nTempat: \nAgenda: \n\nMohon hadir tepat waktu.\n\nTerima kasih.",
            'priority' => 'important',
        ],
        'rapat_tahunan' => [
            'label'    => 'Rapat Tahunan',
            'icon'     => 'fa-calendar-star',
            'title'    => 'Rapat Tahunan UNICODE',
            'content'  => "Halo anggota UNICODE,\n\nRapat tahunan akan dilaksanakan pada:\n\nHari/Tanggal: \nWaktu: \nTempat: \nAgenda: \n\nKehadiran wajib bagi seluruh anggota.\n\nTerima kasih.",
            'priority' => 'high',
        ],
        'perubahan_jadwal' => [
            'label'    => 'Perubahan Jadwal',
            'icon'     => 'fa-clock-rotate-left',
            'title'    => 'Perubahan Jadwal Kegiatan',
            'content'  => "Diberitahukan bahwa jadwal kegiatan berikut mengalami perubahan:\n\nKegiatan: \nJadwal Lama: \nJadwal Baru: \nAlasan: \n\nMohon diperhatikan.\n\nTerima kasih.",
            'priority' => 'urgent',
        ],
        'deadline' => [
            'label'    => 'Deadline',
            'icon'     => 'fa-hourglass-end',
            'title'    => 'Batas Waktu Pengumpulan',
            'content'  => "Pengingat penting untuk seluruh anggota.\n\nDeadline: \nUntuk: \nKeterangan: \n\nMohon diselesaikan sebelum batas waktu.\n\nTerima kasih.",
            'priority' => 'high',
        ],
        'kegiatan' => [
            'label'    => 'Pengumuman Kegiatan',
            'icon'     => 'fa-flag',
            'title'    => 'Kegiatan Baru UNICODE',
            'content'  => "Halo teman-teman,\n\nAkan diadakan kegiatan:\n\nNama Kegiatan: \nTanggal: \nWaktu: \nLokasi: \nDeskripsi: \n\nTertarik untuk ikut? Daftar segera!\n\nTerima kasih.",
            'priority' => 'normal',
        ],
        'informasi' => [
            'label'    => 'Informasi Umum',
            'icon'     => 'fa-circle-info',
            'title'    => 'Informasi Penting',
            'content'  => "Halo teman-teman,\n\nBerikut informasi penting yang perlu diketahui:\n\n...\n\nTerima kasih.",
            'priority' => 'normal',
        ],
    ];
}

public function previewAnnouncement(Request $request)
{
    $data = $request->validate([
        'title'       => ['required', 'string', 'max:255'],
        'content'     => ['required', 'string'],
        'type'        => ['nullable', 'string', 'max:50'],
        'priority'    => ['nullable', 'in:normal,important,high,urgent'],
        'group_ids'   => ['required', 'array', 'min:1'],
        'group_ids.*' => ['integer', 'exists:groups,id'],
    ]);

    $groupIds = Group::whereIn('id', $data['group_ids'])
        ->where('status', 'aktif')
        ->pluck('id');

    // Hitung penerima unik (dedupe by user)
    $recipientCount = DB::table('users')
        ->whereIn('id', function ($sub) use ($groupIds) {
            $sub->select('user_id')
                ->from('group_members')
                ->whereIn('group_id', $groupIds)
                ->where('status', 'aktif');
        })
        ->whereNull('deleted_at')
        ->where('is_active', true)
        ->whereIn('level_id', [3, 4, 5])
        ->count();

    return response()->json([
        'ok'              => true,
        'groups'          => Group::whereIn('id', $groupIds)
                                 ->get(['id', 'name'])
                                 ->map(fn ($g) => ['id' => $g->id, 'name' => $g->name]),
        'recipient_count' => $recipientCount,
    ]);
}
    /* =========================================================
     | STORE ANNOUNCEMENT — Buat pengumuman baru
     * ========================================================= */
    public function storeAnnouncement(Request $request, GroupAnnouncementService $service)
{
    $data = $request->validate([
        'title'                   => ['required', 'string', 'max:255'],
        'content'                 => ['required', 'string'],
        'type'                    => ['nullable', 'string', 'max:50'],
        'priority'                => ['nullable', 'in:normal,important,high,urgent'],
        'group_ids'               => ['required', 'array', 'min:1'],
        'group_ids.*'             => ['integer', 'exists:groups,id'],
        'is_pinned'               => ['nullable', 'boolean'],
        'require_acknowledgement' => ['nullable', 'boolean'],
        'scheduled_at'            => ['nullable', 'date', 'after:now'],
        'expires_at'              => ['nullable', 'date', 'after:now'],
        'send_mode'               => ['nullable', 'in:now,schedule,draft'],
        'action_url'              => ['nullable', 'url', 'max:500'],
    ]);

    $sendMode = $data['send_mode'] ?? 'now';
    $publish  = $sendMode === 'now';

    // Kalau scheduled, jangan publish sekarang
    if ($sendMode === 'schedule') {
        if (empty($data['scheduled_at'])) {
            return back()->withInput()->with('error', 'Tanggal jadwal wajib diisi untuk mode terjadwal.');
        }
        $data['status'] = 'scheduled';
    } elseif ($sendMode === 'draft') {
        $data['status'] = 'draft';
    }

    try {
        $result = $service->create($data, $data['group_ids'], $publish);
    } catch (\Throwable $e) {
        return back()->withInput()->with('error', 'Gagal membuat pengumuman: ' . $e->getMessage());
    }

    $msg = match ($sendMode) {
        'schedule' => 'Pengumuman berhasil dijadwalkan.',
        'draft'    => 'Pengumuman berhasil disimpan sebagai draft.',
        default    => "Pengumuman berhasil dikirim ke {$result['recipient_count']} anggota.",
    };

    return redirect()
        ->route('owner.kelompok.pengumuman', ['group_id' => $data['group_ids'][0] ?? null])
        ->with('success', $msg)
        ->with('announcement_summary', [
            'recipient_count' => $result['recipient_count'],
            'group_count'     => count($data['group_ids']),
        ]);
}
public function destroyAnnouncement(GroupAnnouncement $announcement)
{
    DB::transaction(function () use ($announcement) {
        DB::table('notifications')
            ->where('data->announcement_id', $announcement->id)
            ->delete();

        $announcement->groups()->detach();
        $announcement->delete();
    });

    return back()->with('success', 'Pengumuman berhasil dihapus.');
}

    /* =========================================================
     | PESAN — Halaman pesan kelompok
     * ========================================================= */
    public function pesan()
    {
        return view('owner.kelompok.pesan');
    }


    /* =========================================================
     | MEMBERS COUNT (single) — untuk polling per kelompok
     * ========================================================= */
    public function membersCount(Group $group): JsonResponse
    {
        return response()->json([
            'count' => $this->countActiveMembers($group),
        ]);
    }


    /* =========================================================
     | PRIVATE HELPERS — Query Scopes
     * ========================================================= */

    /**
     * Scope: hanya anggota aktif dengan level 3/4/5.
     */
    private function scopeActiveMembers($query)
    {
        return $query
            ->where('status', 'aktif')
            ->whereHas('user', fn ($q) => $q->whereIn('level_id', self::MEMBER_LEVELS));
    }

    /**
     * Query kelompok yang bisa diakses user (admin → semua; user biasa → yang diikuti).
     */
    private function accessibleGroupsQuery(User $user)
    {
        $query = Group::query();

        if (! $this->isAdmin($user)) {
            $query->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'aktif');
            });
        }

        return $query;
    }

    /**
     * Load group dengan anggota (eager loading lengkap).
     */
    private function loadGroupWithMembers(int $groupId): ?Group
    {
        return Group::with([
            'members' => fn ($q) => $this->scopeActiveMembers($q)
                ->with([
                    'user:id,name,nim,avatar_url,level_id,is_active',
                ]),
            'creator:id,name',
        ])->withCount([
            'members as members_count' => fn ($q) => $this->scopeActiveMembers($q),
        ])->find($groupId);
    }

    /**
     * Terapkan filter search.
     */
    private function applySearch($query, ?string $q): void
    {
        if (! $q) return;

        $q = trim($q);
        $query->where(function ($sq) use ($q) {
            $sq->where('name', 'like', "%{$q}%")
               ->orWhere('code', 'like', "%{$q}%")
               ->orWhere('description', 'like', "%{$q}%");
        });
    }

    /**
     * Filter status kelompok.
     */
    private function applyStatusFilter($query, ?string $status): void
    {
        if ($status && in_array($status, ['aktif', 'nonaktif', 'arsip'], true)) {
            $query->where('status', $status);
        }
    }

    /**
     * Filter kesehatan kelompok (hampir penuh, penuh, kosong).
     */
    private function applyHealthFilter($query, ?string $health): void
    {
        if (! $health) return;

        // Subquery konsisten: hanya hitung level 3/4/5
        $countSubquery = '(SELECT COUNT(*) FROM group_members gm
                           JOIN users u ON u.id = gm.user_id
                           WHERE gm.group_id = groups.id
                             AND gm.status = "aktif"
                             AND u.level_id IN (3, 4, 5)
                             AND u.deleted_at IS NULL)';

        switch ($health) {
            case 'near_full':
                $query->whereNotNull('max_members')
                      ->where('max_members', '>', 0)
                      ->whereRaw("{$countSubquery} >= groups.max_members * 0.8")
                      ->whereRaw("{$countSubquery} < groups.max_members");
                break;

            case 'full':
                $query->whereNotNull('max_members')
                      ->where('max_members', '>', 0)
                      ->whereRaw("{$countSubquery} >= groups.max_members");
                break;

            case 'empty':
                $query->whereDoesntHave('members', function ($q) {
                    $q->where('status', 'aktif')
                      ->whereHas('user', fn ($uq) => $uq->whereIn('level_id', self::MEMBER_LEVELS));
                });
                break;
        }
    }

    /**
     * Terapkan sorting.
     */
    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'name'    => $query->orderBy('name'),
            'members' => $query->orderByDesc('members_count'),
            'oldest'  => $query->orderBy('created_at'),
            default   => $query->orderByDesc('created_at'),
        };
    }

    /**
     * Sanitasi jumlah per halaman.
     */
    private function sanitizePerPage($value): int
    {
        $value = (int) $value;
        return in_array($value, [12, 24, 48], true) ? $value : 12;
    }

    /**
     * Filter state untuk UI.
     */
    private function buildFiltersState(Request $request): array
    {
        return [
            'q'        => $request->input('q', ''),
            'status'   => $request->input('status', ''),
            'health'   => $request->input('health', ''),
            'sort'     => $request->input('sort', 'latest'),
            'per_page' => $this->sanitizePerPage($request->input('per_page', 12)),
        ];
    }


    /* =========================================================
     | PRIVATE HELPERS — Statistics
     * ========================================================= */

    /**
     * Build stats ringkas untuk command center.
     */
    private function buildStats(User $user): array
    {
        $base = Group::query();

        if (! $this->isAdmin($user)) {
            $base->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'aktif');
            });
        }

        $totalGroups   = (clone $base)->count();
        $activeGroups  = (clone $base)->where('status', 'aktif')->count();
        $totalCapacity = (int) (clone $base)->sum('max_members');

        // Total anggota (filter level 3/4/5)
        $totalMembers = $this->countTotalActiveMembers($user);

        // Near-full groups
        $nearFullCount = (clone $base)
            ->whereNotNull('max_members')
            ->where('max_members', '>', 0)
            ->whereRaw(
                '(SELECT COUNT(*) FROM group_members gm
                  JOIN users u ON u.id = gm.user_id
                  WHERE gm.group_id = groups.id
                    AND gm.status = "aktif"
                    AND u.level_id IN (3, 4, 5)
                    AND u.deleted_at IS NULL) >= groups.max_members * 0.8'
            )
            ->whereRaw(
                '(SELECT COUNT(*) FROM group_members gm
                  JOIN users u ON u.id = gm.user_id
                  WHERE gm.group_id = groups.id
                    AND gm.status = "aktif"
                    AND u.level_id IN (3, 4, 5)
                    AND u.deleted_at IS NULL) < groups.max_members'
            )
            ->count();

        return [
            'total_groups'   => $totalGroups,
            'active_groups'  => $activeGroups,
            'total_members'  => $totalMembers,
            'total_capacity' => $totalCapacity,
            'near_full'      => $nearFullCount,
            'health_pct'     => $totalCapacity > 0
                ? (int) round(($totalMembers / $totalCapacity) * 100)
                : null,
        ];
    }

    /**
     * Hitung total anggota aktif (level 3/4/5) di seluruh kelompok yang bisa diakses user.
     */
    private function countTotalActiveMembers(User $user): int
    {
        $query = GroupMember::query()
            ->where('status', 'aktif')
            ->whereHas('user', fn ($q) => $q->whereIn('level_id', self::MEMBER_LEVELS));

        if (! $this->isAdmin($user)) {
            $query->whereIn('group_id', function ($sub) use ($user) {
                $sub->select('group_id')
                    ->from('group_members')
                    ->where('user_id', $user->id)
                    ->where('status', 'aktif');
            });
        }

        return $query->count();
    }

    /**
     * Build statistik anggota untuk halaman anggota.
     */
    private function buildMemberStats($members): array
    {
        return [
            'total'    => $members->where('status', 'aktif')->count(),
            'ketua'    => $members->where('position', 'ketua')->where('status', 'aktif')->count(),
            'pengurus' => $members->where('position', 'pengurus')->where('status', 'aktif')->count(),
            'anggota'  => $members->where('position', 'anggota')->where('status', 'aktif')->count(),
            'nonaktif' => $members->where('status', '!=', 'aktif')->count(),
        ];
    }

    /**
     * Cache poin per user agar tidak N+1.
     */
    private function attachPointCacheToMembers(Group $group): void
    {
        $userIds = $group->members->pluck('user_id')->filter()->unique()->values();

        if ($userIds->isEmpty()) return;

        $pointsMap = PointHistory::query()
            ->selectRaw('user_id, SUM(points) as total')
            ->whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $group->members->each(function ($m) use ($pointsMap) {
            if ($m->user) {
                $m->user->setAttribute(
                    'total_points_cached',
                    (int) ($pointsMap[$m->user->id] ?? 0)
                );
            }
        });
    }

    /**
     * Build UI state untuk halaman invite.
     */
    private function buildInviteUiState(?Group $selectedGroup, $users): array
    {
        if (! $selectedGroup) {
            $users->each(function ($u) {
                $u->is_member       = false;
                $u->member_position = null;
                $u->other_groups    = $u->activeGroups ?? collect();
            });

            return [
                'hasKetua'    => false,
                'maxMembers'  => null,
                'memberCount' => 0,
                'slotsLeft'   => null,
                'isFull'      => false,
            ];
        }

        $memberMap = $selectedGroup->members->keyBy('user_id');

        $users->each(function ($u) use ($memberMap, $selectedGroup) {
            $m = $memberMap->get($u->id);
            $u->is_member       = (bool) $m;
            $u->member_position = $m?->position;
            $u->other_groups    = $u->activeGroups->where('id', '!=', $selectedGroup->id);
        });

        $maxMembers  = $selectedGroup->max_members;
        $memberCount = $selectedGroup->members_count;
        $slotsLeft   = $maxMembers ? max(0, $maxMembers - $memberCount) : null;

        return [
            'hasKetua'    => $selectedGroup->members->contains('position', 'ketua'),
            'maxMembers'  => $maxMembers,
            'memberCount' => $memberCount,
            'slotsLeft'   => $slotsLeft,
            'isFull'      => $maxMembers && $memberCount >= $maxMembers,
        ];
    }


    /* =========================================================
     | PRIVATE HELPERS — Membership Operations
     * ========================================================= */

    /**
     * Validasi sebelum menambahkan anggota. Return pesan error atau null.
     */
    private function validateMemberAddition(Group $group, User $target): ?string
    {
        if (! $target->is_active) {
            return "Tidak dapat menambahkan {$target->name}. Status akun tidak aktif.";
        }

        if (! in_array($target->level_id, self::MEMBER_LEVELS, true)) {
            return "Tidak dapat menambahkan {$target->name}. Level pengguna tidak diizinkan.";
        }

        if ($group->status !== 'aktif') {
            return "Kelompok \"{$group->name}\" tidak dalam status aktif.";
        }

        $alreadyMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $target->id)
            ->where('status', 'aktif')
            ->exists();

        if ($alreadyMember) {
            return "{$target->name} sudah menjadi anggota kelompok ini.";
        }

        if ($limit = $this->capacityLimit($group)) {
            $current = $this->countActiveMembers($group);
            if ($current >= $limit) {
                return "Kapasitas kelompok penuh ({$current}/{$limit}).";
            }
        }

        return null;
    }

    /**
     * Tentukan posisi final (otomatis ketua jika belum ada).
     */
    private function resolvePosition(Group $group, ?string $requested): string
    {
        $hasKetua = $this->groupHasKetua($group);
        $position = $hasKetua ? ($requested ?? 'anggota') : 'ketua';

        return in_array($position, self::POSITIONS, true) ? $position : 'anggota';
    }

    /**
     * Attach user ke group (create atau reaktivasi).
     */
    private function attachMember(Group $group, User $target, string $position): GroupMember
    {
        $permissions = $this->permissionsFor($position);

        $existing = GroupMember::where('group_id', $group->id)
            ->where('user_id', $target->id)
            ->first();

        if ($existing) {
            $existing->update(array_merge([
                'position'  => $position,
                'status'    => 'aktif',
                'joined_at' => $existing->joined_at ?? now(),
                'left_at'   => null,
            ], $permissions));

            return $existing;
        }

        return GroupMember::create(array_merge([
            'group_id'  => $group->id,
            'user_id'   => $target->id,
            'position'  => $position,
            'status'    => 'aktif',
            'joined_at' => now(),
        ], $permissions));
    }

    /**
     * Permission default berdasarkan posisi.
     */
    private function permissionsFor(string $position): array
    {
        return match ($position) {
            'ketua' => [
                'can_view'           => true,
                'can_announcement'   => true,
                'can_message'        => true,
                'can_manage_members' => true,
            ],
            'pengurus' => [
                'can_view'           => true,
                'can_announcement'   => false,
                'can_message'        => true,
                'can_manage_members' => false,
            ],
            default => [
                'can_view'           => true,
                'can_announcement'   => false,
                'can_message'        => false,
                'can_manage_members' => false,
            ],
        };
    }


    /* =========================================================
     | PRIVATE HELPERS — Counters
     * ========================================================= */

    private function countActiveMembers(Group $group): int
    {
        return GroupMember::where('group_id', $group->id)
            ->where('status', 'aktif')
            ->whereHas('user', fn ($q) => $q->whereIn('level_id', self::MEMBER_LEVELS))
            ->count();
    }

    private function countKetua(Group $group): int
    {
        return GroupMember::where('group_id', $group->id)
            ->where('position', 'ketua')
            ->where('status', 'aktif')
            ->count();
    }

    private function groupHasKetua(Group $group): bool
    {
        return $this->countKetua($group) > 0;
    }

    private function getActiveMemberIds(Group $group, array $userIds): array
    {
        return GroupMember::where('group_id', $group->id)
            ->whereIn('user_id', $userIds)
            ->where('status', 'aktif')
            ->pluck('user_id')
            ->toArray();
    }

    private function capacityLimit(Group $group): ?int
    {
        return ($group->max_members && $group->max_members > 0)
            ? $group->max_members
            : null;
    }


    /* =========================================================
     | PRIVATE HELPERS — Authorization
     * ========================================================= */

    private function isAdmin(?User $user): bool
    {
        return $user && in_array($user->level_id, self::ADMIN_LEVELS, true);
    }

    /**
     * Cek apakah user boleh manage members di group ini.
     */
    private function canManageMembers(Group $group): bool
    {
        $user = Auth::user();
        if (! $user) return false;

        if ($user->level_id === 1) return true; // Owner selalu boleh

        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        return $member && $member->can_manage_members;
    }


    /* =========================================================
     | PRIVATE HELPERS — Utilities
     * ========================================================= */

    private function generateGroupCode(): string
    {
        do {
            $code = 'KLP-' . strtoupper(Str::random(6));
        } while (Group::where('code', $code)->exists());

        return $code;
    }

    private function logGroupActivity(Group $group, User $target, string $position): void
    {
        if (! function_exists('activity') || ! Auth::check()) return;

        activity('groups')
            ->performedOn($group)
            ->causedBy(Auth::user())
            ->withProperties([
                'user_id'  => $target->id,
                'position' => $position,
            ])
            ->log("Menambahkan {$target->name} ke kelompok {$group->name}");
    }

    private function notifyGroupAddition(Group $group, User $target, string $position): void
    {
        if (! method_exists($target, 'notify')) return;

        try {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AddedToGroup',
                'notifiable_type' => User::class,
                'notifiable_id'   => $target->id,
                'data'            => json_encode([
                    'group_id'   => $group->id,
                    'group_name' => $group->name,
                    'position'   => $position,
                    'by'         => Auth::user()->name ?? 'Owner',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Tabel notifications mungkin belum ada — abaikan
        }
    }

    /**
     * Respond unified (JSON untuk AJAX, redirect untuk form biasa).
     */
    protected function respond(Request $request, Group $group, bool $ok, string $msg, array $extra = [])
    {
        $payload = array_merge([
            'ok'      => $ok,
            'message' => $msg,
            'group'   => [
                'id'    => $group->id,
                'count' => $this->countActiveMembers($group),
            ],
        ], $extra);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($payload, $ok ? 200 : 422);
        }

        return back()->with($ok ? 'success' : 'error', $msg);
    }
}