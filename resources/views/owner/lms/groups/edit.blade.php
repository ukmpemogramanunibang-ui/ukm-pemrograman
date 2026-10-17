@extends('layouts.roles.owner')

@section('title', 'Edit Pengelompokan')

@section('content')
<div class="p-5 max-w-3xl mx-auto">

    <a href="{{ route('owner.lms.groups.show', $group->id) }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-violet-600 transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <h1 class="text-2xl font-black mt-3 text-gray-900 dark:text-white">Edit Pengelompokan</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui nama dan deskripsi pengelompokan.</p>

    @if($errors->any())
        <div class="mt-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40">
            <ul class="ml-5 list-disc text-xs text-rose-600 dark:text-rose-300 space-y-0.5">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('owner.lms.groups.update', $group->id) }}" enctype="multipart/form-data"
          class="mt-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700
                 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                Nama Pengelompokan <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $group->name) }}" required maxlength="120"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                          bg-gray-50 dark:bg-gray-900 text-sm
                          focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="4" maxlength="2000"
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700
                             bg-gray-50 dark:bg-gray-900 text-sm resize-y
                             focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-500">{{ old('description', $group->description) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
            <a href="{{ route('owner.lms.groups.show', $group->id) }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300
                      bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white
                           bg-gradient-to-r from-violet-500 to-fuchsia-500 hover:shadow-lg
                           hover:shadow-violet-500/30 transition-all">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection