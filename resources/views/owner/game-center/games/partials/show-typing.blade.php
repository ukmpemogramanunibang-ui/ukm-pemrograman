<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
    <div>
        <p class="text-slate-500 dark:text-slate-400">Mode</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ $settings['mode'] ?? 'N/A' }}</p>
    </div>
    <div>
        <p class="text-slate-500 dark:text-slate-400">Bahasa</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ $settings['language'] ?? 'N/A' }}</p>
    </div>
    <div>
        <p class="text-slate-500 dark:text-slate-400">Durasi (detik)</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ $settings['duration'] ?? 'N/A' }}</p>
    </div>
    <div>
        <p class="text-slate-500 dark:text-slate-400">Sumber Teks</p>
        <p class="font-bold text-slate-800 dark:text-white">{{ $settings['text_source'] ?? 'N/A' }}</p>
    </div>
</div>