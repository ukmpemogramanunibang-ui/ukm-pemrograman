<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\DashboardItem;
use App\Models\DashboardItemTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class DashboardBuilderController extends Controller
{
    // =====================================================================
    // GUARD
    // =====================================================================
    private function ensureOwner(): void
    {
        abort_unless(auth()->check(), 403, 'Silakan login terlebih dahulu.');
        abort_unless((int) (auth()->user()->level_id ?? 0) === 1, 403, 'Hanya Owner.');
    }

    // =====================================================================
    // CARDS (Folder)
    // =====================================================================

    public function createCard()
    {
        $this->ensureOwner();

        return view('owner.settings.dashboard-cards.create', $this->formData());
    }

    public function storeCard(Request $request)
    {
        $this->ensureOwner();

        $data = $this->validateCard($request);

        $item = DB::transaction(function () use ($request, $data) {
            $item = DashboardItem::create($this->fillCard($data, $request));
            $this->syncTargets($item, $data);
            return $item;
        });

        return redirect()
            ->route('owner.settings.general')
            ->with('success', "✅ Folder \"{$item->name}\" berhasil dibuat.");
    }

    public function editCard(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'card', 404);

        return view('owner.settings.dashboard-cards.edit', array_merge(
            $this->formData(),
            ['item' => $item->load(['targets', 'shortcuts'])]
        ));
    }

    public function updateCard(Request $request, DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'card', 404);

        $data = $this->validateCard($request);

        DB::transaction(function () use ($request, $data, $item) {
            $item->update($this->fillCard($data, $request, $item));
            $item->targets()->delete();
            $this->syncTargets($item, $data);
        });

        return redirect()
            ->route('owner.settings.general')
            ->with('success', "✅ Folder \"{$item->name}\" berhasil diperbarui.");
    }

    public function destroyCard(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'card', 404);

        if ($item->background_image) {
            Storage::disk('public')->delete($item->background_image);
        }

        // Soft delete children too
        $item->shortcuts()->delete();
        $item->delete();

        return back()->with('success', "🗑️ Folder \"{$item->name}\" dipindahkan ke Trash.");
    }

    public function toggleCard(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'card', 404);

        $item->update(['is_active' => ! $item->is_active]);

        return back()->with('success', $item->is_active
            ? "✅ Folder \"{$item->name}\" diaktifkan."
            : "⏸️ Folder \"{$item->name}\" dinonaktifkan.");
    }

    // =====================================================================
    // SHORTCUTS
    // =====================================================================

    public function createShortcut(Request $request)
    {
        $this->ensureOwner();

        return view('owner.settings.dashboard-shortcuts.create', $this->formData(
            shortcut: true,
            preselectParent: $request->integer('parent_id') ?: null
        ));
    }

    public function storeShortcut(Request $request)
    {
        $this->ensureOwner();

        $data = $this->validateShortcut($request);

        $item = DashboardItem::create($this->fillShortcut($data, $request));

        return redirect()
            ->route('owner.settings.general')
            ->with('success', "✅ Shortcut \"{$item->name}\" berhasil ditambahkan ke folder.");
    }

    public function editShortcut(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'shortcut', 404);

        return view('owner.settings.dashboard-shortcuts.edit', array_merge(
            $this->formData(shortcut: true),
            ['item' => $item]
        ));
    }

    public function updateShortcut(Request $request, DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'shortcut', 404);

        $data = $this->validateShortcut($request);

        $item->update($this->fillShortcut($data, $request, $item));

        return redirect()
            ->route('owner.settings.general')
            ->with('success', "✅ Shortcut \"{$item->name}\" berhasil diperbarui.");
    }

    public function destroyShortcut(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'shortcut', 404);

        if ($item->background_image) {
            Storage::disk('public')->delete($item->background_image);
        }

        $item->delete();

        return back()->with('success', "🗑️ Shortcut \"{$item->name}\" dipindahkan ke Trash.");
    }

    public function toggleShortcut(DashboardItem $item)
    {
        $this->ensureOwner();
        abort_unless($item->type === 'shortcut', 404);

        $item->update(['is_active' => ! $item->is_active]);

        return back()->with('success', $item->is_active
            ? "✅ Shortcut \"{$item->name}\" diaktifkan."
            : "⏸️ Shortcut \"{$item->name}\" dinonaktifkan.");
    }

    // =====================================================================
    // HELPERS
    // =====================================================================

    private function formData(bool $shortcut = false, ?int $preselectParent = null): array
    {
        return [
            'roles' => class_exists(Role::class) ? Role::orderBy('name')->get() : collect(),

            'users' => User::with('level')->orderBy('name')->get(),

'parents' => $shortcut
    ? DashboardItem::query()
        ->cards()
        ->active()
        ->withCount('shortcuts')  // ✅ ini AMAN — dengan "withCount" panggil relasi instance
        ->orderBy('name')
        ->get()
    : collect(),
    
            'preselectParent' => $preselectParent,
        ];
    }

    /**
     * Validasi khusus CARD/FOLDER (tanpa url, title, description).
     */
    private function validateCard(Request $request): array
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'icon'  => ['nullable', 'string', 'max:100'],

            'audience_mode' => ['required', Rule::in(['all', 'roles', 'users'])],
            'role_ids'      => ['nullable', 'array'],
            'role_ids.*'    => ['integer', 'exists:roles,id'],
            'user_ids'      => ['nullable', 'array'],
            'user_ids.*'    => ['integer', 'exists:users,id'],

            'background_type'  => ['required', Rule::in(['plain', 'color', 'image'])],
            'background_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],

            'category'   => ['required', Rule::in(['formal', 'non_formal'])],
            'priority'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'schedule_type' => ['required', Rule::in(['always', 'scheduled', 'expires'])],
            'starts_at'     => ['nullable', 'date'],
            'ends_at'       => ['nullable', 'date', 'after_or_equal:starts_at'],

            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['audience_mode'] === 'roles' && empty($data['role_ids'])) {
            abort(back()->withErrors(['role_ids' => 'Pilih minimal satu role.'])->withInput()->getStatusCode());
        }
        if ($data['audience_mode'] === 'users' && empty($data['user_ids'])) {
            abort(back()->withErrors(['user_ids' => 'Pilih minimal satu akun.'])->withInput()->getStatusCode());
        }

        // Normalisasi schedule
        if ($data['schedule_type'] === 'always') {
            $data['starts_at'] = null;
            $data['ends_at']   = null;
        }

        return $data;
    }

    /**
     * Validasi khusus SHORTCUT (wajib parent_id, tanpa target).
     */
    private function validateShortcut(Request $request): array
    {
        return $request->validate([
            'parent_id' => ['required', 'integer', 'exists:dashboard_items,id'],
            'name'      => ['required', 'string', 'max:100'],
            'url'       => [
                'required', 'string', 'max:2048',
                function ($attribute, $value, $fail) {
                    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
                    if (! in_array($scheme, ['https', 'http'], true)) {
                        $fail('URL harus menggunakan http atau https.');
                    }
                },
            ],
            'icon' => ['nullable', 'string', 'max:100'],

            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:4096'],

            'sort_order'      => ['nullable', 'integer', 'min:0'],
            'open_in_new_tab' => ['nullable', 'boolean'],
            'is_active'       => ['nullable', 'boolean'],
        ]);
    }

    private function fillCard(array $data, Request $request, ?DashboardItem $existing = null): array
    {
        $imagePath = $this->handleImage($data, $request, $existing);

        $payload = [
            'type'             => 'card',
            'name'             => $data['name'],
            'title'            => $data['name'], // kept for legacy
            'description'      => null,
            'icon'             => $data['icon'] ?? 'fa-folder',
            'url'              => null,
            'audience_mode'    => $data['audience_mode'],
            'background_type'  => $data['background_type'],
            'background_color' => $data['background_color'] ?? null,
            'background_image' => $imagePath,
            'category'         => $data['category'],
            'priority'         => $data['priority'] ?? 0,
            'sort_order'       => $data['sort_order'] ?? 0,
            'schedule_type'    => $data['schedule_type'],
            'starts_at'        => $data['starts_at'] ?? null,
            'ends_at'          => $data['ends_at'] ?? null,
            'is_active'        => (bool) ($data['is_active'] ?? true),
            'updated_by'       => auth()->id(),
        ];

        if (! $existing) {
            $payload['created_by'] = auth()->id();
        }

        return $payload;
    }

    private function fillShortcut(array $data, Request $request, ?DashboardItem $existing = null): array
    {
        $imagePath = $this->handleImage($data, $request, $existing);

        $payload = [
            'type'             => 'shortcut',
            'parent_id'        => $data['parent_id'],
            'name'             => $data['name'],
            'title'            => $data['name'],
            'description'      => null,
            'icon'             => $data['icon'] ?? 'fa-link',
            'url'              => $data['url'],
            'open_in_new_tab'  => (bool) ($data['open_in_new_tab'] ?? true),
            'audience_mode'    => 'all', // follows parent card
            'background_type'  => 'plain',
            'background_image' => $imagePath,
            'category'         => 'formal',
            'priority'         => 0,
            'sort_order'       => $data['sort_order'] ?? 0,
            'schedule_type'    => 'always',
            'is_active'        => (bool) ($data['is_active'] ?? true),
            'updated_by'       => auth()->id(),
        ];

        if (! $existing) {
            $payload['created_by'] = auth()->id();
        }

        return $payload;
    }

    private function handleImage(array $data, Request $request, ?DashboardItem $existing): ?string
    {
        $imagePath = $existing?->background_image;

        $bgType = $data['background_type'] ?? 'plain';

        if (($bgType === 'image' || isset($data['url'])) && $request->hasFile('background_image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            return $request->file('background_image')->store('dashboard/backgrounds', 'public');
        }

        if (isset($data['url']) && $request->hasFile('background_image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            return $request->file('background_image')->store('dashboard/backgrounds', 'public');
        }

        if (isset($data['background_type']) && $data['background_type'] !== 'image') {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
                return null;
            }
        }

        return $imagePath;
    }

    private function syncTargets(DashboardItem $item, array $data): void
    {
        if ($data['audience_mode'] === 'roles') {
            foreach (array_unique($data['role_ids'] ?? []) as $roleId) {
                DashboardItemTarget::create([
                    'dashboard_item_id' => $item->id,
                    'role_id'           => $roleId,
                ]);
            }
        }

        if ($data['audience_mode'] === 'users') {
            foreach (array_unique($data['user_ids'] ?? []) as $userId) {
                DashboardItemTarget::create([
                    'dashboard_item_id' => $item->id,
                    'user_id'           => $userId,
                ]);
            }
        }
    }
}