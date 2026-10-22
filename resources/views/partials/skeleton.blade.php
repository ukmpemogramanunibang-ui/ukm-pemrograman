{{-- 
 | Skeleton Loading Component
 | Pemakaian:
 |   @include('partials.skeleton', ['type' => 'dashboard'])
 |   @include('partials.skeleton', ['type' => 'table'])
 |   @include('partials.skeleton', ['type' => 'cards'])
--}}

@php
    $type = $type ?? 'dashboard';
@endphp

<div class="max-w-7xl mx-auto space-y-4 md:space-y-6 animate-pulse">

    {{-- Header --}}
    <div class="space-y-3">
        <div class="h-8 w-1/3 max-w-xs bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
        <div class="h-4 w-1/2 max-w-md bg-gray-200 dark:bg-gray-700 rounded"></div>
    </div>

    @if($type === 'dashboard')
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @for($i = 0; $i < 4; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700">
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded mb-4"></div>
                    <div class="h-8 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-3"></div>
                    <div class="h-3 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            @endfor
        </div>
        {{-- Blocks --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 space-y-4">
                <div class="h-6 w-40 bg-gray-200 dark:bg-gray-700 rounded"></div>
                @for($i = 0; $i < 6; $i++)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-3/4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-3 w-1/2 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 space-y-3">
                <div class="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                @for($i = 0; $i < 5; $i++)
                    <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                @endfor
            </div>
        </div>

    @elseif($type === 'table')
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
            @for($i = 0; $i < 8; $i++)
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-4">
                    <div class="h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full shrink-0"></div>
                    <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="w-20 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            @endfor
        </div>

    @elseif($type === 'cards')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @for($i = 0; $i < 9; $i++)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-5 w-3/4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-3 w-2/3 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            @endfor
        </div>
    @endif
</div>