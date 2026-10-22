@extends('layouts.roles.owner')

@section('title', 'Kumpulkan — ' . ($item->title ?? 'Tugas'))

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto space-y-5">

    {{-- HEADER --}}
    <div>
        <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 flex-wrap mb-2">
            <a href="{{ route('owner.lms.groups.show', $item->group_id) }}" class="hover:text-violet-600">
                <i class="fas fa-folder-open"></i> {{ $item->group->name ?? '-' }}
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('owner.lms.items.show', $item->id) }}" class="hover:text-violet-600">
                {{ $item->title }}
            </a>
            <i class="fas fa-chevron-right text-[8px] opacity-60"></i>
            <span class="font-bold text-gray-700 dark:text-gray-300">Kumpulkan</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">
            <i class="fas fa-paper-plane text-violet-500 mr-2"></i> Kumpulkan Tugas
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ $item->title }}
        </p>
    </div>

    {{-- DEADLINE WARNING --}}
    @if($item->deadline_at)
        @php $overdue = now()->gt($item->deadline_at); @endphp
        <div class="p-4 rounded-2xl {{ $overdue ? 'bg-rose-50 dark:bg-rose-900/20 border border-rose-200' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200' }}">
            <p class="text-xs font-black {{ $overdue ? 'text-rose-700 dark:text-rose-300' : 'text-amber-700 dark:text-amber-300' }}">
                <i class="fas fa-clock"></i>
                Deadline: {{ $item->deadline_at->isoFormat('dddd, D MMM Y · HH:mm') }} WIB
                @if($overdue) · <b>SUDAH LEWAT</b> @endif
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-700 dark:text-rose-300 text-sm">
            <i class="fas fa-times-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('owner.lms.items.submissions.store', $item->id) }}"
          enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Jawaban Teks
            </label>
            <textarea name="answer_text" rows="6" maxlength="8000"
                      placeholder="Tulis jawaban Anda..."
                      class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                             focus:outline-none focus:ring-2 focus:ring-violet-500/30 resize-y">{{ old('answer_text') }}</textarea>
            @error('answer_text') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Link Jawaban (Google Drive, GitHub, dll)
            </label>
            <input type="url" name="answer_link" value="{{ old('answer_link') }}"
                   placeholder="https://..."
                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                          focus:outline-none focus:ring-2 focus:ring-violet-500/30">
            @error('answer_link') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Lampiran (maks 3 file, 10 MB/file)
            </label>
            <input type="file" name="files[]" multiple
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.jpg,.jpeg,.png"
                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                          file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-violet-500 file:text-white file:font-bold file:text-xs">
            @error('files') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            @error('files.*') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-1.5">
                Catatan (opsional)
            </label>
            <textarea name="note" rows="2" maxlength="1000"
                      placeholder="Catatan tambahan untuk reviewer..."
                      class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm
                             focus:outline-none focus:ring-2 focus:ring-violet-500/30 resize-y">{{ old('note') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
            <a href="{{ route('owner.lms.items.show', $item->id) }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-violet-500 to-fuchsia-500
                           hover:shadow-lg hover:shadow-violet-500/30 transition-all">
                <i class="fas fa-paper-plane"></i> Kumpulkan
            </button>
        </div>
    </form>
</div>
@endsection