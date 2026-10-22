<?php

namespace App\Http\Controllers\Owner\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreGroupRequest;
use App\Models\Lms\LmsGroup;
use App\Services\Lms\LmsAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function __construct(protected LmsAuditService $audit) {}

    /* =========================================================
     | INDEX
     * ========================================================= */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $groups = LmsGroup::with([
                'creator:id,name,avatar_url,level_id',
                'creator.level:id,name',
            ])
            ->withCount('items')
            ->where('status', 'active')
            ->when($user->level_id > 2, fn ($q) => $q->where('creator_id', $user->id))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('owner.lms.groups.index', compact('groups'));
    }

    /* =========================================================
     | CREATE
     * ========================================================= */
public function create(Request $request): View
{
    $isKetua = $request->routeIs('ketua.*');
    $view    = $isKetua ? 'ketua.lms.groups.create' : 'owner.lms.groups.create';

    return view($view, [
        'group'   => null,
        'isKetua' => $isKetua,
    ]);
}
    /* =========================================================
     | STORE
     * ========================================================= */
public function store(StoreGroupRequest $request): RedirectResponse
{
    $data = $request->validated();
    $isKetua = $request->routeIs('ketua.*');

    $coverImages = [];
    if ($request->hasFile('cover')) {
        foreach ($request->file('cover') as $file) {
            $coverImages[] = $file->store('lms/groups/covers', 'public');
        }
    }

    $group = LmsGroup::create([
        'name'         => $data['name'],
        'description'  => $data['description'] ?? null,
        'cover_path'   => $coverImages[0] ?? null,
        'cover_images' => $coverImages,
        'creator_id'   => Auth::id(),
        'status'       => 'active',
    ]);

    $this->audit->log('group_created', $group, ['name' => $group->name]);

    $routeName = $isKetua ? 'ketua.lms.groups.show' : 'owner.lms.groups.show';

    return redirect()
        ->route($routeName, $group->id)
        ->with('success', 'Pengelompokan berhasil dibuat.');
}

    /* =========================================================
     | SHOW — Ini yang error sebelumnya
     * ========================================================= */
    public function show(LmsGroup $group): View
    {
        // 1. Load creator + level
        $group->load([
            'creator:id,name,avatar_url,level_id',
            'creator.level:id,name',
        ]);

        // 2. Load items dengan pagination & counter penerima
        $items = $group->items()
            ->with([
                'creator:id,name,avatar_url',
            ])
            ->withCount('recipients')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(16);   // kelipatan 4 → rapi di grid 4 kolom

        // 3. Counter untuk header stats (items, tasks, materials)
        $group->loadCount([
            'items as items_count',
            'items as tasks_count'     => fn ($q) => $q->where('type', 'task'),
            'items as materials_count' => fn ($q) => $q->where('type', 'material'),
        ]);

        return view('owner.lms.groups.show', compact('group', 'items'));
    }

    /* =========================================================
     | EDIT
     * ========================================================= */
    public function edit(LmsGroup $group): View
    {
        $this->ensureCanManage($group);
        return view('owner.lms.groups.edit', compact('group'));
    }

    /* =========================================================
     | UPDATE
     * ========================================================= */
    public function update(StoreGroupRequest $request, LmsGroup $group): RedirectResponse
    {
        $this->ensureCanManage($group);

        $data   = $request->validated();
        $before = $group->only(['name', 'description']);

        $group->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? $group->description,
        ]);

        $this->audit->log('group_updated', $group, [
            'before' => $before,
            'after'  => $group->only(['name', 'description']),
        ]);

        return redirect()
            ->route('owner.lms.groups.show', $group->id)
            ->with('success', 'Pengelompokan berhasil diperbarui.');
    }

    /* =========================================================
     | DESTROY
     * ========================================================= */
    public function destroy(Request $request, LmsGroup $group): RedirectResponse
    {
        $this->ensureCanManage($group);

        $reason = $request->input('reason', 'Tidak ada alasan');

        $group->update([
            'status'        => 'deleted',
            'deleted_at'    => now(),
            'deleted_by'    => Auth::id(),
            'delete_reason' => $reason,
        ]);

        $this->audit->log('group_deleted', $group, ['reason' => $reason], $reason);

        return redirect()
            ->route('owner.lms.groups.index')
            ->with('success', 'Pengelompokan berhasil dihapus.');
    }

    /* =========================================================
     | RESTORE
     * ========================================================= */
    public function restore(LmsGroup $group): RedirectResponse
    {
        $this->ensureCanManage($group);

        $group->update([
            'status'     => 'active',
            'deleted_at' => null,
            'deleted_by' => null,
        ]);

        $this->audit->log('group_restored', $group);

        return back()->with('success', 'Pengelompokan dipulihkan.');
    }

    /* =========================================================
     | AUTHORIZATION HELPERS
     * ========================================================= */
    protected function ensureCanView(LmsGroup $group): void
    {
        $user = Auth::user();
        if ($user->level_id <= 2) return;

        abort_unless($group->creator_id === $user->id, 403, 'Akses ditolak.');
    }

    protected function ensureCanManage(LmsGroup $group): void
    {
        $user = Auth::user();
        if ($user->level_id <= 2) return;

        abort_unless($group->creator_id === $user->id, 403, 'Akses ditolak.');
    }
}