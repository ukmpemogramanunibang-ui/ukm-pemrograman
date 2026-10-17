@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6 max-w-3xl">

        <div class="flex items-center gap-3">
            <a href="{{ route('ketua.broadcast-pengumuman.index') }}" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Buat Pengumuman</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tulis pengumuman baru untuk organisasi</p>
            </div>
        </div>

        @if($errors->any())
            <div class="px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ketua.broadcast-pengumuman.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Contoh: Rapat Koordinasi Bulanan">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Audiens</label>
                <select name="audience" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="all">Semua</option>
                    <option value="pengurus">Pengurus</option>
                    <option value="anggota">Anggota</option>
                    <option value="divisi">Divisi Tertentu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Isi Pengumuman</label>
                <textarea name="content" rows="8" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Tulis isi pengumuman...">{{ old('content') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('ketua.broadcast-pengumuman.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                    <i class="fas fa-paper-plane mr-1"></i> Publikasikan
                </button>
            </div>
        </form>

    </div>
</main>

@include('partials.roles.ketua.footer')