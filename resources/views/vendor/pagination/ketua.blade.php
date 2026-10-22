@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination"
     class="flex flex-col lg:flex-row items-center justify-between gap-4
            bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800
            shadow-sm px-4 sm:px-6 py-4">

    {{-- ═══ LEFT: Info ═══ --}}
    <div class="flex items-center gap-2 text-sm order-2 lg:order-1">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500
                    flex items-center justify-center shadow-md">
            <i class="fas fa-list text-white text-[10px]"></i>
        </div>
        <p class="text-gray-500 dark:text-gray-400">
            Menampilkan
            <span class="font-bold text-gray-900 dark:text-white tabular-nums">{{ $paginator->firstItem() ?? 0 }}</span>
            –
            <span class="font-bold text-gray-900 dark:text-white tabular-nums">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500 tabular-nums">
                {{ $paginator->total() }}
            </span>
            data
        </p>
    </div>

    {{-- ═══ RIGHT: Pager ═══ --}}
    <div class="flex items-center gap-1.5 order-1 lg:order-2 flex-wrap justify-center">

        {{-- ─── PREVIOUS ─── --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                         bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600
                         cursor-not-allowed border border-gray-100 dark:border-gray-800">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               aria-label="Halaman Sebelumnya"
               class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                      bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                      text-gray-600 dark:text-gray-400
                      hover:bg-purple-50 dark:hover:bg-purple-900/20
                      hover:text-purple-600 dark:hover:text-purple-400
                      hover:border-purple-300 dark:hover:border-purple-700
                      hover:shadow-md hover:-translate-y-0.5
                      transition-all duration-200">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </a>
        @endif

        {{-- ─── PAGE NUMBERS ─── --}}
        @foreach ($elements as $element)

            {{-- "..." separator --}}
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10
                             text-gray-400 dark:text-gray-600 font-bold text-xs select-none">
                    <i class="fas fa-ellipsis-h"></i>
                </span>
            @endif

            {{-- Page buttons --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        {{-- ACTIVE PAGE --}}
                        <span aria-current="page"
                              class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                                     bg-gradient-to-br from-purple-600 to-pink-600
                                     text-white font-black text-sm tabular-nums
                                     shadow-lg shadow-purple-500/40
                                     ring-2 ring-purple-500/20 scale-105
                                     transition-all duration-200">
                            {{ $page }}
                        </span>
                    @else
                        {{-- INACTIVE PAGE --}}
                        <a href="{{ $url }}"
                           aria-label="Ke halaman {{ $page }}"
                           class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                                  bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                                  text-gray-700 dark:text-gray-300 font-bold text-sm tabular-nums
                                  hover:bg-gradient-to-br hover:from-purple-50 hover:to-pink-50
                                  dark:hover:from-purple-900/30 dark:hover:to-pink-900/20
                                  hover:text-purple-600 dark:hover:text-purple-400
                                  hover:border-purple-300 dark:hover:border-purple-700
                                  hover:shadow-md hover:-translate-y-0.5
                                  transition-all duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- ─── NEXT ─── --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               aria-label="Halaman Berikutnya"
               class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                      bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800
                      text-gray-600 dark:text-gray-400
                      hover:bg-purple-50 dark:hover:bg-purple-900/20
                      hover:text-purple-600 dark:hover:text-purple-400
                      hover:border-purple-300 dark:hover:border-purple-700
                      hover:shadow-md hover:-translate-y-0.5
                      transition-all duration-200">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl
                         bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600
                         cursor-not-allowed border border-gray-100 dark:border-gray-800">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </div>
</nav>
@endif