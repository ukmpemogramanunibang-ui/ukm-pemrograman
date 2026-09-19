<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach($settings as $key => $value)
        @if(is_array($value))
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 md:col-span-2">
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($value as $subKey => $subValue)
                        <span class="text-xs bg-white dark:bg-slate-700 px-2 py-1 rounded-md text-slate-700 dark:text-slate-300">
                            {{ ucfirst(str_replace('_', ' ', $subKey)) }}: {{ is_bool($subValue) ? ($subValue ? '✅' : '❌') : $subValue }}
                        </span>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4">
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                <p class="font-bold text-slate-800 dark:text-white">{{ is_bool($value) ? ($value ? '✅ Ya' : '❌ Tidak') : $value }}</p>
            </div>
        @endif
    @endforeach
</div>