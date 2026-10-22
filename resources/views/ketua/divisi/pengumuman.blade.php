@extends('layouts.ketua')
@section('title', 'Pengumuman Divisi')

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto space-y-5">

    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('ketua.dashboard') }}" class="hover:text-violet-600"><i class="fas fa-home"></i> Dashboard</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <a href="{{ route('ketua.divisi.index') }}" class="hover:text-violet-600">Divisi</a>
        <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
        <span class="font-bold text-gray-700 dark:text-gray-300">Pengumuman</span>
    </nav>

    <div class="rounded-2xl p-5 text-white bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 shadow-xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 border border-white/25">
                <i class="fas fa-bullhorn"></i> Broadcast
            </span>
            <h1 class="text-2xl sm:text-3xl font-black mt-3">Pengumuman Divisi</h1>
            <p class="text-sm opacity-90 mt-1">Kirim pengumuman ke satu atau semua divisi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 text-emerald-700 dark:text-emerald-300 text-sm">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('ketua.divisi.announcement.store') }}"
          class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm p-5 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Judul Pengumuman <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="title" required maxlength="255"
                   placeholder="Contoh: Perubahan Jadwal Rapat"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                          bg-gray-50 dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/30">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Isi Pengumuman <span class="text-rose-500">*</span>
            </label>
            <textarea name="content" rows="6" required maxlength="10000"
                      placeholder="Tulis isi pengumuman..."
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700
                             bg-gray-50 dark:bg-gray-900 text-sm resize-y min-h-[140px]
                             focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">
                Target Divisi
            </label>
            <div class="space-y-2 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="target_all" value="1" id="target_all"
                           class="w-4 h-4 accent-amber-500">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-globe text-amber-500 mr-1"></i> Semua Divisi
                    </span>
                </label>
                <div class="ml-6 space-y-1.5 pt-2 border-t border-gray-200 dark:border-gray-700">
                    @foreach($divisions as $d)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="target_division_ids[]" value="{{ $d->id }}"
                                   class="w-4 h-4 accent-amber-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $d->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('ketua.divisi.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                      text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700
                      hover:bg-gray-200 transition">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-amber-500 to-orange-600
                           hover:shadow-lg transition active:scale-95">
                <i class="fas fa-bullhorn"></i> Kirim Pengumuman
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('target_all')?.addEventListener('change', function () {
    document.querySelectorAll('input[name="target_division_ids[]"]').forEach(cb => {
        cb.checked = this.checked;
        cb.disabled = this.checked;
    });
});
</script>
@endpush
@endsection