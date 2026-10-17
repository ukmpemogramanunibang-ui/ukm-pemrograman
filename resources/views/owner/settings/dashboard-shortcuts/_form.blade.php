@php
    $item    = $item ?? null;
    $isEdit  = (bool) $item;
    $action  = $isEdit
        ? route('owner.settings.dashboard-shortcuts.update', $item)
        : route('owner.settings.dashboard-shortcuts.store');
    $preSel  = $preselectParent ?? null;
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      x-data="shortcutForm()" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

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
         SECTION 1: FOLDER INDUK (WAJIB)
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-folder-open"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Pilih Folder Induk</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Shortcut akan ditempatkan di dalam folder ini.
                </p>
            </div>
        </div>

        @php
            $selectedParent = old('parent_id', $item->parent_id ?? $preSel);
        @endphp

        @if ($parents->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700
                        p-6 text-center">
                <i class="fas fa-folder-open text-3xl text-gray-400 mb-2"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Belum ada folder
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Buat folder terlebih dahulu sebelum menambahkan shortcut.
                </p>
                <a href="{{ route('owner.settings.dashboard-cards.create') }}"
                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-xl
                          bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold">
                    <i class="fas fa-plus"></i> Buat Folder
                </a>
            </div>
        @else
            <select name="parent_id" id="parent_id" required
                    x-model="parentId"
                    class="w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                           focus:border-cyan-500 focus:ring-cyan-500 dark:text-white">
                <option value="">— Pilih Folder —</option>
                @foreach ($parents as $p)
                    <option value="{{ $p->id }}"
                            data-name="{{ $p->name }}"
                            data-icon="{{ $p->icon ?: 'fa-folder' }}"
                            data-category="{{ $p->category }}"
                            data-priority="{{ $p->priority }}"
                            data-schedule="{{ $p->schedule_label }}"
                            data-count="{{ $p->shortcuts_count }}"
                            {{ $selectedParent == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} ({{ $p->shortcuts_count }} shortcut)
                    </option>
                @endforeach
            </select>
            @error('parent_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

            {{-- FOLDER INFO PREVIEW --}}
            <div x-show="parentId" x-transition
                 class="mt-4 p-4 rounded-xl border-2 border-cyan-300 dark:border-cyan-800
                        bg-cyan-50 dark:bg-cyan-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-600 dark:text-cyan-400
                                flex items-center justify-center flex-shrink-0">
                        <i :class="'fas ' + parentData.icon" class="text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] uppercase tracking-wider text-cyan-700 dark:text-cyan-300 font-bold">
                            Folder Induk
                        </p>
                        <p class="font-bold text-gray-900 dark:text-white truncate" x-text="parentData.name"></p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 font-bold uppercase"
                                  x-text="parentData.category === 'formal' ? 'Formal' : 'Non Formal'"></span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-700 dark:text-amber-300 font-bold"
                                  x-text="'P' + parentData.priority"></span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-500/15 text-purple-700 dark:text-purple-300 font-bold"
                                  x-text="parentData.schedule"></span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-500/15 text-gray-700 dark:text-gray-300 font-bold"
                                  x-text="parentData.count + ' shortcut'"></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================
         SECTION 2: INFO SHORTCUT
         ============================================================ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6
                border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600
                        text-white flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Shortcut</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Link yang akan disimpan di dalam folder
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Nama Shortcut <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required x-model="name"
                       value="{{ old('name', $item->name ?? '') }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-cyan-500 focus:ring-cyan-500 dark:text-white"
                       placeholder="Contoh: Google Classroom">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    URL Tujuan <span class="text-red-500">*</span>
                </label>
                <input type="url" name="url" required
                       value="{{ old('url', $item->url ?? '') }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-cyan-500 focus:ring-cyan-500 dark:text-white"
                       placeholder="https://classroom.google.com">
                <p class="text-xs text-gray-500 mt-1">HTTPS atau HTTP saja.</p>
                @error('url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5 mt-5">
            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Icon Shortcut</label>
                <div class="mt-2 flex gap-2">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/15 text-cyan-500
                                flex items-center justify-center flex-shrink-0">
                        <i :class="'fas ' + (icon || 'fa-link')" class="text-lg"></i>
                    </div>
                    <input type="text" name="icon" x-model="icon"
                           value="{{ old('icon', $item->icon ?? 'fa-link') }}"
                           class="flex-1 rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                                  focus:border-cyan-500 focus:ring-cyan-500 dark:text-white"
                           placeholder="fa-link">
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Urutan dalam Folder
                </label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', $item->sort_order ?? 0) }}"
                       class="mt-2 w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700
                              focus:border-cyan-500 focus:ring-cyan-500 dark:text-white">
                <p class="text-xs text-gray-500 mt-1">Kecil = lebih awal</p>
            </div>
        </div>

        <div class="mt-5">
            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Logo / Icon Upload (opsional)
            </label>
            <input type="file" name="background_image"
                   accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                   class="mt-2 w-full text-sm text-gray-700 dark:text-gray-300">
            <p class="text-xs text-gray-500 mt-1">
                Jika tidak diisi, akan menggunakan FontAwesome icon.
            </p>
            @if ($isEdit && $item->background_image)
                <div class="mt-2 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $item->background_image) }}"
                         class="w-16 h-16 rounded-lg object-cover border">
                    <span class="text-xs text-gray-500">Logo saat ini</span>
                </div>
            @endif
        </div>

        <div class="mt-5 flex items-center gap-3">
            <input type="hidden" name="open_in_new_tab" value="0">
            <input type="checkbox" name="open_in_new_tab" value="1" id="open_tab"
                   {{ old('open_in_new_tab', $item->open_in_new_tab ?? true) ? 'checked' : '' }}
                   class="rounded text-cyan-500 w-5 h-5">
            <label for="open_tab" class="text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200">
                Buka di tab baru
            </label>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active_shortcut"
                   {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
                   class="rounded text-green-500 w-5 h-5">
            <label for="is_active_shortcut" class="text-sm font-semibold cursor-pointer text-gray-700 dark:text-gray-200">
                <i class="fas fa-check-circle text-green-500 mr-1"></i> Aktifkan Shortcut
            </label>
        </div>
    </div>

    {{-- ============================================================
         SECTION 3: INFO TENTANG AKSES
         ============================================================ --}}
    <div class="rounded-2xl border-2 border-blue-300 dark:border-blue-800
                bg-blue-50 dark:bg-blue-900/20 p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-600 dark:text-blue-400
                        flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-sm">
                <p class="font-bold text-blue-900 dark:text-blue-200">
                    Akses otomatis mengikuti folder
                </p>
                <p class="text-blue-800 dark:text-blue-300 mt-1 text-xs leading-relaxed">
                    Shortcut ini tidak perlu mengatur target pengguna sendiri. Siapa yang bisa melihat
                    folder induk, otomatis bisa melihat shortcut ini juga. Untuk mengubah akses, edit
                    folder induk.
                </p>
            </div>
        </div>
    </div>

    {{-- ============ ACTIONS ============ --}}
    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="{{ route('owner.settings.general') }}"
           class="px-5 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-sm font-semibold text-center
                  text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
            Batal
        </a>
        <button type="submit" @if ($parents->isEmpty()) disabled @endif
                class="px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 disabled:opacity-50
                       disabled:cursor-not-allowed text-white font-bold shadow-lg shadow-cyan-500/20 transition">
            <i class="fas fa-{{ $isEdit ? 'save' : 'plus' }} mr-2"></i>
            {{ $isEdit ? 'Simpan Perubahan' : 'Create Icon Shortcut' }}
        </button>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function shortcutForm() {
    return {
        name: @json(old('name', $item->name ?? '')),
        icon: @json(old('icon', $item->icon ?? 'fa-link')),
        parentId: @json((string) old('parent_id', $item->parent_id ?? ($preSel ?? ''))),

        get parentData() {
            const sel = document.getElementById('parent_id');
            const opt = sel?.querySelector(`option[value="${this.parentId}"]`);
            if (!opt) return { name: '', icon: 'fa-folder', category: 'formal', priority: 0, schedule: '-', count: 0 };
            return {
                name: opt.dataset.name || '',
                icon: opt.dataset.icon || 'fa-folder',
                category: opt.dataset.category || 'formal',
                priority: opt.dataset.priority || 0,
                schedule: opt.dataset.schedule || '-',
                count: opt.dataset.count || 0,
            };
        },
    };
}
</script>
@endpush