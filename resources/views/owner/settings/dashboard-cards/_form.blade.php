@php
    $item   = $item ?? null;
    $isEdit = (bool) $item;
    $action = $isEdit
        ? route('owner.settings.dashboard-cards.update', $item)
        : route('owner.settings.dashboard-cards.store');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      x-data="cardForm()" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- ============ ERROR SUMMARY ============ --}}
    @if ($errors->any())
        <div class="rounded-2xl border-2 border-red-300 dark:border-red-800
                    bg-red-50 dark:bg-red-900/20 p-4">
            <p class="font-bold text-red-800 dark:text-red-200 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> Ada kesalahan
            </p>
            <ul class="mt-2 space-y-0.5 text-xs text-red-700 dark:text-red-300 list-disc list-inside">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ============================================================
         SECTION 1: IDENTITAS FOLDER
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-folder-open"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Identitas Folder
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Card berfungsi sebagai folder untuk menyimpan shortcut pada dashboard pengguna.
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            {{-- Nama Folder --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Nama Folder <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required
                       x-model="name"
                       value="{{ old('name', $item->name ?? '') }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-cyan-500 focus:ring-cyan-500 dark:text-white"
                       placeholder="Contoh: Portal Akademik">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Icon --}}
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Icon Folder
                </label>
                <div class="mt-2 flex gap-2">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/15 text-cyan-500
                                flex items-center justify-center flex-shrink-0">
                        <i :class="'fas ' + (icon || 'fa-folder')" class="text-lg"></i>
                    </div>
                    <input type="text" name="icon"
                           x-model="icon"
                           value="{{ old('icon', $item->icon ?? 'fa-folder') }}"
                           class="flex-1 rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                                  focus:border-cyan-500 focus:ring-cyan-500 dark:text-white"
                           placeholder="fa-folder">
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Contoh:
                    <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700">fa-folder</code>,
                    <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700">fa-graduation-cap</code>,
                    <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-700">fa-building</code>
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 2: AKSES FOLDER
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Akses Folder
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Semua shortcut di dalam folder akan otomatis mengikuti akses ini.
                </p>
            </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-3">
            @foreach ([
                ['roles', 'fa-users-cog',  'Target Role',   'purple', 'Satu atau beberapa role'],
                ['users', 'fa-user-check', 'Target Pribadi','amber',  'Satu atau beberapa akun'],
            ] as [$val, $ic, $title, $color, $desc])
                @php $checked = old('audience_mode', $item->audience_mode ?? 'all') === $val; @endphp
                <label class="cursor-pointer">
                    <input type="radio" name="audience_mode" value="{{ $val }}"
                           {{ $checked ? 'checked' : '' }} class="peer sr-only"
                           @change="audience = $event.target.value">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 transition
                                peer-checked:border-{{ $color }}-500 peer-checked:bg-{{ $color }}-50
                                dark:peer-checked:bg-{{ $color }}-900/20">
                        <i class="fas {{ $ic }} text-{{ $color }}-500 text-xl"></i>
                        <p class="font-bold mt-2 text-sm text-gray-900 dark:text-white">{{ $title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $desc }}</p>
                    </div>
                </label>
            @endforeach
        </div>

        @php
            $oldRoles = old('role_ids', $isEdit ? $item->targets->pluck('role_id')->filter()->toArray() : []);
            $oldUsers = old('user_ids', $isEdit ? $item->targets->pluck('user_id')->filter()->toArray() : []);
        @endphp

        {{-- Role checkboxes --}}
        <div x-show="audience === 'roles'" x-transition class="mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-sm text-gray-800 dark:text-white">Pilih Role</h3>
                <label class="text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="select-all-roles" class="rounded"> Pilih Semua
                </label>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700
                                  cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                               {{ in_array($role->id, $oldRoles) ? 'checked' : '' }}
                               class="role-checkbox rounded text-cyan-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- User checkboxes --}}
        <div x-show="audience === 'users'" x-transition class="mt-6">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between mb-3">
                <input type="text" id="user-search" placeholder="🔎 Cari nama / NIM / email..."
                       class="flex-1 rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-amber-500 dark:text-white">
                <label class="text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" id="select-all-users" class="rounded"> Pilih Semua
                </label>
            </div>
            <div class="max-h-80 overflow-y-auto grid sm:grid-cols-2 gap-2 p-1">
                @foreach ($users as $u)
                    @php $checked = in_array($u->id, $oldUsers); @endphp
                    <label class="user-target-item flex items-center gap-3 p-3 rounded-xl
                                  border border-gray-200 dark:border-gray-700 cursor-pointer
                                  hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
                           data-search="{{ strtolower($u->name . ' ' . ($u->nim ?? '') . ' ' . ($u->email ?? '')) }}">
                        <input type="checkbox" name="user_ids[]" value="{{ $u->id }}"
                               {{ $checked ? 'checked' : '' }}
                               class="user-checkbox rounded text-amber-500">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate text-gray-800 dark:text-gray-100">{{ $u->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $u->nim ?? '-' }} · {{ optional($u->level)->name ?? '-' }}
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 3: BACKGROUND & LIVE PREVIEW
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-palette"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Background Folder</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kustomisasi tampilan folder</p>
            </div>
        </div>

        @php
    $bg = old('background_type', $item->background_type ?? 'plain');
@endphp
        <div class="grid sm:grid-cols-3 gap-3">
            @foreach ([
                ['plain', 'fa-fill',    'Polos'],
                ['color', 'fa-palette', 'Warna Solid'],
                ['image', 'fa-image',   'Upload Gambar'],
            ] as [$val, $ic, $label])
                <label class="cursor-pointer">
                    <input type="radio" name="background_type" value="{{ $val }}"
       {{ ($bg ?? null) === $val ? 'checked' : '' }} class="peer sr-only"
       @change="bgType = $event.target.value">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-center transition
                                peer-checked:border-pink-500 peer-checked:bg-pink-50 dark:peer-checked:bg-pink-900/20">
                        <i class="fas {{ $ic }} text-pink-500 text-lg"></i>
                        <p class="font-semibold text-xs mt-1 text-gray-700 dark:text-gray-200">{{ $label }}</p>
                    </div>
                </label>
            @endforeach
        </div>

        <div x-show="bgType === 'color'" x-transition class="mt-5">
            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Warna Solid</label>
            <input type="color" name="background_color"
                   x-model="bgColor"
                   value="{{ old('background_color', $item->background_color ?? '#0891b2') }}"
                   class="mt-2 h-14 w-full rounded-xl cursor-pointer">
        </div>

        <div x-show="bgType === 'image'" x-transition class="mt-5">
            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Upload Gambar</label>
            <input type="file" name="background_image" accept=".jpg,.jpeg,.png,.webp,.gif"
                   class="mt-2 w-full text-sm">
            <p class="text-xs text-gray-500 mt-1">JPG/PNG/WEBP/GIF. Maksimum 4 MB.</p>
            @if ($isEdit && $item->background_image)
                <div class="mt-2 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $item->background_image) }}"
                         class="w-20 h-14 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                    <span class="text-xs text-gray-500">Gambar saat ini</span>
                </div>
            @endif
        </div>

        {{-- LIVE PREVIEW --}}
        <div class="mt-6">
            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                Live Preview
            </p>
            <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div :style="previewStyle" class="p-5 min-h-[110px] text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur
                                    flex items-center justify-center flex-shrink-0">
                            <i :class="'fas ' + (icon || 'fa-folder')" class="text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-extrabold drop-shadow-sm truncate"
                                x-text="name || 'Nama Folder'"></h3>
                            <p class="text-xs opacity-80">Folder Dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 4: KATEGORI, PRIORITAS & JADWAL
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Kategori, Prioritas & Jadwal</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Aturan ini berlaku untuk folder dan seluruh shortcut di dalamnya.
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category" required
                        class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                               focus:border-amber-500 dark:text-white">
                    <option value="formal" {{ old('category', $item->category ?? 'formal') === 'formal' ? 'selected' : '' }}>Formal</option>
                    <option value="non_formal" {{ old('category', $item->category ?? '') === 'non_formal' ? 'selected' : '' }}>Non Formal</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Prioritas</label>
                <input type="number" name="priority" min="0" max="9999"
                       value="{{ old('priority', $item->priority ?? 0) }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-amber-500 dark:text-white">
                <p class="text-xs text-gray-500 mt-1">Lebih tinggi = tampil lebih awal.</p>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Urutan Folder</label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', $item->sort_order ?? 0) }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-amber-500 dark:text-white">
            </div>
        </div>

        {{-- JADWAL --}}
        <div class="mt-6">
            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Jadwal Tampilan</label>
            @php $sched = old('schedule_type', $item->schedule_type ?? 'always'); @endphp
            <div class="grid md:grid-cols-3 gap-3 mt-3">
                @foreach ([
                    ['always',    'fa-infinity',       'Tampil Selamanya', 'Tanpa kadaluarsa'],
                    ['scheduled', 'fa-calendar-alt',   'Terjadwal',        'Periode tertentu'],
                    ['expires',   'fa-hourglass-end',  'Kadaluarsa',       'Sampai tanggal'],
                ] as [$val, $ic, $title, $desc])
                    <label class="cursor-pointer">
                        <input type="radio" name="schedule_type" value="{{ $val }}"
                               {{ $sched === $val ? 'checked' : '' }} class="peer sr-only"
                               @change="scheduleType = $event.target.value">
                        <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 transition
                                    peer-checked:border-amber-500 peer-checked:bg-amber-50
                                    dark:peer-checked:bg-amber-900/20">
                            <i class="fas {{ $ic }} text-amber-500 text-xl"></i>
                            <p class="font-bold text-sm mt-2 text-gray-900 dark:text-white">{{ $title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $desc }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div x-show="scheduleType !== 'always'" x-transition
             class="grid md:grid-cols-2 gap-5 mt-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mulai Tampil</label>
                <input type="datetime-local" name="starts_at"
                       value="{{ old('starts_at', optional($item->starts_at ?? null)->format('Y-m-d\TH:i')) }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Berakhir</label>
                <input type="datetime-local" name="ends_at"
                       value="{{ old('ends_at', optional($item->ends_at ?? null)->format('Y-m-d\TH:i')) }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
                   class="rounded text-green-500 w-5 h-5">
            <label for="is_active" class="text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200">
                <i class="fas fa-check-circle text-green-500 mr-1"></i> Aktifkan Folder
            </label>
        </div>
    </div>

    {{-- ============ ACTIONS ============ --}}
    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('owner.settings.general') }}"
           class="px-5 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-sm font-semibold text-center
                  text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            Batal
        </a>
        <button type="submit"
                class="px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold
                       shadow-lg shadow-cyan-500/20 transition">
            <i class="fas fa-{{ $isEdit ? 'save' : 'plus' }} mr-2"></i>
            {{ $isEdit ? 'Simpan Perubahan' : 'Create Folder' }}
        </button>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function cardForm() {
    return {
        name: @json(old('name', $item->name ?? '')),
        icon: @json(old('icon', $item->icon ?? 'fa-folder')),
        audience: @json(old('audience_mode', $item->audience_mode ?? 'all')),
        bgType: @json(old('background_type', $item->background_type ?? 'plain')),
        bgColor: @json(old('background_color', $item->background_color ?? '#0891b2')),
        scheduleType: @json(old('schedule_type', $item->schedule_type ?? 'always')),

        get previewStyle() {
            if (this.bgType === 'color') {
                return `background: ${this.bgColor};`;
            }
            return 'background: linear-gradient(135deg, #0891b2, #2563eb, #4f46e5);';
        },
    };
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('select-all-roles')?.addEventListener('change', function () {
        document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('select-all-users')?.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            if (cb.closest('.user-target-item').style.display !== 'none') {
                cb.checked = this.checked;
            }
        });
    });

    document.getElementById('user-search')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('.user-target-item').forEach(el => {
            el.style.display = (!q || el.dataset.search.includes(q)) ? '' : 'none';
        });
    });
});
</script>
@endpush