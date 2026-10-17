@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">SOP Organisasi</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Standar Operasional Prosedur UNICODE</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-3">
                @forelse($sops ?? [] as $i => $sop)
                    <details class="group border border-gray-200 dark:border-gray-700 rounded-lg">
                        <summary class="cursor-pointer px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/40 rounded-lg">
                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ $i+1 }}. {{ $sop->title }}</span>
                            <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                        </summary>
                        <div class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-300">
                            {!! nl2br(e($sop->content)) !!}
                        </div>
                    </details>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-10">
                        <i class="fas fa-clipboard-list text-4xl block mb-3 opacity-40"></i>
                        Belum ada SOP yang tersedia.
                    </p>
                @endforelse
            </div>
        </div>

    </div>
</main>

@include('partials.roles.ketua.footer')