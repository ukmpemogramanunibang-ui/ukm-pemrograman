@extends('layouts.roles.owner')

@section('title', 'System Health – UNICODE')
@section('page-title', '🩺 System Health')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">🩺 System Health</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Pemeriksaan terakhir: <span class="font-mono" id="last-check">{{ $status['system_time'] }}</span>
                <span class="ml-2 text-xs text-gray-400">({{ $status['total_checks'] }} total, {{ $status['passed_checks'] }} sehat)</span>
                @if(!$status['is_deep'])
                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded">Quick Check</span>
                @else
                    <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Deep Check</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('owner.system.health', ['refresh' => 1]) }}"
               class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Quick Check
            </a>
            <a href="{{ route('owner.system.health', ['deep' => 1]) }}"
               class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-microscope"></i> Deep Check
            </a>
            <button onclick="window.print()"
                    class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>

    {{-- Overall Status --}}
    <div class="rounded-2xl border p-5
                @if($status['overall'] == 'normal')
                    border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800
                @elseif($status['overall'] == 'warning')
                    border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800
                @else
                    border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800
                @endif
                ">
        <div class="flex items-center gap-3">
            <span class="text-4xl">
                @if($status['overall'] == 'normal') 🟢
                @elseif($status['overall'] == 'warning') 🟡
                @else 🔴
                @endif
            </span>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white">
                    @if($status['overall'] == 'normal')
                        ✅ Semua Sistem Normal ({{ $status['passed_checks'] }}/{{ $status['total_checks'] }})
                    @elseif($status['overall'] == 'warning')
                        ⚠️ Ada Peringatan ({{ count($status['warning_components']) }} komponen)
                    @else
                        ❌ Ada Masalah Serius ({{ count($status['failed_components']) }} komponen gagal)
                    @endif
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $status['overall'] == 'normal' ? 'Semua komponen berjalan dengan baik.' : 'Beberapa komponen perlu perhatian.' }}
                </p>
            </div>
        </div>
        <div class="mt-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
            <div class="h-2.5 rounded-full transition-all duration-500
                @if($status['overall'] == 'normal') bg-green-500
                @elseif($status['overall'] == 'warning') bg-yellow-500
                @else bg-red-500 @endif"
                style="width: {{ ($status['passed_checks'] / $status['total_checks']) * 100 }}%">
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-1">{{ round(($status['passed_checks'] / $status['total_checks']) * 100) }}% komponen sehat</p>
    </div>

    {{-- Health History --}}
    @if(isset($history) && $history->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400">📈 Health History</h3>
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach($history as $item)
                <span class="text-xs px-2 py-1 rounded-full border
                    @if($item->status == 'normal') border-green-200 bg-green-50 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300
                    @elseif($item->status == 'warning') border-yellow-200 bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300
                    @else border-red-200 bg-red-50 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300
                    @endif
                ">
                    {{ $item->status == 'normal' ? '🟢' : ($item->status == 'warning' ? '🟡' : '🔴') }}
                    {{ \Carbon\Carbon::parse($item->checked_at ?? $item->created_at)->format('d M H:i') }}
                    ({{ $item->duration_ms }}ms)
                </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Grid Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- LARAVEL --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🧬 Laravel</h3>
                <span class="text-sm {{ $status['laravel']['status'] == 'normal' ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $status['laravel']['status'] == 'normal' ? '🟢' : '🟡' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Version:</span> <span class="font-semibold">{{ $status['laravel']['version'] }}</span></p>
                <p><span class="text-gray-500">Env:</span> <span class="font-semibold">{{ $status['laravel']['env'] }}</span></p>
                <p><span class="text-gray-500">Debug:</span> <span class="{{ $status['laravel']['debug'] ? 'text-yellow-500' : 'text-green-500' }}">{{ $status['laravel']['debug'] ? 'ON' : 'OFF' }}</span></p>
                <p><span class="text-gray-500">Config Cache:</span> {{ $status['laravel']['cached']['config'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Route Cache:</span> {{ $status['laravel']['cached']['route'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">View Cache:</span> {{ $status['laravel']['cached']['view'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Event Cache:</span> {{ $status['laravel']['cached']['event'] ? '✅' : '❌' }}</p>
                @if(isset($status['laravel']['migrations']['pending']) && $status['laravel']['migrations']['pending'] > 0)
                    <p class="text-yellow-600"><span class="text-gray-500">Migrasi:</span> {{ $status['laravel']['migrations']['pending'] }} pending</p>
                @endif
            </div>
        </div>

        {{-- SERVER --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🖥️ Server</h3>
                <span class="text-sm {{ $status['server']['status'] == 'normal' ? 'text-green-500' : ($status['server']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ $status['server']['status'] == 'normal' ? '🟢' : ($status['server']['status'] == 'warning' ? '🟡' : '🔴') }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Server:</span> {{ $status['server']['server_software'] }}</p>
                <p><span class="text-gray-500">OS:</span> {{ $status['server']['os'] }}</p>
                <p><span class="text-gray-500">PHP:</span> <span class="font-semibold">{{ $status['php']['version'] }}</span> ({{ $status['php']['architecture'] }})</p>
                <p><span class="text-gray-500">Memory Usage:</span> {{ $status['server']['memory_usage'] }} ({{ $status['server']['memory_percent'] ?? 0 }}%)</p>
                <p><span class="text-gray-500">Memory Limit:</span> {{ $status['server']['memory_limit'] }}</p>
                <p><span class="text-gray-500">Upload Max:</span> {{ $status['server']['max_upload'] }}</p>
                <p><span class="text-gray-500">Post Max:</span> {{ $status['server']['max_post'] }}</p>
                <p><span class="text-gray-500">Max Exec:</span> {{ $status['server']['max_execution'] }}</p>
                <p><span class="text-gray-500">Load Avg:</span> {{ $status['server']['load_avg'] }}</p>
                <p><span class="text-gray-500">Timezone:</span> {{ $status['php']['timezone'] }}</p>
                @if(isset($status['server']['warnings']) && count($status['server']['warnings']) > 0)
                    <p class="text-yellow-500 text-xs">{{ implode(', ', $status['server']['warnings']) }}</p>
                @endif
            </div>
        </div>

        {{-- PHP --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🐘 PHP</h3>
                <span class="text-sm {{ $status['php']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $status['php']['status'] == 'normal' ? '🟢' : '🔴' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Version:</span> <span class="font-semibold">{{ $status['php']['version'] }}</span></p>
                <p><span class="text-gray-500">SAPI:</span> {{ $status['php']['sapi'] }}</p>
                <p><span class="text-gray-500">Memory Limit:</span> {{ $status['php']['memory_limit'] }}</p>
                <p><span class="text-gray-500">Max Exec:</span> {{ $status['php']['max_execution_time'] }}s</p>
                @if(isset($status['php']['missing_extensions']) && count($status['php']['missing_extensions']) > 0)
                    <p class="text-red-500 text-xs">❌ Missing: {{ implode(', ', $status['php']['missing_extensions']) }}</p>
                @endif
            </div>
        </div>

        {{-- DISK --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">💾 Disk</h3>
                <span class="text-sm {{ $status['disk']['status'] == 'normal' ? 'text-green-500' : ($status['disk']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ $status['disk']['status'] == 'normal' ? '🟢' : ($status['disk']['status'] == 'warning' ? '🟡' : '🔴') }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Total:</span> {{ $status['disk']['total'] }}</p>
                <p><span class="text-gray-500">Used:</span> {{ $status['disk']['used'] }}</p>
                <p><span class="text-gray-500">Free:</span> {{ $status['disk']['free'] }}</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mt-1">
                    <div class="h-3 rounded-full transition-all duration-500
                        {{ $status['disk']['percent'] > 85 ? 'bg-red-500' : ($status['disk']['percent'] > 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                        style="width: {{ min($status['disk']['percent'], 100) }}%">
                    </div>
                </div>
                <p class="text-xs font-semibold {{ $status['disk']['percent'] > 85 ? 'text-red-500' : ($status['disk']['percent'] > 70 ? 'text-yellow-500' : 'text-green-500') }}">
                    {{ $status['disk']['percent'] }}% — {{ $status['disk']['message'] }}
                </p>
            </div>
        </div>

        {{-- DATABASE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🗄️ Database</h3>
                <span class="text-sm {{ $status['database']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $status['database']['status'] == 'normal' ? '🟢' : '🔴' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">DB:</span> <span class="font-semibold">{{ $status['database']['name'] }}</span></p>
                <p><span class="text-gray-500">Host:</span> {{ $status['database']['host'] }}:{{ $status['database']['port'] }}</p>
                <p><span class="text-gray-500">Tables:</span> {{ $status['database']['tables_count'] }}</p>
                <p><span class="text-gray-500">Size:</span> <span class="font-semibold">{{ $status['database']['size'] }}</span></p>
                <p><span class="text-gray-500">Total Rows:</span> {{ $status['database']['total_rows'] ?? 'N/A' }}</p>
                @if(isset($status['database']['largest_table']))
                    <p><span class="text-gray-500">Largest:</span> {{ $status['database']['largest_table']['name'] }} ({{ $status['database']['largest_table']['size'] }})</p>
                @endif
                <p><span class="text-gray-500">Query Time:</span> <span class="font-semibold">{{ $status['database']['query_time'] }} ms</span></p>
                <p><span class="text-gray-500">Migration:</span> {{ $status['database']['migrations'] }}</p>
                @if(isset($status['database']['error']))
                    <p class="text-red-500 text-xs">{{ $status['database']['error'] }}</p>
                @endif
            </div>
        </div>

        {{-- STORAGE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">📦 Storage</h3>
                <span class="text-sm {{ $status['storage']['status'] == 'normal' ? 'text-green-500' : ($status['storage']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ $status['storage']['status'] == 'normal' ? '🟢' : ($status['storage']['status'] == 'warning' ? '🟡' : '🔴') }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Path:</span> <span class="text-xs">{{ storage_path() }}</span></p>
                <p><span class="text-gray-500">Writable:</span> {{ $status['storage']['storage_writable'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Public Writable:</span> {{ $status['storage']['app_public_writable'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Symlink:</span> {{ $status['storage']['symlink_valid'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Temp File:</span> {{ $status['storage']['temp_file_writable'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Free Space:</span> {{ $status['storage']['disk_free_space'] ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- CACHE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">⚡ Cache</h3>
                <span class="text-sm {{ $status['cache']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $status['cache']['status'] == 'normal' ? '🟢' : '🔴' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Driver:</span> <span class="font-semibold">{{ $status['cache']['driver'] }}</span></p>
                <p><span class="text-gray-500">Store Driver:</span> {{ $status['cache']['store_driver'] }}</p>
                <p><span class="text-gray-500">Connected:</span> {{ $status['cache']['connected'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Can Store:</span> {{ $status['cache']['can_store'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Can Retrieve:</span> {{ $status['cache']['can_retrieve'] ? '✅' : '❌' }}</p>
            </div>
        </div>

        {{-- SESSION --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🔑 Session</h3>
                <span class="text-sm {{ $status['session']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $status['session']['status'] == 'normal' ? '🟢' : '🔴' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Driver:</span> <span class="font-semibold">{{ $status['session']['driver'] }}</span></p>
                <p><span class="text-gray-500">Lifetime:</span> {{ $status['session']['lifetime'] ?? 'N/A' }} menit</p>
                <p><span class="text-gray-500">Active:</span> {{ $status['session']['is_active'] ? '✅' : '❌' }}</p>
                @if(isset($status['session']['error']))
                    <p class="text-red-500 text-xs">{{ $status['session']['error'] }}</p>
                @endif
            </div>
        </div>

        {{-- SECURITY --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🔐 Security</h3>
                <span class="text-sm
                    {{ $status['security']['status'] == 'safe' ? 'text-green-500' : ($status['security']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ $status['security']['status'] == 'safe' ? '🟢' : ($status['security']['status'] == 'warning' ? '🟡' : '🔴') }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Environment:</span> <span class="font-semibold">{{ $status['security']['environment'] }}</span></p>
                <p><span class="text-gray-500">Debug:</span> {{ $status['security']['debug_mode'] }} {{ $status['security']['debug_status'] }}</p>
                <p><span class="text-gray-500">APP_KEY:</span> {{ $status['security']['app_key_status'] }}</p>
                <p><span class="text-gray-500">HTTPS:</span> {{ $status['security']['https_status'] }}</p>
                <p><span class="text-gray-500">Session Secure:</span> {{ $status['security']['session_secure'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Session HttpOnly:</span> {{ $status['security']['session_http_only'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">CSRF:</span> {{ isset($status['security']['csrf_enabled']) && $status['security']['csrf_enabled'] ? '✅' : '❌' }}</p>
                <p><span class="text-gray-500">Allowed Hosts:</span> {{ count($status['security']['allowed_hosts']) > 0 ? implode(', ', $status['security']['allowed_hosts']) : 'Tidak dibatasi' }}</p>
                @if(isset($status['security']['warning']))
                    <p class="text-yellow-600 dark:text-yellow-400 text-xs font-semibold">{{ $status['security']['warning'] }}</p>
                @endif
            </div>
        </div>

        {{-- ROLES --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">👥 Roles</h3>
                <span class="text-sm {{ $status['roles']['status'] == 'normal' ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $status['roles']['status'] == 'normal' ? '🟢' : '🟡' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Total Roles:</span> <span class="font-semibold">{{ $status['roles']['total_roles'] ?? 0 }}</span></p>
                <div class="grid grid-cols-2 gap-1 text-xs mt-1">
                    @foreach($status['roles']['roles'] ?? [] as $role => $statusIcon)
                        <span>{{ $statusIcon }} {{ $role }}</span>
                    @endforeach
                </div>
                @if(isset($status['roles']['user_counts']) && count($status['roles']['user_counts']) > 0)
                    <details class="mt-1">
                        <summary class="text-xs text-blue-500 cursor-pointer">📊 User count per role</summary>
                        <div class="mt-1 text-xs grid grid-cols-2 gap-1">
                            @foreach($status['roles']['user_counts'] as $name => $count)
                                <span>{{ $name }}: <span class="font-semibold">{{ $count }}</span></span>
                            @endforeach
                        </div>
                    </details>
                @endif
                @if(isset($status['roles']['error']))
                    <p class="text-red-500 text-xs">{{ $status['roles']['error'] }}</p>
                @endif
            </div>
        </div>

        {{-- ROUTES --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🛣️ Registered Routes</h3>
                <span class="text-sm {{ $status['routes']['status'] == 'normal' ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ $status['routes']['status'] == 'normal' ? '🟢' : '🟡' }}
                </span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Total Registered:</span> <span class="font-semibold">{{ $status['routes']['total_registered'] }}</span></p>
                @if(isset($status['routes']['failed_routes']) && $status['routes']['failed_routes'] > 0)
                    <p class="text-yellow-500">⚠️ {{ $status['routes']['failed_routes'] }} route core tidak terdaftar</p>
                @endif
                <div class="grid grid-cols-2 gap-1 text-xs mt-1">
                    @foreach($status['routes']['core_routes'] as $name => $info)
                        <span>{{ $info['status'] }} {{ $name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- PERFORMANCE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">⚡ Performance</h3>
                <span class="text-sm text-green-500">🟢</span>
            </div>
            <div class="mt-2 text-sm space-y-0.5">
                <p><span class="text-gray-500">Response Time:</span> <span class="font-semibold">{{ $status['performance']['response_time'] ?? 'N/A' }} ms</span></p>
                <p><span class="text-gray-500">DB Query Time:</span> <span class="font-semibold">{{ $status['performance']['db_query_time'] ?? 'N/A' }}</span></p>
                <p><span class="text-gray-500">Memory Used:</span> {{ $status['performance']['memory_used'] ?? 'N/A' }}</p>
                <p><span class="text-gray-500">Memory Peak:</span> {{ $status['performance']['memory_peak'] ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- ===== DEEP CHECK COMPONENTS (if available) ===== --}}
        @if($status['is_deep'])
            {{-- UPLOAD --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">📤 Upload</h3>
                    <span class="text-sm {{ $status['upload']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                        {{ $status['upload']['status'] == 'normal' ? '🟢' : '🔴' }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">Max Upload:</span> <span class="font-semibold">{{ $status['upload']['upload_max'] }}</span></p>
                    <p><span class="text-gray-500">Post Max:</span> {{ $status['upload']['post_max'] }}</p>
                    <p><span class="text-gray-500">Max File Uploads:</span> {{ $status['upload']['max_file_uploads'] }}</p>
                    <p><span class="text-gray-500">Storage Writable:</span> {{ $status['upload']['storage_writable'] ? '✅' : '❌' }}</p>
                    <p><span class="text-gray-500">Test Upload:</span> <span class="font-semibold">{{ $status['upload']['upload_test'] ?? 'N/A' }}</span></p>
                </div>
            </div>

            {{-- IMAGE --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">🖼️ Image</h3>
                    <span class="text-sm {{ $status['image']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                        {{ $status['image']['status'] == 'normal' ? '🟢' : '🔴' }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">GD:</span> {{ $status['image']['gd_loaded'] ? '✅' : '❌' }}</p>
                    @if($status['image']['gd_loaded'])
                        <p><span class="text-gray-500">JPEG:</span> {{ $status['image']['info']['jpeg'] ? '✅' : '❌' }}</p>
                        <p><span class="text-gray-500">PNG:</span> {{ $status['image']['info']['png'] ? '✅' : '❌' }}</p>
                        <p><span class="text-gray-500">WebP:</span> {{ $status['image']['info']['webp'] ? '✅' : '❌' }}</p>
                        <p><span class="text-gray-500">GIF:</span> {{ $status['image']['info']['gif'] ?? '❌' }}</p>
                        <p><span class="text-gray-500">Version:</span> {{ $status['image']['info']['gd_version'] ?? 'N/A' }}</p>
                    @endif
                    <p><span class="text-gray-500">Fileinfo:</span> {{ $status['image']['fileinfo'] ? '✅' : '❌' }}</p>
                    <p><span class="text-gray-500">Imagick:</span> {{ $status['image']['imagick'] ? '✅' : '❌' }}</p>
                </div>
            </div>

            {{-- QR --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">🔲 QR</h3>
                    <span class="text-sm {{ $status['qr']['status'] == 'normal' ? 'text-green-500' : ($status['qr']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $status['qr']['status'] == 'normal' ? '🟢' : ($status['qr']['status'] == 'warning' ? '🟡' : '🔴') }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">Endroid:</span> {{ $status['qr']['endroid_installed'] ? '✅' : '❌' }}</p>
                    <p><span class="text-gray-500">GD:</span> {{ $status['qr']['gd_available'] ? '✅' : '❌' }}</p>
                    <p><span class="text-gray-500">Test Generate:</span> {{ $status['qr']['test_generate'] ? '✅' : '❌' }}</p>
                    @if(isset($status['qr']['error']))
                        <p class="text-red-500 text-xs">{{ $status['qr']['error'] }}</p>
                    @endif
                </div>
            </div>

            {{-- MAIL --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">📧 Mail</h3>
                    <span class="text-sm {{ $status['mail']['status'] == 'normal' ? 'text-green-500' : 'text-yellow-500' }}">
                        {{ $status['mail']['status'] == 'normal' ? '🟢' : '🟡' }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">Mailer:</span> {{ $status['mail']['mailer'] }}</p>
                    <p><span class="text-gray-500">Host:</span> {{ $status['mail']['host'] }}:{{ $status['mail']['port'] }}</p>
                    <p><span class="text-gray-500">Encryption:</span> {{ $status['mail']['encryption'] ?? 'N/A' }}</p>
                    <p><span class="text-gray-500">From:</span> {{ $status['mail']['from'] }}</p>
                    <p><span class="text-gray-500">Configured:</span> {{ $status['mail']['configured'] ? '✅' : '❌' }}</p>
                    <form action="{{ route('owner.system.mail-test') }}" method="POST" class="mt-2">
                        @csrf
                        <div class="flex gap-2">
                            <input type="email" name="email" value="{{ auth()->user()->email }}" 
                                   class="flex-1 text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1">
                            <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition">
                                Kirim Test
                            </button>
                        </div>
                    </form>
                    @if(session('mail_error'))
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ session('mail_error') }}</p>
                    @endif
                    @if(session('mail_success'))
                        <p class="mt-1 text-xs text-green-600 dark:text-green-400">{{ session('mail_success') }}</p>
                    @endif
                </div>
            </div>

            {{-- QUEUE --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">📋 Queue</h3>
                    <span class="text-sm {{ $status['queue']['status'] == 'normal' ? 'text-green-500' : ($status['queue']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $status['queue']['status'] == 'normal' ? '🟢' : ($status['queue']['status'] == 'warning' ? '🟡' : '🔴') }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">Driver:</span> <span class="font-semibold">{{ $status['queue']['driver'] }}</span></p>
                    @if($status['queue']['driver'] === 'database')
                        <p><span class="text-gray-500">Pending Jobs:</span> <span class="font-semibold">{{ $status['queue']['pending_jobs'] ?? 0 }}</span></p>
                        <p><span class="text-gray-500">Failed Jobs:</span> 
                            <span class="font-semibold {{ ($status['queue']['failed_jobs'] ?? 0) > 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $status['queue']['failed_jobs'] ?? 0 }}
                            </span>
                        </p>
                        <p><span class="text-gray-500">Jobs Table:</span> {{ isset($status['queue']['table_exists']) && $status['queue']['table_exists'] ? '✅' : '❌' }}</p>
                        <p><span class="text-gray-500">Failed Table:</span> {{ isset($status['queue']['failed_table_exists']) && $status['queue']['failed_table_exists'] ? '✅' : '❌' }}</p>
                    @endif
                </div>
            </div>

            {{-- LOGS --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">📝 Logs</h3>
                    <span class="text-sm {{ $status['logs']['status'] == 'normal' ? 'text-green-500' : ($status['logs']['status'] == 'warning' ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ $status['logs']['status'] == 'normal' ? '🟢' : ($status['logs']['status'] == 'warning' ? '🟡' : '🔴') }}
                    </span>
                </div>
                <div class="mt-2 text-sm space-y-0.5">
                    <p><span class="text-gray-500">Exists:</span> {{ $status['logs']['exists'] ? '✅' : '❌' }}</p>
                    <p><span class="text-gray-500">Size:</span> <span class="font-semibold">{{ $status['logs']['size'] }}</span></p>
                    <p><span class="text-gray-500">Last Modified:</span> {{ $status['logs']['last_modified'] ?? 'N/A' }}</p>
                    <p><span class="text-gray-500">Recent Errors:</span> 
                        <span class="font-semibold {{ $status['logs']['recent_errors'] > 0 ? 'text-red-500' : 'text-green-500' }}">
                            {{ $status['logs']['recent_errors'] }}
                        </span>
                        <span class="text-xs text-gray-400">(dari 2000 baris terakhir)</span>
                    </p>
                    <p><span class="text-gray-500">Warnings:</span> <span class="font-semibold">{{ $status['logs']['warning_count'] }}</span></p>
                    <p><span class="text-gray-500">Info:</span> <span class="font-semibold">{{ $status['logs']['info_count'] }}</span></p>
                    @if(count($status['logs']['last_errors']) > 0)
                        <details>
                            <summary class="text-xs text-blue-500 cursor-pointer">📂 Lihat error terakhir ({{ count($status['logs']['last_errors']) }})</summary>
                            <div class="mt-1 max-h-32 overflow-y-auto text-xs bg-red-50 dark:bg-red-900/10 p-2 rounded border border-red-200 dark:border-red-800">
                                @foreach($status['logs']['last_errors'] as $error)
                                    <pre class="text-red-700 dark:text-red-300 whitespace-pre-wrap">{{ $error }}</pre>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            </div>
        @else
            {{-- Quick Check: tampilkan tombol Deep Check --}}
            <div class="col-span-1 sm:col-span-2 xl:col-span-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-microscope text-2xl mb-2 block"></i>
                    Untuk detail Upload, Image, QR, Mail, Queue, dan Logs, jalankan <strong>Deep Check</strong>.
                </p>
                <a href="{{ route('owner.system.health', ['deep' => 1]) }}" 
                   class="mt-3 inline-block px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition">
                    <i class="fas fa-play mr-1"></i> Jalankan Deep Check
                </a>
            </div>
        @endif

        {{-- PHP EXTENSIONS (Full Width) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 col-span-1 sm:col-span-2 xl:col-span-3 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 dark:text-white">🧩 PHP Extensions</h3>
                <span class="text-sm {{ $status['extensions']['status'] == 'normal' ? 'text-green-500' : 'text-red-500' }}">
                    {{ $status['extensions']['status'] == 'normal' ? '🟢' : '🔴' }} 
                    ({{ $status['extensions']['loaded'] ?? 0 }}/{{ $status['extensions']['total'] ?? 0 }})
                </span>
            </div>
            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                @foreach($status['extensions']['required'] as $ext => $info)
                    <div class="flex items-center gap-1 p-1.5 rounded border
                        {{ $info['loaded'] ? 'border-green-200 bg-green-50 dark:bg-green-900/10 dark:border-green-800' : 'border-red-200 bg-red-50 dark:bg-red-900/10 dark:border-red-800' }}">
                        <span>{{ $info['status'] }}</span>
                        <span class="font-medium">{{ $info['name'] }}</span>
                    </div>
                @endforeach
            </div>
            @if(count($status['extensions']['optional']) > 0)
                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                    @foreach($status['extensions']['optional'] as $ext => $info)
                        <div class="flex items-center gap-1 p-1.5 rounded border
                            {{ $info['loaded'] ? 'border-blue-200 bg-blue-50 dark:bg-blue-900/10 dark:border-blue-800' : 'border-gray-200 bg-gray-50 dark:bg-gray-700/30 dark:border-gray-600' }}">
                            <span>{{ $info['status'] }}</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $info['name'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-1">⚪ = Opsional</p>
            @endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="text-xs text-gray-400 dark:text-gray-500 text-center border-t border-gray-200 dark:border-gray-700 pt-4">
        <p>🛡️ Informasi ini bersifat internal dan hanya untuk Owner. Jangan bagikan informasi sensitif.</p>
        <p class="mt-1">© {{ date('Y') }} UNICODE — <span id="live-time"></span></p>
    </div>
</div>

<script>
    // Live clock (no auto-refresh)
    document.addEventListener('DOMContentLoaded', function() {
        function updateClock() {
            const now = new Date();
            document.getElementById('live-time').textContent = now.toLocaleTimeString('id-ID');
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>
@endsection