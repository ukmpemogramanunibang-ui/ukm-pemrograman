@extends('layouts.roles.owner')

@section('content')
@php
    $sv = $log->severity_meta;
    $rs = $log->result_meta;
    $isValid = $log->isIntegrityValid();
@endphp
<div class="p-3 md:p-6 w-full max-w-[1500px] mx-auto">

    {{-- ============ HEADER ============ --}}
    <div class="relative overflow-hidden rounded-2xl mb-5 p-5 md:p-7
                bg-gradient-to-br from-slate-900 via-violet-900 to-indigo-900
                shadow-2xl ring-1 ring-white/10">
        <div class="absolute inset-0 opacity-40 pointer-events-none"
             style="background:
                radial-gradient(700px 240px at 8% 0%, rgba(139,92,246,.45), transparent 65%),
                radial-gradient(700px 240px at 92% 100%, rgba(34,211,238,.35), transparent 65%);"></div>

        <div class="relative flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-[11px] text-violet-200/80 mb-2">
                    <a href="{{ route('owner.attendance.audit.index') }}" class="hover:underline">
                        <i class="fas fa-shield-halved mr-1"></i> Audit Center
                    </a>
                    <span>/</span>
                    <span class="font-mono">{{ $log->event_uuid }}</span>
                </div>
                <h1 class="text-xl md:text-2xl font-extrabold font-mono tracking-tight text-white">
                    {{ $log->action }}
                </h1>
                <div class="mt-3 flex flex-wrap gap-2 text-[10px] text-white/85">
                    <span class="px-2.5 py-1 rounded-full bg-{{ $sv['color'] }}-500/30 ring-1 ring-white/20">
                        {{ $sv['label'] }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-{{ $rs['color'] }}-500/30 ring-1 ring-white/20">
                        <i class="fas {{ $rs['icon'] }} mr-1"></i>{{ $rs['label'] }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-white/10 ring-1 ring-white/20">
                        {{ $log->category_label }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-white/10 ring-1 ring-white/20">
                        {{ $log->source_label }}
                    </span>
                    @if($log->is_sensitive)
                        <span class="px-2.5 py-1 rounded-full bg-fuchsia-500/30 ring-1 ring-fuchsia-300/50 text-fuchsia-100 font-bold">
                            🔒 SENSITIVE
                        </span>
                    @endif
                    <span class="px-2.5 py-1 rounded-full ring-1
                                 {{ $isValid
                                    ? 'bg-emerald-500/25 ring-emerald-300/40 text-emerald-100'
                                    : 'bg-red-500/30 ring-red-300/50 text-red-100' }}">
                        <i class="fas {{ $isValid ? 'fa-lock' : 'fa-lock-open' }} mr-1"></i>
                        {{ $isValid ? 'HASH VALID' : 'HASH BROKEN' }}
                    </span>
                </div>
            </div>
            <div class="text-right shrink-0">
                <div class="text-[10px] uppercase text-violet-200/70 tracking-wider">Waktu</div>
                <div class="text-sm font-bold text-white font-mono">
                    {{ $log->created_at?->format('d M Y · H:i:s') }}
                </div>
                <div class="text-[11px] text-violet-200/70 mt-0.5">{{ $log->created_at?->diffForHumans() }}</div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- ============ LEFT: ACTOR + ENTITY ============ --}}
        <div class="space-y-5">

            {{-- Actor --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-800/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-cyan-100/70 to-transparent dark:from-cyan-900/20
                            flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br from-cyan-500 to-blue-600">
                        <i class="fas fa-user-shield text-xs"></i>
                    </div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Aktor</div>
                </div>
                <div class="p-4 space-y-2.5 text-sm">
                    <div>
                        <div class="text-[10px] uppercase text-slate-500 tracking-wider">Nama</div>
                        <div class="font-semibold text-slate-800 dark:text-white">{{ $log->actor_name }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">Role</div>
                            <div class="text-slate-700 dark:text-slate-300">{{ $log->actor_role ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">Tipe</div>
                            <div class="text-slate-700 dark:text-slate-300 font-mono">{{ $log->actor_type }}</div>
                        </div>
                    </div>
                    @if($log->target_user_id)
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">Target User</div>
                            <div class="text-slate-700 dark:text-slate-300">
                                {{ $log->target->name ?? ('#'.$log->target_user_id) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Entity --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-800/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-violet-100/70 to-transparent dark:from-violet-900/20
                            flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br from-violet-500 to-fuchsia-600">
                        <i class="fas fa-cube text-xs"></i>
                    </div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Entity</div>
                </div>
                <div class="p-4 space-y-2.5 text-sm">
                    @if($log->auditable_type)
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">Tipe</div>
                            <div class="text-slate-700 dark:text-slate-300 font-mono text-xs">
                                {{ class_basename($log->auditable_type) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">ID</div>
                            <div class="text-slate-700 dark:text-slate-300 font-mono">#{{ $log->auditable_id }}</div>
                        </div>
                    @endif
                    @if($log->session_id)
                        <a href="{{ route('owner.attendance.show', $log->session_id) }}"
                           class="block p-2.5 rounded-xl bg-cyan-50 dark:bg-cyan-900/20
                                  text-cyan-700 dark:text-cyan-300 hover:bg-cyan-100 transition text-xs font-semibold">
                            🔗 Lihat Sesi #{{ $log->session_id }}
                        </a>
                    @endif
                    @if($log->room_id)
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider">Ruangan</div>
                            <div class="text-slate-700 dark:text-slate-300">{{ $log->room->name ?? '#'.$log->room_id }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tracing --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-800/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-slate-100/70 to-transparent dark:from-slate-900/20
                            flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br from-slate-500 to-slate-700">
                        <i class="fas fa-network-wired text-xs"></i>
                    </div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Tracing &amp; Device</div>
                </div>
                <div class="p-4 space-y-2 text-xs font-mono">
                    <div class="flex justify-between gap-3">
                        <span class="text-slate-500">IP</span>
                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $log->ip_address ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-slate-500">Request ID</span>
                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $log->request_id ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-slate-500">Correlation</span>
                        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $log->correlation_id ?? '-' }}</span>
                    </div>
                    @if($log->user_agent)
                        <div class="pt-2 border-t border-slate-100 dark:border-white/5">
                            <div class="text-slate-500 mb-1">User Agent</div>
                            <div class="text-slate-600 dark:text-slate-400 text-[10px] break-all">
                                {{ \Illuminate\Support\Str::limit($log->user_agent, 180) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============ RIGHT: DIFF + TIMELINE + INTEGRITY ============ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Description & reason --}}
            @if($log->description || $log->reason)
                <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                            bg-white dark:bg-slate-800/70 p-4 space-y-3">
                    @if($log->description)
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider mb-1">Deskripsi</div>
                            <div class="text-sm text-slate-700 dark:text-slate-300">{{ $log->description }}</div>
                        </div>
                    @endif
                    @if($log->reason)
                        <div>
                            <div class="text-[10px] uppercase text-slate-500 tracking-wider mb-1">Alasan</div>
                            <div class="text-sm text-slate-700 dark:text-slate-300 italic">
                                "{{ $log->reason }}"
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Authorization --}}
            @if($log->permission || $log->authorization_result)
                <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                            bg-white dark:bg-slate-800/70 p-4">
                    <div class="text-[10px] uppercase text-slate-500 tracking-wider mb-2">Authorization</div>
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        @if($log->permission)
                            <span class="font-mono px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                {{ $log->permission }}
                            </span>
                        @endif
                        @if($log->authorization_result)
                            <span class="font-bold px-2 py-1 rounded
                                         {{ $log->authorization_result === 'granted'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                {{ strtoupper($log->authorization_result) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- DIFF VIEWER --}}
            @if($log->hasChange())
                <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                            bg-white dark:bg-slate-800/70 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-amber-100/70 to-transparent dark:from-amber-900/20
                                flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                    bg-gradient-to-br from-amber-400 to-orange-600">
                            <i class="fas fa-code-compare text-xs"></i>
                        </div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Perubahan Data</div>
                        <span class="ml-auto text-[10px] px-2 py-0.5 rounded-full
                                     bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold">
                            {{ count($log->change_data) }} field
                        </span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($log->change_data as $field => $chg)
                            <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_1fr] gap-0 md:gap-3
                                        items-stretch md:items-start p-3">
                                <div class="md:pt-1">
                                    <div class="text-[10px] uppercase text-slate-500 tracking-wider">Field</div>
                                    <div class="font-mono text-xs font-bold text-slate-700 dark:text-slate-200 mt-0.5">
                                        {{ $field }}
                                    </div>
                                </div>
                                <div class="mt-2 md:mt-0 p-2.5 rounded-lg bg-red-50 dark:bg-red-950/20
                                            border border-red-100 dark:border-red-900/30">
                                    <div class="text-[10px] uppercase text-red-600 dark:text-red-400 tracking-wider font-bold mb-1">
                                        Sebelum
                                    </div>
                                    <div class="text-xs text-red-800 dark:text-red-200 font-mono break-all">
                                        {{ $chg['before'] === null ? '∅' : (is_scalar($chg['before']) ? $chg['before'] : json_encode($chg['before'])) }}
                                    </div>
                                </div>
                                <div class="mt-2 md:mt-0 p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/20
                                            border border-emerald-100 dark:border-emerald-900/30">
                                    <div class="text-[10px] uppercase text-emerald-600 dark:text-emerald-400 tracking-wider font-bold mb-1">
                                        Sesudah
                                    </div>
                                    <div class="text-xs text-emerald-800 dark:text-emerald-200 font-mono break-all">
                                        {{ $chg['after'] === null ? '∅' : (is_scalar($chg['after']) ? $chg['after'] : json_encode($chg['after'])) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- TIMELINE (correlation chain) --}}
            @if($related->isNotEmpty())
                <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                            bg-white dark:bg-slate-800/70 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-cyan-100/70 to-transparent dark:from-cyan-900/20
                                flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                    bg-gradient-to-br from-cyan-500 to-teal-600">
                            <i class="fas fa-stream text-xs"></i>
                        </div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm">Timeline Korelasi</div>
                        <span class="ml-auto text-[10px] font-mono text-slate-500">
                            {{ $log->correlation_id }}
                        </span>
                    </div>
                    <div class="p-4">
                        <ol class="relative border-l-2 border-slate-200 dark:border-slate-700 ml-3 space-y-4">
                            @foreach($related as $r)
                                @php $rsv = $r->severity_meta; @endphp
                                <li class="ml-5 relative">
                                    <span class="absolute -left-[27px] top-1 w-4 h-4 rounded-full
                                                 bg-{{ $rsv['color'] }}-500 ring-4 ring-white dark:ring-slate-800"></span>
                                    <a href="{{ route('owner.attendance.audit.show', $r) }}"
                                       class="block text-sm hover:text-violet-600 transition">
                                        <div class="font-semibold text-slate-800 dark:text-white font-mono text-xs">
                                            {{ $r->action }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $r->actor_name }} · {{ $r->created_at->format('H:i:s') }}
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            @endif

            {{-- Integrity hash --}}
            <div class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                        bg-white dark:bg-slate-800/70 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-emerald-100/70 to-transparent dark:from-emerald-900/20
                            flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg grid place-items-center text-white shadow
                                bg-gradient-to-br from-emerald-500 to-teal-600">
                        <i class="fas fa-fingerprint text-xs"></i>
                    </div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">Integritas Hash Chain</div>
                </div>
                <div class="p-4 space-y-2 text-xs font-mono">
                    <div>
                        <div class="text-slate-500 mb-1">Previous Hash</div>
                        <div class="text-slate-700 dark:text-slate-300 break-all p-2 rounded bg-slate-50 dark:bg-slate-900/60">
                            {{ $log->previous_hash ?? '∅ (genesis)' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-1">Integrity Hash</div>
                        <div class="text-slate-700 dark:text-slate-300 break-all p-2 rounded bg-slate-50 dark:bg-slate-900/60
                                    {{ $isValid ? 'ring-1 ring-emerald-400/50' : 'ring-2 ring-red-500/60' }}">
                            {{ $log->integrity_hash }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Raw JSON --}}
            <details class="rounded-2xl shadow-lg ring-1 ring-slate-200/70 dark:ring-white/5
                            bg-white dark:bg-slate-800/70 overflow-hidden">
                <summary class="px-4 py-3 cursor-pointer bg-gradient-to-r from-slate-100/70 to-transparent
                                dark:from-slate-900/20 font-bold text-sm text-slate-800 dark:text-white">
                    <i class="fas fa-brackets-curly mr-2 text-slate-500"></i> Raw Payload (JSON)
                </summary>
                <pre class="p-4 overflow-x-auto text-[11px] leading-relaxed
                            bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300">{{ json_encode($log->only([
                    'event_uuid','actor_type','actor_id','actor_name','actor_role',
                    'target_user_id','action','category','severity','result',
                    'auditable_type','auditable_id','session_id','group_id','room_id',
                    'description','before_data','after_data','change_data','reason',
                    'permission','authorization_result','source','ip_address','user_agent','device_id',
                    'request_id','correlation_id','affected_count','affected_ids',
                    'previous_hash','integrity_hash','is_sensitive','created_at',
                ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ route('owner.attendance.audit.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  border border-slate-300 dark:border-white/10 text-slate-700 dark:text-slate-300
                  hover:bg-slate-100 dark:hover:bg-white/5">
            <i class="fas fa-arrow-left"></i> Kembali ke Audit Center
        </a>
    </div>
</div>
@endsection