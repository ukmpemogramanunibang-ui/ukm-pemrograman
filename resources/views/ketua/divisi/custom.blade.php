@extends('layouts.ketua')
@section('title', ($division ?? null) ? 'Edit Divisi' : 'Buat Divisi Baru')

@section('content')
<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-5">

    {{-- BREADCRUMB --}}
    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-violet-600">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.divisi.index') }}" class="hover:text-violet-600">Divisi</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">
            {{ ($division ?? null) ? 'Edit' : 'Buat Baru' }}
        </span>
    </nav>

    {{-- HEADER --}}
    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-sitemap"></i> Manajemen Divisi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">
                {{ ($division ?? null) ? 'Edit Divisi' : 'Buat Divisi Baru' }}
            </h1>
            <p class="text-sm opacity-90 mt-1">
                Isi form dengan lengkap. Pilih ketua & pengurus divisi.
            </p>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('warning'))
        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 text-amber-700 dark:text-amber-300 text-sm">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-700 dark:text-rose-300 text-sm">
            <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40">
            <p class="text-sm font-bold text-rose-700 dark:text-rose-300 mb-2">
                <i class="fas fa-times-circle mr-1"></i>
                Ada {{ $errors->count() }} kesalahan:
            </p>
            <ul class="ml-4 list-disc text-xs text-rose-600 dark:text-rose-300 space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST"
          action="{{ ($division ?? null) ? route('ketua.divisi.update', $division->id) : route('ketua.divisi.store') }}"
          class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 space-y-4">
        @csrf
        @if($division ?? null) @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Nama Divisi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required maxlength="255"
                       value="{{ old('name', $division->name ?? '') }}"
                       placeholder="Contoh: Divisi Pengembang"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-900 text-sm
                              focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Status</label>
                <select name="status"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-900 text-sm
                               focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
                    <option value="aktif" {{ old('status', $division->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $division->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" maxlength="2000"
                          placeholder="Jelaskan tujuan atau cakupan divisi ini..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                                 bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[100px]
                                 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">{{ old('description', $division->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Maksimal Anggota
                </label>
                <input type="number" name="max_members" min="1" max="999"
                       value="{{ old('max_members', $division->max_members ?? '') }}"
                       placeholder="Contoh: 20"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-900 text-sm
                              focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                    Label Ketua
                </label>
                <input type="text" name="ketua_label" maxlength="100"
                       value="{{ old('ketua_label', $division->ketua_label ?? 'Ketua') }}"
                       placeholder="Contoh: Ketua / PJ / Koordinator"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                              bg-gray-50 dark:bg-gray-900 text-sm
                              focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
            </div>
        </div>

        @if(!($division ?? null))
            {{-- KETUA DIVISI --}}
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-black text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-crown text-amber-500"></i> Pilih Ketua Divisi <span class="text-rose-500">*</span>
                </h3>
                <select name="ketua_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-900 text-sm
                               focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400">
                    <option value="">-- Pilih Ketua --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('ketua_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->level->name ?? '-' }}) — {{ $user->nim ?? '-' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-400 mt-1">
                    Hanya user level Ketua, Pengurus, atau Anggota UKM.
                </p>
            </div>

            {{-- PENGURUS --}}
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700" id="pengurus-wrapper">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white">
                        <i class="fas fa-users-cog text-blue-500"></i> Pengurus Divisi
                    </h3>
                    <button type="button" id="add-pengurus-btn"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                   bg-blue-500 text-white text-xs font-bold
                                   hover:bg-blue-600 transition">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
                <div id="pengurus-list" class="space-y-3"></div>
                <p class="text-[11px] text-rose-500 mt-2 hidden" id="pengurus-error">
                    <i class="fas fa-exclamation-circle"></i>
                    Akun yang sama tidak boleh dipilih lebih dari satu.
                </p>
            </div>
        @else
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="p-4 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700">
                    <p class="text-xs text-violet-700 dark:text-violet-300">
                        <i class="fas fa-info-circle mr-1"></i>
                        Untuk mengubah keanggotaan, gunakan menu
                        <a href="{{ route('ketua.divisi.anggota', ['divisionId' => $division->id]) }}"
                           class="font-bold underline">Anggota Divisi</a>.
                    </p>
                </div>
            </div>
        @endif

        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('ketua.divisi.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Batal
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-500 to-fuchsia-600
                           hover:shadow-lg hover:shadow-violet-500/30 transition active:scale-95">
                <i class="fas fa-save"></i>
                {{ ($division ?? null) ? 'Perbarui Divisi' : 'Simpan Divisi' }}
            </button>
        </div>
    </form>
</div>

@if(!($division ?? null))
@push('scripts')
<script>
(function () {
    'use strict';
    const USERS = @json($userOptions);
    const list  = document.getElementById('pengurus-list');
    const errEl = document.getElementById('pengurus-error');

    function createOptions(selectedId = null, excludedIds = []) {
        let html = '<option value="">-- Pilih Pengurus --</option>';
        USERS.forEach(u => {
            const isDisabled = excludedIds.includes(u.id) && u.id !== selectedId;
            const selected = selectedId == u.id ? 'selected' : '';
            const label = isDisabled
                ? `${u.name} (${u.level}) — ${u.nim} [Sudah dipilih]`
                : `${u.name} (${u.level}) — ${u.nim}`;
            html += `<option value="${u.id}" ${selected} ${isDisabled ? 'disabled' : ''}>${label}</option>`;
        });
        return html;
    }

    function getSelected() {
        return Array.from(document.querySelectorAll('.pengurus-select'))
            .map(s => parseInt(s.value)).filter(Boolean);
    }

    function refreshDropdowns() {
        const ids = getSelected();
        document.querySelectorAll('.pengurus-select').forEach(sel => {
            const current = parseInt(sel.value);
            sel.querySelectorAll('option').forEach(opt => {
                const val = parseInt(opt.value);
                if (!val) return;
                const disabled = ids.includes(val) && val !== current;
                opt.disabled = disabled;
                const cleanText = opt.text.replace(' [Sudah dipilih]', '');
                opt.text = disabled ? cleanText + ' [Sudah dipilih]' : cleanText;
            });
        });
    }

    function validate() {
        const ids = getSelected();
        const hasDup = ids.length !== new Set(ids).size;
        errEl.classList.toggle('hidden', !hasDup);
        return !hasDup;
    }

    function createItem(selectedId = null, jabatan = '') {
        const div = document.createElement('div');
        div.className = 'pengurus-item flex flex-wrap items-end gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800';
        div.innerHTML = `
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-1">Pengurus</label>
                <select name="pengurus_ids[]" class="pengurus-select w-full px-3 py-2 rounded-lg
                        border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm">
                    ${createOptions(selectedId, getSelected())}
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-1">Jabatan (opsional)</label>
                <input type="text" name="pengurus_jabatan[]" value="${jabatan}"
                       placeholder="Sekretaris, Bendahara..."
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-sm">
            </div>
            <button type="button" class="remove-pengurus-btn w-8 h-8 rounded-lg bg-rose-500 text-white
                    flex items-center justify-center hover:bg-rose-600 transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        div.querySelector('.remove-pengurus-btn').addEventListener('click', () => {
            div.remove(); refreshDropdowns(); validate();
        });
        div.querySelector('.pengurus-select').addEventListener('change', () => {
            refreshDropdowns(); validate();
        });
        return div;
    }

    document.getElementById('add-pengurus-btn').addEventListener('click', () => {
        list.appendChild(createItem());
        refreshDropdowns();
    });

    // Default 1 item
    list.appendChild(createItem());
    refreshDropdowns();
})();
</script>
@endpush
@endif
@endsection