@include('partials.roles.ketua.header')
@include('partials.roles.ketua.sidebar')

<main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
    <div class="p-6 space-y-6 max-w-3xl">

        <div class="flex items-center gap-3">
            <a href="{{ route('ketua.broadcast-pengumuman.index') }}" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detail Pengumuman</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pratinjau pengumuman</p>
            </div>
        </div>

        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $announcement->title ?? 'Judul' }}</h2>
            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span><i class="fas fa-user mr-1"></i>{{ $announcement->author->name ?? 'Ketua' }}</span>
                <span><i class="fas fa-clock mr-1"></i>{{ optional($announcement->created_at ?? null)->format('d M Y H:i') }}</span>
            </div>
            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                {!! nl2br(e($announcement->content ?? '-')) !!}
            </div>
        </article>

    </div>
</main>

@include('partials.roles.ketua.footer')