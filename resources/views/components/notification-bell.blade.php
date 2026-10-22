{{-- resources/views/components/notification-bell.blade.php --}}
<div
    x-data="notificationBell()"
    x-init="init()"
    @click.outside="open = false"
    class="relative"
>
    {{-- Bell Button --}}
    <button
        type="button"
        @click="toggleOpen()"
        class="relative p-2 rounded-full hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-violet-400"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-6 h-6 text-gray-700"
             :class="shake ? 'animate-bell-shake' : ''"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <span
            x-show="count > 0"
            x-transition
            class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 px-1 flex items-center justify-center text-[11px] font-bold text-white bg-red-500 rounded-full shadow ring-2 ring-white"
            :class="pulse ? 'animate-pulse' : ''"
            x-text="count > 99 ? '99+' : count"
        ></span>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute right-0 mt-2 w-[420px] max-w-[calc(100vw-1rem)] bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden z-50"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b bg-gradient-to-r from-violet-50 to-white">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                <span x-show="count > 0"
                      class="text-xs px-2 py-0.5 bg-violet-100 text-violet-700 rounded-full font-medium"
                      x-text="count + ' baru'"></span>
            </div>
            <button
                type="button"
                x-show="count > 0"
                @click="markAllRead()"
                class="text-xs text-violet-600 hover:text-violet-800 font-medium transition"
            >Tandai semua dibaca</button>
        </div>

        {{-- 🆕 Mini Tabs di Dropdown --}}
        <div class="flex border-b bg-white">
            <template x-for="t in tabs" :key="t.key">
                <button
                    type="button"
                    @click="switchTab(t.key)"
                    class="flex-1 py-2 text-xs font-semibold transition border-b-2"
                    :class="activeTab === t.key
                        ? 'text-violet-600 border-violet-500 bg-violet-50/50'
                        : 'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-50'"
                >
                    <span x-text="t.label"></span>
                </button>
            </template>
        </div>

        {{-- List --}}
        <div class="max-h-[400px] overflow-y-auto overscroll-contain">
            <template x-if="filteredItems.length === 0">
                <div class="p-8 text-center">
                    <div class="text-4xl mb-2" x-text="activeTab === 'starred' ? '⭐' : '🎉'"></div>
                    <p class="text-sm text-gray-500"
                       x-text="activeTab === 'starred' ? 'Belum ada notifikasi favorit' : 'Tidak ada notifikasi'"></p>
                </div>
            </template>

            <template x-for="item in filteredItems" :key="item.id">
                <div class="group relative flex gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0"
                     :class="!item.is_read ? 'bg-violet-50/40' : ''">

                    {{-- Body --}}
                    <a :href="item.action_url || '{{ route('owner.notifications.index') }}'"
                       @click="onItemClick(item, $event)"
                       class="flex gap-3 flex-1 min-w-0 cursor-pointer">
                        {{-- Emoji --}}
                        <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg"
                             :class="{
                                'bg-rose-100': item.priority === 'urgent',
                                'bg-amber-100': item.priority === 'high',
                                'bg-violet-100': !item.priority || !['urgent','high'].includes(item.priority)
                             }"
                             x-text="item.source_meta?.emoji || '🔔'">
                        </div>

                        {{-- Text --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm text-gray-800 truncate"
                                   :class="!item.is_read ? 'font-semibold' : 'font-medium'"
                                   x-text="item.title"></p>
                                <span x-show="!item.is_read"
                                      class="shrink-0 w-2 h-2 bg-violet-500 rounded-full mt-1.5"></span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2 mt-0.5"
                               x-text="item.message || '—'"></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] text-gray-400" x-text="item.time_ago"></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500"
                                      x-text="item.source_meta?.label || item.source"></span>
                                <span x-show="item.is_starred" class="text-[10px] text-amber-500">★</span>
                            </div>
                        </div>
                    </a>

                    {{-- 🆕 Quick Actions --}}
                    <div class="shrink-0 flex items-start gap-1 opacity-0 group-hover:opacity-100 transition">
                        {{-- Star / Favorite --}}
                        <button
                            type="button"
                            @click.stop="toggleStar(item)"
                            class="p-1.5 rounded-md hover:bg-amber-50 transition"
                            :title="item.is_starred ? 'Hapus dari Favorit' : 'Tambah ke Favorit'"
                        >
                            <svg class="w-4 h-4"
                                 :class="item.is_starred ? 'text-amber-400 fill-amber-400' : 'text-gray-400'"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </button>

                        {{-- Delete (hanya jika read) --}}
                        <button
                            type="button"
                            x-show="item.is_read"
                            @click.stop="deleteItem(item)"
                            class="p-1.5 rounded-md hover:bg-rose-50 transition"
                            title="Hapus notifikasi"
                        >
                            <svg class="w-4 h-4 text-gray-400 hover:text-rose-500"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- 🆕 Footer: Lihat Semua --}}
        <a href="{{ route('owner.notifications.index') }}"
           class="block text-center py-3 text-sm font-semibold text-violet-600 hover:bg-violet-50 transition border-t">
            Lihat semua notifikasi →
        </a>
    </div>

    {{-- Toast Container --}}
    <div id="notif-toast-container"
         class="fixed bottom-6 right-6 z-[100] flex flex-col gap-2 pointer-events-none"></div>

    <style>
        @keyframes bell-shake {
            0%, 100% { transform: rotate(0); }
            15% { transform: rotate(15deg); }
            30% { transform: rotate(-15deg); }
            45% { transform: rotate(10deg); }
            60% { transform: rotate(-10deg); }
            75% { transform: rotate(5deg); }
        }
        .animate-bell-shake { animation: bell-shake 0.8s ease-in-out; }
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
    function notificationBell() {
        return {
            open: false,
            count: {{ $unreadCount ?? 0 }},
            items: [],
            activeTab: 'all',
            tabs: [
                { key: 'all',     label: 'Semua' },
                { key: 'unread',  label: 'Belum' },
                { key: 'starred', label: '★ Favorit' },
            ],
            shake: false,
            pulse: false,
            lastId: localStorage.getItem('notif_last_id') || '',
            pollTimer: null,
            firstLoad: true,

            get filteredItems() {
                if (this.activeTab === 'unread')  return this.items.filter(i => !i.is_read);
                if (this.activeTab === 'starred') return this.items.filter(i => i.is_starred);
                return this.items;
            },

            init() {
                this.fetchPoll();
                this.pollTimer = setInterval(() => this.fetchPoll(), 10000);

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        clearInterval(this.pollTimer);
                        this.pollTimer = null;
                    } else if (!this.pollTimer) {
                        this.fetchPoll();
                        this.pollTimer = setInterval(() => this.fetchPoll(), 10000);
                    }
                });

                window.addEventListener('notif:read',    (e) => this.decrement(e.detail?.count ?? 1));
                window.addEventListener('notif:refresh', () => this.fetchPoll());
            },

            async fetchPoll() {
                try {
                    const res = await fetch('{{ route('owner.notifications.poll') }}?since_id=' + encodeURIComponent(this.lastId), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    const prev = this.count;
                    this.count = data.unread_count ?? 0;

                    if (!this.firstLoad && data.has_new && this.count > prev) {
                        this.onNewNotification(data.items?.[0]);
                    }

                    this.items = data.items || [];

                    if (data.latest_id) {
                        this.lastId = data.latest_id;
                        localStorage.setItem('notif_last_id', this.lastId);
                    }
                    this.firstLoad = false;
                } catch (e) {
                    console.warn('[Notification] Poll gagal:', e);
                }
            },

            onNewNotification(item) {
                if (!item) return;
                this.shake = true; this.pulse = true;
                setTimeout(() => this.shake = false, 900);
                setTimeout(() => this.pulse = false, 3000);
                this.showToast(item);
                this.playDing();
                this.syncTitle();
            },

            showToast(item) {
                const container = document.getElementById('notif-toast-container');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = 'pointer-events-auto bg-white rounded-xl shadow-2xl ring-1 ring-black/5 w-80 p-3 flex gap-3 transition-all duration-300 opacity-100 translate-x-0 cursor-pointer hover:bg-gray-50';
                toast.innerHTML = `
                    <div class="shrink-0 w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center text-lg">
                        ${item.source_meta?.emoji || '🔔'}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">${this.escape(item.title)}</p>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-0.5">${this.escape(item.message || '')}</p>
                        <p class="text-[10px] text-gray-400 mt-1">${this.escape(item.time_ago || '')}</p>
                    </div>
                `;
                toast.addEventListener('click', () => {
                    window.location.href = item.action_url || '{{ route('owner.notifications.index') }}';
                });
                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-4');
                    setTimeout(() => toast.remove(), 300);
                }, 6000);
            },

            playDing() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const o = ctx.createOscillator();
                    const g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.type = 'sine';
                    o.frequency.setValueAtTime(880, ctx.currentTime);
                    o.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.15);
                    g.gain.setValueAtTime(0.0001, ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(0.15, ctx.currentTime + 0.02);
                    g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
                    o.start(); o.stop(ctx.currentTime + 0.4);
                } catch (e) {}
            },

            async toggleOpen() {
                this.open = !this.open;
                if (this.open) {
                    await this.fetchPoll();
                } else {
                    // Tandai seen untuk semua item yang terlihat
                    this.items.forEach(i => { if (!i.is_seen) this.markSeen(i.id); });
                }
            },

            switchTab(key) {
                this.activeTab = key;
                this.fetchPoll();
            },

            async markSeen(id) {
                try {
                    await fetch(`/owner/notifications/${id}/seen`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                } catch (e) {}
            },

            async onItemClick(item, evt) {
                if (!item.is_read) {
                    item.is_read = true;
                    this.decrement(1);
                    fetch(`/owner/notifications/${item.id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                        keepalive: true,
                    }).catch(() => {});
                }
            },

            async toggleStar(item) {
                // Optimistic UI
                const newVal = !item.is_starred;
                item.is_starred = newVal;

                try {
                    const res = await fetch(`/owner/notifications/${item.id}/toggle-star`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) throw new Error();
                    // Update dari server (source of truth)
                    if (typeof data.is_starred === 'boolean') item.is_starred = data.is_starred;
                } catch (e) {
                    item.is_starred = !newVal; // rollback
                }
            },

            async deleteItem(item) {
                // Guard di FE juga (meskipun server tetap validasi)
                if (!item.is_read) {
                    this.flashToast('Buka notifikasi dulu sebelum menghapus.', 'warn');
                    return;
                }

                if (!confirm('Hapus notifikasi ini?')) return;

                try {
                    const res = await fetch(`/owner/notifications/${item.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) throw new Error(data.message || 'Gagal');

                    // Hapus dari state lokal
                    this.items = this.items.filter(i => i.id !== item.id);
                    if (!item.is_read) this.decrement(1);
                    this.flashToast('Notifikasi dihapus.', 'success');
                } catch (e) {
                    this.flashToast(e.message || 'Gagal menghapus.', 'error');
                }
            },

            async markAllRead() {
                try {
                    const res = await fetch('{{ route('owner.notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                    if (res.ok) {
                        this.items = this.items.map(i => ({ ...i, is_read: true }));
                        this.count = 0;
                        this.syncTitle();
                    }
                } catch (e) {}
            },

            decrement(n = 1) {
                this.count = Math.max(0, this.count - n);
                this.syncTitle();
            },

            syncTitle() {
                const base = document.title.replace(/^\(\d+\)\s*/, '');
                document.title = this.count > 0 ? `(${this.count}) ${base}` : base;
            },

            flashToast(msg, type = 'info') {
                const colors = {
                    success: 'bg-emerald-500',
                    warn: 'bg-amber-500',
                    error: 'bg-rose-500',
                    info: 'bg-slate-700',
                };
                const container = document.getElementById('notif-toast-container');
                if (!container) return;
                const t = document.createElement('div');
                t.className = `pointer-events-auto ${colors[type]} text-white text-sm rounded-xl shadow-2xl px-4 py-2.5 w-80 transition-all duration-300 opacity-0 translate-x-4`;
                t.textContent = msg;
                container.appendChild(t);
                requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-4'));
                setTimeout(() => {
                    t.classList.add('opacity-0', 'translate-x-4');
                    setTimeout(() => t.remove(), 300);
                }, 3200);
            },

            escape(str) {
                return String(str ?? '').replace(/[&<>"']/g, c => ({
                    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
                }[c]));
            },
        }
    }
    </script>
</div>