{{-- resources/views/anggota/_coming-soon.blade.php --}}
@php
    $title       = $title       ?? 'Halaman';
    $icon        = $icon        ?? 'fa-tools';
    $tone        = $tone        ?? 'cyan';
    $description = $description ?? 'Halaman ini sedang dalam pengembangan.';
    $features    = $features    ?? [];
    $eta         = $eta         ?? null;
    $progress    = $progress    ?? 0;

    $toneMap = [
        'cyan'    => ['from' => 'from-cyan-500',   'to' => 'to-blue-600',    'ring' => 'ring-cyan-500/20',    'text' => 'text-cyan-600 dark:text-cyan-400',     'bg' => 'bg-cyan-500'],
        'amber'   => ['from' => 'from-amber-500',  'to' => 'to-orange-600',  'ring' => 'ring-amber-500/20',   'text' => 'text-amber-600 dark:text-amber-400',   'bg' => 'bg-amber-500'],
        'emerald' => ['from' => 'from-emerald-500','to' => 'to-teal-600',    'ring' => 'ring-emerald-500/20', 'text' => 'text-emerald-600 dark:text-emerald-400','bg' => 'bg-emerald-500'],
        'indigo'  => ['from' => 'from-indigo-500', 'to' => 'to-purple-600',  'ring' => 'ring-indigo-500/20',  'text' => 'text-indigo-600 dark:text-indigo-400', 'bg' => 'bg-indigo-500'],
        'blue'    => ['from' => 'from-blue-500',   'to' => 'to-indigo-600',  'ring' => 'ring-blue-500/20',    'text' => 'text-blue-600 dark:text-blue-400',     'bg' => 'bg-blue-500'],
        'sky'     => ['from' => 'from-sky-500',    'to' => 'to-cyan-600',    'ring' => 'ring-sky-500/20',     'text' => 'text-sky-600 dark:text-sky-400',       'bg' => 'bg-sky-500'],
        'orange'  => ['from' => 'from-orange-500', 'to' => 'to-red-600',     'ring' => 'ring-orange-500/20',  'text' => 'text-orange-600 dark:text-orange-400', 'bg' => 'bg-orange-500'],
        'yellow'  => ['from' => 'from-yellow-500', 'to' => 'to-amber-600',   'ring' => 'ring-yellow-500/20',  'text' => 'text-yellow-600 dark:text-yellow-400', 'bg' => 'bg-yellow-500'],
        'purple'  => ['from' => 'from-purple-500', 'to' => 'to-fuchsia-600', 'ring' => 'ring-purple-500/20',  'text' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-500'],
        'green'   => ['from' => 'from-green-500',  'to' => 'to-emerald-600', 'ring' => 'ring-green-500/20',   'text' => 'text-green-600 dark:text-green-400',   'bg' => 'bg-green-500'],
        'rose'    => ['from' => 'from-rose-500',   'to' => 'to-pink-600',    'ring' => 'ring-rose-500/20',    'text' => 'text-rose-600 dark:text-rose-400',     'bg' => 'bg-rose-500'],
    ];
    $c = $toneMap[$tone] ?? $toneMap['cyan'];
@endphp

<div class="p-4 md:p-6 lg:p-8 max-w-4xl mx-auto">

    <nav class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-5">
        <a href="{{ route('anggota.dashboard') }}" class="hover:text-cyan-600 transition">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <i class="fas fa-chevron-right text-[8px] opacity-50"></i>
        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $title }}</span>
    </nav>

    <div class="bg-white dark:bg-gray-800 rounded-3xl
                border border-gray-200 dark:border-gray-700
                overflow-hidden shadow-sm">

        <div class="h-2 bg-gradient-to-r {{ $c['from'] }} {{ $c['to'] }}"></div>

        <div class="p-8 md:p-12 text-center">

            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 rounded-3xl
                            bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                            animate-pulse opacity-20 blur-xl"></div>
                <div class="relative w-24 h-24 rounded-3xl
                            bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }}
                            flex items-center justify-center
                            shadow-2xl ring-4 {{ $c['ring'] }}">
                    <i class="fas {{ $icon }} text-white text-4xl"></i>
                </div>
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white">
                {{ $title }}
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 max-w-md mx-auto leading-relaxed">
                {{ $description }}
            </p>

            @if($progress > 0)
                <div class="mt-6 max-w-sm mx-auto">
                    <div class="flex items-center justify-between mb-1.5
                                text-[10px] font-bold uppercase tracking-wider">
                        <span class="text-gray-400">Progress</span>
                        <span class="{{ $c['text'] }}">{{ $progress }}%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r {{ $c['from'] }} {{ $c['to'] }}
                                    transition-all duration-1000"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif

            <div class="mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-full
                        bg-amber-50 dark:bg-amber-900/20
                        text-amber-700 dark:text-amber-300
                        border border-amber-200 dark:border-amber-700/40
                        text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <i class="fas fa-hourglass-half"></i>
                {{ $eta ?? 'Segera Hadir' }}
            </div>
        </div>

        @if(count($features) > 0)
            <div class="border-t border-gray-200 dark:border-gray-700
                        bg-gray-50/50 dark:bg-gray-800/50 p-6 md:p-8">
                <p class="text-[10px] font-black uppercase tracking-widest
                          text-gray-400 dark:text-gray-500 mb-4 text-center">
                    Fitur yang Akan Datang
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl mx-auto">
                    @foreach($features as $f)
                        <div class="flex items-start gap-3 p-3 rounded-xl
                                    bg-white dark:bg-gray-800
                                    border border-gray-200 dark:border-gray-700
                                    hover:shadow-md transition-all duration-200">
                            <span class="w-8 h-8 rounded-lg shrink-0 {{ $c['bg'] }}/10
                                         flex items-center justify-center">
                                <i class="fas {{ $f['icon'] ?? 'fa-check' }} {{ $c['text'] }} text-xs"></i>
                            </span>
                            <span class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $f['text'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-t border-gray-200 dark:border-gray-700 p-6 md:p-8
                    flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('anggota.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                      bg-gradient-to-r {{ $c['from'] }} {{ $c['to'] }}
                      hover:opacity-90 text-white text-sm font-bold
                      shadow-md transition">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <button type="button" disabled
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                           bg-gray-100 dark:bg-gray-700
                           text-gray-400 dark:text-gray-500
                           text-sm font-bold cursor-not-allowed">
                <i class="fas fa-bell"></i>
                Beri tahu saya
            </button>
        </div>
    </div>

    <p class="text-center text-[11px] text-gray-400 dark:text-gray-500 mt-6">
        <i class="fas fa-code-branch mr-1"></i>
        Halaman ini dikelola oleh Tim Pengembang UNICODE
    </p>
</div>