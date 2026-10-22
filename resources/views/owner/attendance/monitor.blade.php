@extends('layouts.roles.owner')

@section('title', 'Live Monitor – ' . $session->title)

@section('content')
<div class="p-3 md:p-6 space-y-4" x-data="liveMonitor({{ $session->id }})" x-init="startPolling()">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-1">
                <a href="{{ route('owner.attendance.index') }}" class="hover:text-cyan-600">Kehadiran</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('owner.attendance.show', $session) }}" class="hover:text-cyan-600">{{ $session->title }}</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="font-medium">Live Monitor</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                Live Monitor
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $session->title }} · {{ $session->scheduled_start_at?->format('d M Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <button @click="togglePolling()" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm font-medium">
                <i class="fas" :class="polling ? 'fa-pause' : 'fa-play'"></i>
                <span x-text="polling ? 'Pause' : 'Resume'"></span>
            </button>
            <button @click="refresh()" class="px-3 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <template x-for="s in statCards" :key="s.key">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                <p class="text-[10px] uppercase text-gray-500 dark:text-gray-400" x-text="s.label"></p>
                <p class="text-2xl font-bold" :class="s.color" x-text="stats[s.key] ?? 0"></p>
            </div>
        </template>
    </div>

    {{-- DISTRIBUSI --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Mode</h3>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between"><span>Offline</span><span class="font-bold" x-text="stats.offline ?? 0"></span></div>
                <div class="flex justify-between"><span>Online</span><span class="font-bold" x-text="stats.online ?? 0"></span></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Metode</h3>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between"><span>NFC</span><span class="font-bold" x-text="stats.nfc ?? 0"></span></div>
                <div class="flex justify-between"><span>QR</span><span class="font-bold" x-text="stats.qr ?? 0"></span></div>
                <div class="flex justify-between"><span>Manual</span><span class="font-bold" x-text="stats.manual ?? 0"></span></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Progress</h3>
            <div class="text-sm">
                <div class="flex justify-between mb-1"><span>Kehadiran</span>
                    <span class="font-bold" x-text="(stats.hadir ?? 0) + '/' + (stats.target ?? 0)"></span>
                </div>
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 transition-all"
                         :style="`width: ${stats.target ? (stats.hadir / stats.target * 100) : 0}%`"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT CHECK-INS + BELUM HADIR --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Sudah Hadir --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-user-check text-green-500 mr-1"></i> Sudah Hadir
                </h3>
                <span class="text-xs text-gray-500" x-text="records.length + ' record'"></span>
            </div>
            <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="r in records" :key="r.id">
                    <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             x-text="r.name.charAt(0)"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate" x-text="r.name"></p>
                            <p class="text-[10px] text-gray-500" x-text="(r.role || '') + (r.group ? ' · ' + r.group : '')"></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold block"
                                  :class="'bg-' + colorFor(r.status) + '-100 text-' + colorFor(r.status) + '-700'"
                                  x-text="r.label"></span>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                <span x-text="r.time"></span> · <span x-text="r.method.toUpperCase()"></span>
                            </p>
                        </div>
                        <div class="text-right w-12 flex-shrink-0">
                            <span class="text-xs font-bold"
                                  :class="r.points > 0 ? 'text-green-600' : (r.points < 0 ? 'text-red-600' : 'text-gray-400')"
                                  x-text="(r.points > 0 ? '+' : '') + r.points"></span>
                        </div>
                    </div>
                </template>
                <div x-show="records.length === 0" class="p-8 text-center text-gray-400">
                    <i class="fas fa-user-clock text-3xl mb-2"></i>
                    <p class="text-sm">Belum ada yang absen.</p>
                </div>
            </div>
        </div>

        {{-- Belum Hadir --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-user-clock text-yellow-500 mr-1"></i> Belum Absen
                </h3>
                <span class="text-xs text-gray-500" x-text="belumHadir.length + ' orang'"></span>
            </div>
            <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="b in belumHadir" :key="b.name + b.nim">
                    <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 text-xs font-bold flex-shrink-0"
                             x-text="b.name.charAt(0)"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate" x-text="b.name"></p>
                            <p class="text-[10px] text-gray-500" x-text="(b.nim || '') + (b.group ? ' · ' + b.group : '')"></p>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                              :class="b.mode === 'online' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                              x-text="b.mode.toUpperCase()"></span>
                    </div>
                </template>
                <div x-show="belumHadir.length === 0" class="p-8 text-center text-green-500">
                    <i class="fas fa-check-circle text-3xl mb-2"></i>
                    <p class="text-sm font-medium">Semua sudah absen! 🎉</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function liveMonitor(sessionId) {
    return {
        sessionId,
        polling: true,
        stats: {},
        records: [],
        belumHadir: [],
        timer: null,
        statCards: [
            { key: 'hadir',     label: 'Hadir',     color: 'text-green-600' },
            { key: 'tepat',     label: 'Tepat',     color: 'text-emerald-600' },
            { key: 'terlambat', label: 'Telat',     color: 'text-yellow-600' },
            { key: 'izin',      label: 'Izin',      color: 'text-cyan-600' },
            { key: 'bolos',     label: 'Bolos',     color: 'text-red-600' },
            { key: 'belum',     label: 'Belum',     color: 'text-gray-600' },
            { key: 'pending',   label: 'Pending',   color: 'text-purple-600' },
        ],
        startPolling() {
            this.refresh();
            this.timer = setInterval(() => { if (this.polling) this.refresh(); }, 3000);
        },
        togglePolling() { this.polling = !this.polling; },
        async refresh() {
            try {
                const res = await fetch(`/owner/attendance/${this.sessionId}/monitor`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.stats = data.stats || {};
                this.records = data.records || [];
                this.belumHadir = data.belum_hadir || [];
            } catch (e) { console.error(e); }
        },
        colorFor(status) {
            const map = {
                'present':'green','late':'yellow','late_excused':'blue',
                'excused_absent':'cyan','absent':'red','absent_excused':'orange',
                'manual':'purple','historical':'indigo','pending':'gray','rejected':'red',
            };
            return map[status] || 'gray';
        }
    }
}
</script>
@endsection