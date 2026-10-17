@php
    $cards       = $dashboardData['cards']        ?? collect();
    $hiddenCards = $dashboardData['hidden_cards'] ?? collect();
    $isOwner     = $dashboardData['is_owner']     ?? false;

    $urlBulk    = \Illuminate\Support\Facades\Route::has('dashboard.items.bulk')
        ? route('dashboard.items.bulk') : null;
    $urlReset   = \Illuminate\Support\Facades\Route::has('dashboard.items.reset')
        ? route('dashboard.items.reset') : null;
    $urlSetTpl  = \Illuminate\Support\Facades\Route::has('dashboard.items.set')
        ? route('dashboard.items.set', ['item' => '__ID__']) : null;
    $urlBuilder = $isOwner && \Illuminate\Support\Facades\Route::has('owner.settings.general')
        ? route('owner.settings.general') : null;
@endphp

@if ($cards->count() > 0 || $hiddenCards->count() > 0)
<section id="dynamic-dashboard-folders"
         class="space-y-4"
         x-data="folderShortcutBuilder({
             storageKey: 'hidden_dashboard_cards_{{ auth()->id() }}',
             urlBulk:   @js($urlBulk),
             urlReset:  @js($urlReset),
             urlSetTpl: @js($urlSetTpl),
             cardIds:   @js($cards->pluck('id')->values()),
             csrf:      @js(csrf_token()),
         })">

    {{-- ============ HEADER ============ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-th-large text-cyan-500"></i>
                Akses Cepat
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $cards->count() }} folder aktif
                @if ($hiddenCards->count() > 0)
                    · <button type="button" @click="showHiddenPanel = !showHiddenPanel"
                              class="text-amber-500 hover:underline font-semibold">
                        {{ $hiddenCards->count() }} disembunyikan
                    </button>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if ($urlBuilder)
                <a href="{{ $urlBuilder }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                          bg-gray-100 dark:bg-gray-800
                          text-gray-700 dark:text-gray-200 text-xs font-semibold
                          hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-cog"></i> Kelola
                </a>
            @endif

            @if ($hiddenCards->count() > 0)
                <button type="button" @click="resetAll()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                               bg-cyan-50 dark:bg-cyan-900/20
                               text-cyan-700 dark:text-cyan-300 text-xs font-semibold
                               hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition">
                    <i class="fas fa-eye"></i> Tampilkan Semua
                </button>
            @endif
        </div>
    </div>

    {{-- ============ MOBILE SCROLL / DESKTOP GRID ============ --}}
    <div class="-mx-4 px-4 overflow-x-auto lg:mx-0 lg:px-0 lg:overflow-visible snap-x snap-mandatory">
        <div class="flex lg:grid lg:grid-cols-2 gap-4 lg:gap-5 min-w-full lg:min-w-0 pb-2">

            @forelse ($cards as $card)
                @php
                    $shortcuts = $card->shortcuts ?? collect();
                    $count     = $shortcuts->count();
                    $rows      = max(1, (int) ceil($count / 4));

                    $bodyMinH = match(true) {
                        $count === 0  => 'min-h-[120px]',
                        $rows === 1   => 'min-h-[155px]',
                        $rows === 2   => 'min-h-[255px]',
                        $rows === 3   => 'min-h-[355px]',
                        $rows === 4   => 'min-h-[455px]',
                        default       => 'min-h-[555px]',
                    };
                    $iconSize = $count <= 2 ? 'w-20 h-20' : 'w-16 h-16';
                    $iconText = $count <= 2 ? 'text-3xl'   : 'text-2xl';
                    $mobileW  = $count <= 2 ? 'w-[65vw] sm:w-[48vw]' : 'w-[88vw] sm:w-[72vw]';
                @endphp

                <div x-show="isVisible({{ $card->id }})"
                     x-transition
                     class="snap-center shrink-0 {{ $mobileW }} lg:w-auto
                            relative overflow-hidden rounded-2xl shadow-lg
                            border border-gray-200 dark:border-gray-700
                            bg-white dark:bg-gray-800
                            hover:shadow-xl hover:border-cyan-500/30
                            transition-all duration-300 flex flex-col">

                    {{-- FOLDER HEADER --}}
                    <div class="relative px-4 py-3.5 {{ $card->text_color }}"
                         style="{{ $card->background_style }}">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl {{ $card->icon_bg }} backdrop-blur
                                        flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $card->icon ?: 'fa-folder' }} text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-base truncate drop-shadow-sm">
                                    {{ $card->name }}
                                </h3>
                                <p class="text-[11px] {{ $card->subtext_color }}">
                                    <i class="fas fa-link mr-1"></i>
                                    {{ $count }} shortcut
                                </p>
                            </div>

                            {{-- TOGGLE MENU --}}
                            <div class="relative shrink-0" x-data="{ menuOpen: false }">
                                <button type="button"
                                        @click.stop="menuOpen = !menuOpen"
                                        class="w-8 h-8 rounded-full {{ $card->icon_bg }} backdrop-blur
                                               flex items-center justify-center
                                               hover:scale-110 active:scale-95 transition-all"
                                        title="Opsi card">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>

                                <div x-show="menuOpen"
                                     x-transition.opacity
                                     @click.outside="menuOpen = false"
                                     class="absolute right-0 top-10 z-50 w-48 rounded-xl
                                            bg-white dark:bg-gray-800
                                            border border-gray-200 dark:border-gray-700
                                            shadow-2xl overflow-hidden"
                                     style="display: none;">
                                    <button type="button"
                                            @click.stop="hideCard({{ $card->id }}); menuOpen = false"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs font-semibold
                                                   text-red-600 dark:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">
                                        <i class="fas fa-eye-slash w-4"></i>
                                        Sembunyikan Card
                                    </button>
                                    <button type="button"
                                            @click.stop="hideAllExcept({{ $card->id }}); menuOpen = false"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs font-semibold
                                                   text-amber-600 dark:text-amber-400
                                                   hover:bg-amber-50 dark:hover:bg-amber-900/20 transition text-left
                                                   border-t border-gray-100 dark:border-gray-700">
                                        <i class="fas fa-filter w-4"></i>
                                        Hanya Tampilkan Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SHORTCUT GRID --}}
                    <div class="flex-1 px-4 py-5 bg-gradient-to-b from-gray-50 to-white
                                dark:from-gray-900/40 dark:to-gray-800 {{ $bodyMinH }}">
                        @if ($count > 0)
                            <div class="grid grid-cols-4 gap-x-3 gap-y-4">
                                @foreach ($shortcuts as $sc)
                                    <a href="{{ $sc->url }}"
                                       @if ($sc->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                       title="{{ $sc->name }}"
                                       class="flex flex-col items-start gap-2 group/sc">

                                        <div class="relative {{ $iconSize }} aspect-square rounded-2xl overflow-hidden
                                                    bg-white dark:bg-gray-800
                                                    border border-gray-200 dark:border-gray-700
                                                    flex items-center justify-center shadow-sm
                                                    group-hover/sc:shadow-lg
                                                    group-hover/sc:border-cyan-400
                                                    dark:group-hover/sc:border-cyan-500
                                                    group-hover/sc:scale-105
                                                    transition-all duration-200">
                                            @if ($sc->background_image)
                                                <img src="{{ asset('storage/' . $sc->background_image) }}"
                                                     alt="{{ $sc->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <i class="fas {{ $sc->icon ?: 'fa-link' }}
                                                          {{ $iconText }}
                                                          text-cyan-600 dark:text-cyan-400"></i>
                                            @endif

                                            @if (!$sc->is_active)
                                                <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full
                                                             bg-red-500 border-2 border-white dark:border-gray-800"></span>
                                            @endif
                                        </div>

                                        <span class="text-[11px] font-semibold
                                                     text-gray-700 dark:text-gray-300
                                                     text-left w-full line-clamp-2 leading-tight
                                                     group-hover/sc:text-cyan-600
                                                     dark:group-hover/sc:text-cyan-400
                                                     transition-colors">
                                            {{ $sc->name }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center justify-center h-full py-6">
                                <div class="inline-flex items-center gap-1.5 text-[11px]
                                            text-gray-400 dark:text-gray-500 italic">
                                    <i class="fas fa-inbox text-sm"></i>
                                    Belum ada shortcut
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Empty state (hanya muncul kalau hidden cards kosong juga) --}}
                <div class="w-full col-span-full">
                    <div class="rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700
                                p-8 text-center">
                        <i class="fas fa-folder-open text-3xl text-gray-400 mb-3"></i>
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                            Belum ada folder akses cepat untuk role Anda.
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Hubungi admin untuk menambahkan shortcut.
                        </p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>

    {{-- HIDDEN CARDS PANEL --}}
    @if ($hiddenCards->count() > 0)
        <div x-show="showHiddenPanel"
             x-transition
             class="rounded-2xl border-2 border-amber-300 dark:border-amber-800
                    bg-amber-50 dark:bg-amber-900/20 p-4"
             style="display: none;">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div>
                    <h3 class="font-bold text-amber-900 dark:text-amber-200 text-sm">
                        <i class="fas fa-eye-slash mr-1"></i>
                        {{ $hiddenCards->count() }} Card Disembunyikan
                    </h3>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5">
                        Klik "Tampilkan" untuk mengembalikan.
                    </p>
                </div>
                <button type="button" @click="showHiddenPanel = false"
                        class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400
                               flex items-center justify-center hover:bg-amber-500/30">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach ($hiddenCards as $hidden)
                    <div class="flex items-center gap-2 p-2.5 rounded-lg
                                bg-white dark:bg-gray-800
                                border border-amber-300 dark:border-amber-700/60">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400
                                    flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $hidden->icon ?: 'fa-folder' }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">
                                {{ $hidden->name }}
                            </p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">
                                {{ $hidden->shortcuts->count() }} shortcut
                            </p>
                        </div>
                        <button type="button"
                                @click="showCard({{ $hidden->id }})"
                                class="w-7 h-7 rounded-lg bg-cyan-500/15 text-cyan-600 dark:text-cyan-400
                                       hover:bg-cyan-500/25 flex items-center justify-center">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</section>

@push('scripts')
<script>
function folderShortcutBuilder(cfg = {}) {
    return {
        storageKey:      cfg.storageKey || 'hidden_dashboard_cards',
        urlBulk:         cfg.urlBulk,
        urlReset:        cfg.urlReset,
        urlSetTpl:       cfg.urlSetTpl,
        allCardIds:      cfg.cardIds || [],
        csrf:            cfg.csrf || '',

        hidden: JSON.parse(localStorage.getItem(cfg.storageKey) || '[]'),
        showHiddenPanel: false,

        isVisible(id) { return ! this.hidden.includes(id); },

        save() { localStorage.setItem(this.storageKey, JSON.stringify(this.hidden)); },

        hideCard(id) {
            if (! this.hidden.includes(id)) {
                this.hidden.push(id);
                this.save();
                this.notifyServer(id, false);
                this.toast('Card disembunyikan', 'info');
            }
        },

        showCard(id) {
            this.hidden = this.hidden.filter(h => h !== id);
            this.save();
            this.notifyServer(id, true);
            this.toast('Card ditampilkan', 'success');
        },

        hideAllExcept(keepId) {
            this.hidden = this.allCardIds.filter(id => id !== keepId);
            this.save();

            if (this.urlBulk) {
                fetch(this.urlBulk, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ item_ids: this.hidden, is_visible: false }),
                }).catch(() => {});
            }

            this.notifyServer(keepId, true);
            this.toast('Hanya menampilkan 1 card', 'success');
        },

        resetAll() {
            this.hidden = [];
            this.save();

            if (this.urlReset) {
                fetch(this.urlReset, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json',
                    },
                }).catch(() => {});
            }

            this.toast('Semua card ditampilkan', 'success');
        },

        notifyServer(id, isVisible) {
            if (! this.urlSetTpl) return;
            const url = this.urlSetTpl.replace('__ID__', id);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ is_visible: isVisible }),
            }).catch(() => {});
        },

        toast(message, type = 'info') {
            const colors = { success: 'bg-green-500', info: 'bg-blue-500', error: 'bg-red-500' };
            const el = document.createElement('div');
            el.className = `fixed bottom-6 right-6 z-[9999] ${colors[type] || 'bg-gray-800'}
                            text-white px-4 py-2.5 rounded-xl shadow-2xl text-sm font-semibold
                            transform translate-y-4 opacity-0 transition-all duration-300`;
            el.textContent = message;
            document.body.appendChild(el);
            requestAnimationFrame(() => el.classList.remove('translate-y-4', 'opacity-0'));
            setTimeout(() => {
                el.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => el.remove(), 300);
            }, 2200);
        },
    };
}
</script>
@endpush
@endif