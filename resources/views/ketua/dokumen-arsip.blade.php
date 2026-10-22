@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Dokumen & Arsip</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Penyimpanan dokumen organisasi</p>
            </div>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                <i class="fas fa-upload"></i> Upload Dokumen
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(['Surat','Notulen','Proposal','LPJ'] as $folder)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition cursor-pointer">
                    <i class="fas fa-folder text-yellow-500 text-3xl"></i>
                    <p class="mt-3 font-medium text-gray-800 dark:text-gray-100">{{ $folder }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">0 file</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
            <i class="fas fa-file-alt text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">Pilih folder untuk melihat dokumen.</p>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')