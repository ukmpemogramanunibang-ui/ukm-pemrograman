@extends('layouts.roles.owner')

@section('content')
<div class="p-3 md:p-5 w-full"
     x-data="attendanceCalendar(@js($meta['view']), @js($meta['cursor']))"
     x-init="init()">

    {{-- ============================================================
         HEADER LUXURY — Glass + gradient accent
    ============================================================= --}}
    <div class="relative overflow-hidden rounded-2xl mb-5 p-5 md:p-6
                bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-900
                shadow-2xl ring-1 ring-white/10">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
             style="background:
                radial-gradient(600px 200px at 10% 0%, rgba(34,211,238,.35), transparent 60%),
                radial-gradient(600px 200px at 90% 100%, rgba(168,85,247,.30), transparent 60%);"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight
                           bg-gradient-to-r from-white via-cyan-200 to-teal-200 bg-clip-text text-transparent">
                    <i class="fas fa-calendar-alt text-cyan-400 mr-2"></i> Kalender Absensi
                </h1>
                <p class="text-sm text-cyan-100/80 mt-1">
                    Control tower jadwal & sesi absensi UNICODE — full spectrum.
                </p>
                <div class="mt-2 flex flex-wrap gap-2 text-[11px] text-cyan-100/70">
                    <span class="px-2 py-0.5 rounded-full bg-white/10 ring-1 ring-white/20">
                        {{ $meta['label'] }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-white/10 ring-1 ring-white/20">
                        {{ $stats['total'] }} sesi
                    </span>
                    @if($conflicts['total'] > 0)
                        <span class="px-2 py-0.5 rounded-full bg-red-500/30 ring-1 ring-red-400/50 text-red-100">
                            ⚠ {{ $conflicts['total'] }} konflik
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 ring-1 ring-emerald-400/40 text-emerald-100">
                            ✓ Tanpa konflik
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('owner.attendance.create') }}"
                   class="inline-flex items-center px-4 py-2.5 rounded-xl font-semibold text-white shadow-lg
                          bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500
                          hover:shadow-cyan-500/40 hover:scale-[1.02] transition">
                    <i class="fas fa-plus-circle mr-2"></i> Buat Absensi
                </a>
                <a href="{{ route('owner.attendance.index') }}"
                   class="inline-flex items-center px-4 py-2.5 rounded-xl font-medium
                          bg-white/10 backdrop-blur ring-1 ring-white/20 text-white
                          hover:bg-white/20 transition">
                    <i class="fas fa-list mr-2"></i> Daftar
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         WARNING / READINESS CENTER
    ============================================================= --}}
    @if(!empty($readiness) || ($conflicts['total'] ?? 0) > 0)
        <div class="mb-5 rounded-2xl overflow-hidden ring-1 ring-amber-300/50
                    bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50
                    dark:from-amber-950/40 dark:via-orange-950/30 dark:to-amber-950/40
                    shadow-lg">
            <div class="flex items-start gap-3 p-4">
                <div class="shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500
                            grid place-items-center shadow-lg ring-1 ring-white/40">
                    <i class="fas fa-triangle-exclamation text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-amber-900 dark:text-amber-200">
                        Perlu Perhatian Operator
                    </div>
                    <ul class="text-sm text-amber-800 dark:text-amber-200/90 mt-1 grid md:grid-cols-2 gap-x-6 gap-y-0.5">
                        @if(count($conflicts['rooms']))
                            <li>• {{ count($conflicts['rooms']) }} konflik ruangan</li>
                        @endif
                        @if(count($conflicts['officers']))
                            <li>• {{ count($conflicts['officers']) }} konflik pengurus</li>
                        @endif
                        @foreach(array_slice($readiness, 0, 4) as $r)
                            <li>• <span class="font-semibold">{{ $r['title'] }}</span>
                                <span class="opacity-80">— {{ $r['issue'] }}</span></li>
                        @endforeach
                        @if(count($readiness) > 4)
                            <li class="text-amber-600 font-semibold">• +{{ count($readiness) - 4 }} masalah lain</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         STATISTIK — Luxury tiles
    ============================================================= --}}
    @php
        $statCards = [
            ['label'=>'Total',       'val'=>$stats['total'],    'icon'=>'fa-layer-group',    'g'=>'from-slate-500 to-slate-700'],
            ['label'=>'Akan Datang', 'val'=>$stats['upcoming'], 'icon'=>'fa-clock',          'g'=>'from-blue-500 to-indigo-600'],
            ['label'=>'Berlangsung', 'val'=>$stats['ongoing'],  'icon'=>'fa-play-circle',    'g'=>'from-emerald-400 to-teal-600'],
            ['label'=>'Ditunda',     'val'=>$stats['paused'],   'icon'=>'fa-pause-circle',   'g'=>'from-amber-400 to-orange-600'],
            ['label'=>'Selesai',     'val'=>$stats['closed'],   'icon'=>'fa-check-circle',   'g'=>'from-zinc-500 to-slate-700'],
            ['label'=>'Kadaluarsa',  'val'=>$stats['expired'],  'icon'=>'fa-hourglass-end',  'g'=>'from-red-500 to-rose-600'],
            ['label'=>'Dibatalkan',  'val'=>$stats['cancelled'],'icon'=>'fa-ban',            'g'=>'from-rose-600 to-red-800'],
            ['label'=>'Konflik',     'val'=>$conflicts['total'],'icon'=>'fa-bolt',           'g'=>'from-fuchsia-500 to-purple-700'],
        ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-5">
        @foreach($statCards as $c)
            <div class="group relative overflow-hidden rounded-2xl p-3.5
                        bg-white dark:bg-slate-800/80 shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        hover:-translate-y-0.5 hover:shadow-xl transition">
                <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full opacity-20 blur-2xl
                            bg-gradient-to-br {{ $c['g'] }}"></div>
                <div class="relative flex items-center justify-between">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        {{ $c['label'] }}
                    </div>
                    <div class="w-7 h-7 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br {{ $c['g'] }}">
                        <i class="fas {{ $c['icon'] }} text-[11px]"></i>
                    </div>
                </div>
                <div class="relative mt-2 text-2xl font-extrabold text-slate-800 dark:text-white">
                    {{ $c['val'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================================================
         FILTER BAR
    ============================================================= --}}
    <form method="GET"
          class="mb-5 rounded-2xl bg-white dark:bg-slate-800/70 shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5 p-3.5">
        <input type="hidden" name="view" :value="view">
        <input type="hidden" name="date" :value="cursor">

        <div class="flex flex-wrap gap-2 items-center">
            <div class="relative flex-1 min-w-[240px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ $filters['q'] }}"
                       placeholder="Cari kegiatan, kelompok, pengurus…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl text-sm
                              bg-slate-50 dark:bg-slate-900/60
                              border border-slate-200 dark:border-white/10
                              text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-cyan-400/60 focus:border-cyan-400 outline-none">
            </div>

            <select name="status[]" multiple size="1"
                    class="min-w-[160px] px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60
                           border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white">
                @foreach($meta['status_labels'] as $k => $v)
                    <option value="{{ $k }}" @selected(in_array($k, $filters['status']))>{{ $v }}</option>
                @endforeach
            </select>

            <select name="mode[]" multiple size="1"
                    class="min-w-[130px] px-3 py-2.5 rounded-xl text-sm
                           bg-slate-50 dark:bg-slate-900/60
                           border border-slate-200 dark:border-white/10
                           text-slate-800 dark:text-white">
                <option value="online">🌐 Online</option>
                <option value="offline">🏢 Offline</option>
                <option value="hybrid">🔄 Hybrid</option>
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                   class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white">
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                   class="px-3 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-900/60
                          border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white">

            <button type="submit"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-lg
                           bg-gradient-to-r from-cyan-500 to-teal-600 hover:scale-[1.02] transition">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
            <a href="{{ route('owner.attendance.calendar') }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium
                      border border-slate-300 dark:border-white/10 text-slate-700 dark:text-slate-300
                      hover:bg-slate-100 dark:hover:bg-white/5">
                Reset
            </a>
        </div>
    </form>

    {{-- ============================================================
         VIEW SWITCHER + NAVIGASI
    ============================================================= --}}
    <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3
                rounded-2xl bg-white dark:bg-slate-800/70 shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5 p-2.5">

        <div class="inline-flex p-1 rounded-xl bg-slate-100 dark:bg-slate-900/60">
            @foreach(['month'=>['Bulan','fa-calendar'], 'week'=>['Minggu','fa-calendar-week'],
                      'day'=>['Hari','fa-calendar-day'], 'agenda'=>['Agenda','fa-list-ul']] as $k=>$arr)
                <a href="{{ route('owner.attendance.calendar', array_merge(request()->query(), ['view'=>$k])) }}"
                   class="px-3.5 py-1.5 text-sm font-medium rounded-lg inline-flex items-center gap-1.5 transition
                          {{ $meta['view'] === $k
                             ? 'bg-gradient-to-r from-cyan-500 to-teal-600 text-white shadow'
                             : 'text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-white/10' }}">
                    <i class="fas {{ $arr[1] }} text-xs"></i> {{ $arr[0] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('owner.attendance.calendar', array_merge(request()->query(), $meta['prev'])) }}"
               class="w-9 h-9 grid place-items-center rounded-xl border border-slate-200 dark:border-white/10
                      text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">
                <i class="fas fa-chevron-left text-sm"></i>
            </a>
            <div class="text-center px-3 min-w-[220px]">
                <div class="text-base font-bold text-slate-800 dark:text-white">{{ $meta['label'] }}</div>
            </div>
            <a href="{{ route('owner.attendance.calendar', array_merge(request()->query(), $meta['next'])) }}"
               class="w-9 h-9 grid place-items-center rounded-xl border border-slate-200 dark:border-white/10
                      text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
            <a href="{{ route('owner.attendance.calendar', array_merge(request()->query(), ['date'=>$meta['today']['date'], 'view'=>$meta['view']])) }}"
               class="ml-1 px-3.5 py-2 text-sm font-semibold rounded-xl
                      bg-gradient-to-r from-cyan-500 to-teal-600 text-white shadow hover:scale-[1.02] transition">
                <i class="fas fa-crosshairs mr-1"></i> Hari Ini
            </a>
        </div>
    </div>

    {{-- ============================================================
         AGENDA HARI INI
    ============================================================= --}}
    @if(!empty($today))
        <div class="mb-5 rounded-2xl overflow-hidden shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70">
            <div class="px-4 py-3 flex items-center justify-between
                        bg-gradient-to-r from-amber-100/70 via-orange-100/50 to-transparent
                        dark:from-amber-900/20 dark:via-orange-900/10">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg grid place-items-center shadow
                                bg-gradient-to-br from-amber-400 to-orange-500 text-white">
                        <i class="fas fa-sun text-xs"></i>
                    </div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Agenda Hari Ini</div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full
                                 bg-amber-500/20 text-amber-700 dark:text-amber-300 font-semibold">
                        {{ count($today) }} sesi
                    </span>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @foreach($today as $t)
                    <a href="{{ $t['url_show'] }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                        <div class="w-20 text-center shrink-0">
                            <div class="text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($t['start'])->format('H:i') }}
                            </div>
                            <div class="text-[10px] text-slate-400">→ {{ \Carbon\Carbon::parse($t['end'])->format('H:i') }}</div>
                        </div>
                        <div class="w-1.5 h-10 rounded-full bg-gradient-to-b {{ $t['status_gradient'] }}"></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-800 dark:text-white truncate">{{ $t['title'] }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                @if($t['room'])
                                    <span><i class="fas fa-door-open mr-1 text-[10px]"></i>{{ $t['room']['name'] }}</span>
                                @endif
                                @if(count($t['groups']))
                                    <span>· {{ collect($t['groups'])->pluck('name')->take(3)->join(', ') }}</span>
                                @endif
                                @if(!empty($t['mode']))
                                    <span>·
                                        @if($t['mode']==='online') 🌐 Online
                                        @elseif($t['mode']==='offline') 🏢 Offline
                                        @else 🔄 Hybrid
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full text-white shadow
                                     bg-gradient-to-r {{ $t['status_gradient'] }}">
                            {{ $t['status_label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================================================
         ============= VIEW: MONTH (LUXURY GRID) ==================
    ============================================================= --}}
    @if($meta['view'] === 'month')
        <div class="rounded-2xl overflow-hidden shadow-2xl ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70">
            {{-- Header hari --}}
            <div class="grid grid-cols-7
                        bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
                @foreach(['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)
                    <div class="py-3 text-center text-[11px] font-bold uppercase tracking-widest
                                text-cyan-200/90 border-r border-white/5 last:border-r-0">
                        {{ $d }}
                    </div>
                @endforeach
            </div>

            {{-- Body --}}
            <div class="grid grid-cols-7">
                @php
                    $cursorDay = \Carbon\Carbon::parse($meta['range_start'])->copy();
                    $gridEnd   = \Carbon\Carbon::parse($meta['range_end']);
                    $today     = \Carbon\Carbon::today();
                    $month     = \Carbon\Carbon::parse($meta['cursor'])->month;
                @endphp

                @while($cursorDay->lte($gridEnd))
                    @php
                        $key            = $cursorDay->format('Y-m-d');
                        $isCurrentMonth = $cursorDay->month === $month;
                        $isToday        = $cursorDay->isSameDay($today);
                        $isWeekend      = in_array($cursorDay->dayOfWeek, [0, 6], true);
                        $daySessions    = $byDate->get($key, collect());
                    @endphp

                    <div class="relative min-h-[170px] p-2 border-r border-b border-slate-100 dark:border-white/5
                                transition
                                {{ !$isCurrentMonth ? 'bg-slate-50/70 dark:bg-slate-900/40 opacity-70' : '' }}
                                {{ $isWeekend && $isCurrentMonth ? 'bg-rose-50/30 dark:bg-rose-950/10' : '' }}
                                {{ $isToday ? 'bg-gradient-to-br from-cyan-50 via-teal-50/50 to-transparent dark:from-cyan-950/40 dark:via-teal-950/20 ring-2 ring-cyan-400/60 ring-inset' : '' }}">

                        {{-- Nomor tanggal --}}
                        <div class="flex items-center justify-between mb-1.5 px-0.5">
                            <span class="text-sm font-extrabold inline-flex items-center justify-center w-7 h-7 rounded-lg
                                         {{ $isToday
                                            ? 'bg-gradient-to-br from-cyan-500 to-teal-600 text-white shadow-md'
                                            : ($isCurrentMonth
                                                ? 'text-slate-700 dark:text-slate-200'
                                                : 'text-slate-400 dark:text-slate-500') }}">
                                {{ $cursorDay->day }}
                            </span>
                            @if($daySessions->count())
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                             bg-gradient-to-r from-cyan-500 to-teal-500 text-white shadow">
                                    {{ $daySessions->count() }}
                                </span>
                            @endif
                        </div>

                        {{-- Sesi --}}
                        <div class="space-y-1">
                            @foreach($daySessions->take(4) as $s)
                                <a href="{{ route('owner.attendance.show', $s) }}"
                                   title="{{ $s->title }} — {{ $s->status_label }}
{{ $s->scheduled_start_at?->format('H:i') }}–{{ $s->scheduled_end_at?->format('H:i') }}"
                                   class="group block rounded-lg overflow-hidden shadow-sm
                                          ring-1 ring-black/5 dark:ring-white/10
                                          hover:scale-[1.03] hover:shadow-lg hover:z-10 relative
                                          transition-all duration-150
                                          {{ $s->is_conflicting ? 'ring-2 ring-red-500/80' : '' }}">

                                    <div class="px-2 py-1.5 bg-gradient-to-r {{ $s->status_gradient }} text-white">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white/90 shadow"></span>
                                            <span class="text-[10px] font-mono font-bold tracking-tight">
                                                {{ $s->scheduled_start_at?->format('H:i') }}
                                            </span>
                                            @if($s->is_started_early)
                                                <span class="text-[9px] px-1 rounded bg-white/25">⚡</span>
                                            @endif
                                            @if($s->is_conflicting)
                                                <span class="text-[9px] ml-auto">⚠</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-semibold truncate leading-tight mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($s->title, 18) }}
                                        </div>
                                    </div>

                                    <div class="px-2 py-1 bg-white dark:bg-slate-900/80
                                                border-t border-slate-100 dark:border-white/5
                                                flex items-center gap-1.5 flex-wrap text-[9px]">
                                        @if($s->room)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-white/5
                                                         text-slate-600 dark:text-slate-300 font-medium truncate max-w-[70px]">
                                                🏛 {{ \Illuminate\Support\Str::limit($s->room->name, 10) }}
                                            </span>
                                        @endif
                                        @if(method_exists($s, 'groups') && $s->relationLoaded('groups') && $s->groups->count())
                                            <span class="px-1.5 py-0.5 rounded bg-violet-100 dark:bg-violet-900/30
                                                         text-violet-700 dark:text-violet-300 font-semibold">
                                                G{{ $s->groups->count() }}
                                            </span>
                                        @endif
                                        @if(!empty($s->mode))
                                            <span class="ml-auto text-[9px]">
                                                @if($s->mode==='online') 🌐
                                                @elseif($s->mode==='offline') 🏢
                                                @else 🔄 @endif
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach

                            @if($daySessions->count() > 4)
                                <button type="button"
                                        class="w-full text-[10px] font-semibold text-cyan-600 dark:text-cyan-400
                                               hover:bg-cyan-50 dark:hover:bg-cyan-950/30 rounded py-1 transition">
                                    +{{ $daySessions->count() - 4 }} sesi lain
                                </button>
                            @endif
                        </div>
                    </div>
                    @php $cursorDay->addDay(); @endphp
                @endwhile
            </div>
        </div>

    {{-- ============================================================
         ============= VIEW: AGENDA ================================
    ============================================================= --}}
    @elseif($meta['view'] === 'agenda')
        <div class="rounded-2xl overflow-hidden shadow-2xl ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70 divide-y divide-slate-100 dark:divide-white/5">
            @forelse($sessions as $s)
                <a href="{{ route('owner.attendance.show', $s) }}"
                   class="group flex items-center gap-4 p-4 hover:bg-gradient-to-r
                          hover:from-cyan-50/60 hover:to-transparent
                          dark:hover:from-cyan-950/20 transition">
                    <div class="w-16 shrink-0 text-center">
                        <div class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">
                            {{ $s->scheduled_start_at?->translatedFormat('D') }}
                        </div>
                        <div class="text-2xl font-extrabold text-slate-800 dark:text-white leading-none">
                            {{ $s->scheduled_start_at?->format('d') }}
                        </div>
                        <div class="text-[10px] text-slate-500">
                            {{ $s->scheduled_start_at?->translatedFormat('M Y') }}
                        </div>
                    </div>

                    <div class="w-1.5 self-stretch rounded-full bg-gradient-to-b {{ $s->status_gradient }}"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="font-bold text-slate-800 dark:text-white truncate">{{ $s->title }}</div>
                            @if($s->is_started_early)
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-semibold">⚡ Dimulai awal</span>
                            @endif
                            @if($s->is_conflicting)
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 font-semibold">⚠ Konflik</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-3 flex-wrap">
                            <span class="font-mono">
                                {{ $s->scheduled_start_at?->format('H:i') }}–{{ $s->scheduled_end_at?->format('H:i') }}
                            </span>
                            @if($s->room)
                                <span>🏛 {{ $s->room->name }}</span>
                            @endif
                            @if(method_exists($s,'groups') && $s->relationLoaded('groups') && $s->groups->count())
                                <span>👥 {{ $s->groups->pluck('name')->take(3)->join(', ') }}{{ $s->groups->count() > 3 ? ' +'.($s->groups->count()-3) : '' }}</span>
                            @endif
                            @if(!empty($s->mode))
                                <span>@if($s->mode==='online') 🌐 Online @elseif($s->mode==='offline') 🏢 Offline @else 🔄 Hybrid @endif</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full text-white shadow
                                     bg-gradient-to-r {{ $s->status_gradient }}">
                            {{ $s->status_label }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-16 text-center">
                    <i class="fas fa-calendar-times text-4xl text-slate-300 dark:text-slate-600"></i>
                    <div class="text-slate-500 mt-3">Tidak ada sesi pada rentang ini.</div>
                </div>
            @endforelse
        </div>

    {{-- ============================================================
         ============= VIEW: WEEK (TIME-GRID) ======================
    ============================================================= --}}
    @elseif($meta['view'] === 'week')
        @php
            $days = [];
            $c = \Carbon\Carbon::parse($meta['range_start'])->copy();
            for ($i = 0; $i < 7; $i++) { $days[] = $c->copy(); $c->addDay(); }
            // Jam kerja — bisa diubah sesuai kebutuhan
            $hourStart = 6; $hourEnd = 22;
            // Group sessions by day index
            $sessionsByDay = collect();
            foreach ($sessions as $s) {
                $d = $s->scheduled_start_at?->format('Y-m-d');
                $sessionsByDay->put($d, ($sessionsByDay->get($d) ?? collect())->push($s));
            }
        @endphp

        <div class="rounded-2xl overflow-hidden shadow-2xl ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70">
            {{-- Header hari --}}
            <div class="grid grid-cols-[80px_repeat(7,minmax(0,1fr))]
                        bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
                <div class="py-3 text-center text-[10px] font-bold uppercase text-cyan-200/70">Jam</div>
                @foreach($days as $i => $d)
                    @php $isToday = $d->isToday(); @endphp
                    <div class="py-2.5 text-center border-l border-white/5
                                {{ $isToday ? 'bg-cyan-500/20' : '' }}">
                        <div class="text-[10px] uppercase tracking-widest text-cyan-200/90 font-bold">
                            {{ $d->translatedFormat('D') }}
                        </div>
                        <div class="text-lg font-extrabold text-white leading-none mt-0.5">
                            {{ $d->format('d') }}
                        </div>
                        <div class="text-[9px] text-cyan-200/60">{{ $d->translatedFormat('M Y') }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Grid body --}}
            <div class="grid grid-cols-[80px_repeat(7,minmax(0,1fr))]">
                {{-- Kolom jam --}}
                <div class="bg-slate-50 dark:bg-slate-900/40">
                    @for($h = $hourStart; $h <= $hourEnd; $h++)
                        <div class="h-14 text-[10px] text-right pr-2 pt-1 text-slate-500 dark:text-slate-400
                                    border-b border-slate-100 dark:border-white/5 font-mono font-semibold">
                            {{ sprintf('%02d:00', $h) }}
                        </div>
                    @endfor
                </div>

                {{-- Kolom hari --}}
                @foreach($days as $d)
                    @php
                        $dk = $d->format('Y-m-d');
                        $isToday = $d->isToday();
                        $daySessions = $sessionsByDay->get($dk, collect());
                    @endphp
                    <div class="relative border-l border-slate-100 dark:border-white/5
                                {{ $isToday ? 'bg-cyan-50/30 dark:bg-cyan-950/10' : '' }}">
                        {{-- Garis jam --}}
                        @for($h = $hourStart; $h <= $hourEnd; $h++)
                            <div class="h-14 border-b border-slate-100 dark:border-white/5"></div>
                        @endfor

                        {{-- Sesi absolut --}}
                        @foreach($daySessions as $s)
                            @php
                                $st = $s->scheduled_start_at;
                                $en = $s->scheduled_end_at ?? $st?->copy()->addHour();
                                if (!$st || !$en) continue;
                                $startMin = max(0, ($st->hour * 60 + $st->minute) - $hourStart * 60);
                                $endMin   = min(($hourEnd + 1) * 60, ($en->hour * 60 + $en->minute) - $hourStart * 60);
                                $height   = max(28, $endMin - $startMin);
                                $top      = $startMin * (56 / 60);   // 1 jam = 56px (h-14)
                            @endphp
                            <a href="{{ route('owner.attendance.show', $s) }}"
                               title="{{ $s->title }} — {{ $s->status_label }}"
                               style="top: {{ $top }}px; height: {{ $height }}px;"
                               class="absolute left-1 right-1 rounded-lg p-1.5
                                      shadow-md hover:shadow-2xl hover:z-20 hover:scale-[1.02]
                                      transition-all overflow-hidden text-white
                                      bg-gradient-to-br {{ $s->status_gradient }}
                                      {{ $s->is_conflicting ? 'ring-2 ring-red-400' : 'ring-1 ring-white/20' }}">
                                <div class="text-[10px] font-mono font-bold opacity-90">
                                    {{ $st->format('H:i') }}–{{ $en->format('H:i') }}
                                </div>
                                <div class="text-[11px] font-bold leading-tight truncate mt-0.5">
                                    {{ \Illuminate\Support\Str::limit($s->title, 22) }}
                                </div>
                                @if($s->room)
                                    <div class="text-[9px] opacity-90 truncate mt-0.5">
                                        🏛 {{ \Illuminate\Support\Str::limit($s->room->name, 14) }}
                                    </div>
                                @endif
                                @if($height > 70 && $s->relationLoaded('groups') && $s->groups->count())
                                    <div class="text-[9px] opacity-90 truncate">
                                        👥 {{ $s->groups->count() }} kelompok
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

    {{-- ============================================================
         ============= VIEW: DAY (TIMELINE) ========================
    ============================================================= --}}
    @else
        @php
            $hourStart = 6; $hourEnd = 22;
        @endphp
        <div class="rounded-2xl overflow-hidden shadow-2xl ring-1 ring-slate-200/70 dark:ring-white/5
                    bg-white dark:bg-slate-800/70">
            <div class="grid grid-cols-[90px_minmax(0,1fr)]">
                {{-- Kolom jam --}}
                <div class="bg-slate-50 dark:bg-slate-900/40">
                    @for($h = $hourStart; $h <= $hourEnd; $h++)
                        <div class="h-16 text-xs text-right pr-3 pt-1.5 font-mono font-bold
                                    text-slate-500 dark:text-slate-400
                                    border-b border-slate-100 dark:border-white/5">
                            {{ sprintf('%02d:00', $h) }}
                        </div>
                    @endfor
                </div>

                {{-- Kolom sesi --}}
                <div class="relative border-l border-slate-100 dark:border-white/5">
                    @for($h = $hourStart; $h <= $hourEnd; $h++)
                        <div class="h-16 border-b border-slate-100 dark:border-white/5"></div>
                    @endfor

                    @foreach($sessions as $s)
                        @php
                            $st = $s->scheduled_start_at;
                            $en = $s->scheduled_end_at ?? $st?->copy()->addHour();
                            if (!$st || !$en) continue;
                            $startMin = max(0, ($st->hour * 60 + $st->minute) - $hourStart * 60);
                            $endMin   = min(($hourEnd + 1) * 60, ($en->hour * 60 + $en->minute) - $hourStart * 60);
                            $height   = max(48, $endMin - $startMin);
                            $top      = $startMin * (64 / 60);
                        @endphp
                        <a href="{{ route('owner.attendance.show', $s) }}"
                           style="top: {{ $top }}px; height: {{ $height }}px;"
                           class="absolute left-3 right-3 rounded-xl p-3
                                  shadow-lg hover:shadow-2xl hover:scale-[1.005] transition-all
                                  text-white bg-gradient-to-r {{ $s->status_gradient }}
                                  {{ $s->is_conflicting ? 'ring-2 ring-red-400' : 'ring-1 ring-white/20' }}">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 text-center">
                                    <div class="text-xs font-mono font-extrabold">
                                        {{ $st->format('H:i') }}
                                    </div>
                                    <div class="text-[10px] opacity-80">→ {{ $en->format('H:i') }}</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-sm truncate">{{ $s->title }}</div>
                                    <div class="text-[11px] opacity-90 mt-0.5 flex items-center gap-3 flex-wrap">
                                        @if($s->room)<span>🏛 {{ $s->room->name }}</span>@endif
                                        @if($s->relationLoaded('groups') && $s->groups->count())
                                            <span>👥 {{ $s->groups->pluck('name')->take(4)->join(', ') }}</span>
                                        @endif
                                        @if(!empty($s->mode))
                                            <span>@if($s->mode==='online') 🌐 @elseif($s->mode==='offline') 🏢 @else 🔄 @endif {{ ucfirst($s->mode) }}</span>
                                        @endif
                                    </div>
                                    @if($height > 90)
                                        <div class="mt-1.5 inline-block px-2 py-0.5 rounded-md bg-white/20 text-[10px] font-semibold">
                                            {{ $s->status_label }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================
         LEGENDA LUXURY
    ============================================================= --}}
    <div class="mt-5 rounded-2xl p-4 bg-white dark:bg-slate-800/70 shadow-lg
                ring-1 ring-slate-200/70 dark:ring-white/5">
        <div class="text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">
            Legenda Status
        </div>
        <div class="flex flex-wrap gap-2.5">
            @foreach($meta['status_labels'] as $k => $label)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                            bg-slate-50 dark:bg-slate-900/50 ring-1 ring-slate-200 dark:ring-white/10">
                    <span class="w-3 h-3 rounded-full shadow
                                 bg-gradient-to-br {{ $meta['statuses'][$k] ?? 'from-gray-400 to-gray-600' }}"></span>
                    <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============================================================
     Alpine component
============================================================= --}}
<script>
function attendanceCalendar(initialView, initialCursor) {
    return {
        view: initialView,
        cursor: initialCursor,
        init() {
            // Slot untuk realtime refresh (polling /calendar/data / Echo).
        },
    };
}
</script>
@endsection