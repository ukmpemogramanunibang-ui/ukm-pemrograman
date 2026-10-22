<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\SystemHealthCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Level;

class SystemHealthController extends Controller
{
    private function authorizeOwner(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->level_id == 1,
            403,
            'Akses hanya untuk Owner.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeOwner();

        if ($request->has('refresh') || $request->has('deep')) {
            $status = $this->checkAll($request->has('deep'));
            return view('owner.system.health', compact('status'));
        }

        $status = $this->checkAll(false); // quick check
        $history = SystemHealthCheck::orderBy('checked_at', 'desc')->limit(20)->get();

        return view('owner.system.health', compact('status', 'history'));
    }

    /**
     * Periksa semua komponen sistem.
     * @param bool $deep - apakah menjalankan pemeriksaan berat (QR, upload, mail, dll)
     */
    private function checkAll(bool $deep = false)
    {
        $startTime = microtime(true);

        $checks = [
            'laravel' => $this->checkLaravelApp(),
            'server'  => $this->checkServer(),
            'php'     => $this->checkPHP(),
            'database'=> $this->checkDatabase(),
            'disk'    => $this->checkDiskUsage(),
            'storage' => $this->checkStorage(),
            'cache'   => $this->checkCache(),
            'session' => $this->checkSession(),
            'security'=> $this->checkSecurity(),
            'roles'   => $this->checkRoles(),
            'routes'  => $this->checkRoutes(),
            'extensions' => $this->checkExtensions(),
            'performance' => $this->checkPerformance(),
        ];

        // Hanya jalankan yang berat jika deep check
        if ($deep) {
            $checks['upload'] = $this->checkUpload();
            $checks['image']  = $this->checkImageProcessing();
            $checks['qr']     = $this->checkQRCode();
            $checks['mail']   = $this->checkMail();
            $checks['queue']  = $this->checkQueue();
            $checks['logs']   = $this->checkLogs();
        } else {
            // Quick check: status ringan
            $checks['upload'] = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
            $checks['image']  = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
            $checks['qr']     = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
            $checks['mail']   = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
            $checks['queue']  = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
            $checks['logs']   = ['status' => 'unknown', 'message' => 'Aktifkan Deep Check untuk detail'];
        }

        // Hitung overall status
        $overall = 'normal';
        $failedComponents = [];
        $warningComponents = [];
        $errorCount = 0;
        $warningCount = 0;
        $passedCount = 0;

        foreach ($checks as $key => $data) {
            $status = $data['status'] ?? 'unknown';
            if ($status === 'error') {
                $overall = 'error';
                $failedComponents[] = $key;
                $errorCount++;
            } elseif ($status === 'warning') {
                if ($overall !== 'error') {
                    $overall = 'warning';
                }
                $warningComponents[] = $key;
                $warningCount++;
            } elseif ($status === 'normal') {
                $passedCount++;
            }
        }

        $totalChecks = count($checks);
        $passedChecks = $totalChecks - count($failedComponents) - $warningCount;

        // Simpan history ke database
        $durationMs = round((microtime(true) - $startTime) * 1000);
        $this->saveHealthHistory($overall, $totalChecks, $passedChecks, $warningCount, $errorCount, $durationMs);

        return array_merge($checks, [
            'system_time' => Carbon::now()->toDateTimeString(),
            'overall' => $overall,
            'failed_components' => $failedComponents,
            'warning_components' => $warningComponents,
            'total_checks' => $totalChecks,
            'passed_checks' => $passedChecks,
            'is_deep' => $deep,
        ]);
    }

    private function saveHealthHistory($status, $total, $passed, $warnings, $errors, $duration)
    {
        try {
            SystemHealthCheck::create([
                'status' => $status,
                'total_checks' => $total,
                'passed_checks' => $passed,
                'warning_count' => $warnings,
                'error_count' => $errors,
                'checked_at' => now(),
                'duration_ms' => $duration,
                'details' => null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Gagal menyimpan health history: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 1. LARAVEL APPLICATION
    // ============================================================
    private function checkLaravelApp()
    {
        try {
            $cached = [
                'config' => app()->configurationIsCached(),
                'route'  => app()->routesAreCached(),
                'view'   => false, // tidak ada method resmi
                'event'  => app()->eventsAreCached(),
            ];

            $migrationStatus = $this->checkMigrations();

            return [
                'status' => $migrationStatus['status'] === 'error' ? 'error' : 'normal',
                'version' => app()->version(),
                'name' => config('app.name'),
                'env' => app()->environment(),
                'debug' => config('app.debug'),
                'maintenance' => app()->isDownForMaintenance(),
                'cached' => $cached,
                'migrations' => $migrationStatus,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'version' => 'N/A',
                'name' => 'N/A',
                'env' => 'N/A',
                'debug' => false,
                'maintenance' => false,
                'cached' => ['config' => false, 'route' => false, 'view' => false, 'event' => false],
                'migrations' => ['status' => 'error', 'message' => $e->getMessage()],
            ];
        }
    }

    private function checkMigrations()
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return ['status' => 'warning', 'message' => 'Tabel migrations tidak ditemukan'];
            }
            $migrated = DB::table('migrations')->count();
            $pending = $this->getPendingMigrations();
            return [
                'status' => count($pending) > 0 ? 'warning' : 'normal',
                'total' => $migrated,
                'pending' => count($pending),
                'pending_list' => $pending,
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getPendingMigrations()
    {
        $migrated = DB::table('migrations')->pluck('migration')->toArray();
        $files = File::glob(database_path('migrations/*.php'));
        $pending = [];
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if (!in_array($name, $migrated)) {
                $pending[] = $name;
            }
        }
        return $pending;
    }

    // ============================================================
    // 2. SERVER
    // ============================================================
    private function checkServer()
    {
        $loadAvg = function_exists('sys_getloadavg') ? sys_getloadavg() : null;
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->convertToBytes(ini_get('memory_limit'));
        $memoryPercent = $memoryLimit ? round(($memoryUsage / $memoryLimit) * 100) : 0;

        $status = 'normal';
        $warnings = [];
        if ($memoryPercent > 90) {
            $status = 'error';
            $warnings[] = "Memory usage tinggi ({$memoryPercent}%)";
        } elseif ($memoryPercent > 75) {
            $status = 'warning';
            $warnings[] = "Memory usage cukup tinggi ({$memoryPercent}%)";
        }

        if ($memoryLimit && $memoryLimit < 128 * 1024 * 1024) {
            if ($status !== 'error') $status = 'warning';
            $warnings[] = "Memory limit rendah (" . ini_get('memory_limit') . ")";
        }

        return [
            'status' => $status,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'localhost',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'server_port' => $_SERVER['SERVER_PORT'] ?? '80',
            'os' => PHP_OS,
            'memory_usage' => $this->formatBytes($memoryUsage),
            'memory_limit' => ini_get('memory_limit'),
            'memory_percent' => $memoryPercent,
            'max_execution' => ini_get('max_execution_time') . 's',
            'max_upload' => ini_get('upload_max_filesize'),
            'max_post' => ini_get('post_max_size'),
            'max_input' => ini_get('max_input_vars'),
            'load_avg' => $loadAvg ? implode(', ', array_map(function($v) { return number_format($v, 2); }, $loadAvg)) : 'N/A',
            'warnings' => $warnings,
        ];
    }

    private function convertToBytes($size)
    {
        if (is_numeric($size)) return (int) $size;
        $unit = strtolower(substr($size, -1));
        $value = (int) substr($size, 0, -1);
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return (int) $size;
        }
    }

    // ============================================================
    // 3. PHP
    // ============================================================
    private function checkPHP()
    {
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'gd', 'fileinfo', 'json', 'ctype', 'tokenizer', 'xml', 'curl'];
        $missing = [];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        $status = 'normal';
        if (count($missing) > 0) {
            $status = 'error';
        }

        return [
            'status' => $status,
            'version' => phpversion(),
            'architecture' => PHP_INT_SIZE * 8 . '-bit',
            'sapi' => php_sapi_name(),
            'timezone' => date_default_timezone_get(),
            'display_errors' => ini_get('display_errors'),
            'error_reporting' => $this->getErrorReportingLevel(),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'missing_extensions' => $missing,
        ];
    }

    // ============================================================
    // 4. DATABASE
    // ============================================================
    private function checkDatabase()
    {
        $result = [
            'status' => 'error',
            'connection' => false,
            'name' => config('database.connections.mysql.database'),
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'tables_count' => 0,
            'query_time' => 0,
            'migrations' => 'none',
            'size' => 'N/A',
            'largest_table' => null,
            'total_rows' => 0,
        ];

        try {
            $start = microtime(true);
            $pdo = DB::connection()->getPdo();
            $result['connection'] = true;
            $result['query_time'] = round((microtime(true) - $start) * 1000, 2);
            $result['status'] = 'normal';

            $tables = DB::select('SHOW TABLES');
            $result['tables_count'] = count($tables);

            $dbName = config('database.connections.mysql.database');

            // Database size
            $sizeQuery = DB::select("SELECT SUM(data_length + index_length) AS size 
                                     FROM information_schema.tables 
                                     WHERE table_schema = ?", [$dbName]);
            if (isset($sizeQuery[0]->size)) {
                $result['size'] = $this->formatBytes($sizeQuery[0]->size);
            }

            // Total rows
            try {
                $quotedDb = DB::connection()->getPdo()->quote($dbName);
                $rowQuery = DB::select("SELECT SUM(table_rows) AS rows 
                                        FROM information_schema.tables 
                                        WHERE table_schema = {$quotedDb}");
                if (isset($rowQuery[0]->rows) && $rowQuery[0]->rows !== null) {
                    $result['total_rows'] = number_format($rowQuery[0]->rows);
                } else {
                    $result['total_rows'] = 'N/A';
                }
            } catch (\Exception $e) {
                $result['total_rows'] = 'N/A';
            }

            // Largest table
            $largeTable = DB::select("SELECT TABLE_NAME as table_name, (data_length + index_length) AS size 
                                     FROM information_schema.tables 
                                     WHERE table_schema = ? 
                                     ORDER BY size DESC LIMIT 1", [$dbName]);
            if (!empty($largeTable)) {
                $name = $largeTable[0]->table_name ?? $largeTable[0]->TABLE_NAME ?? 'Unknown';
                $size = $largeTable[0]->size ?? 0;
                $result['largest_table'] = [
                    'name' => $name,
                    'size' => $this->formatBytes($size),
                ];
            }

            $migrated = Schema::hasTable('migrations');
            $result['migrations'] = $migrated ? '✅ Migration tables exist' : '⚠️ No migrations table found';

        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            $result['connection'] = false;
        }

        return $result;
    }

    // ============================================================
    // 5. DISK USAGE
    // ============================================================
    private function checkDiskUsage()
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        $percent = ($total > 0) ? round(($used / $total) * 100) : 0;

        $status = 'normal';
        $message = 'Normal';
        $color = 'green';
        if ($percent > 85) {
            $status = 'error';
            $message = 'Kritis (>85%)';
            $color = 'red';
        } elseif ($percent > 70) {
            $status = 'warning';
            $message = 'Perhatian (>70%)';
            $color = 'yellow';
        }

        return [
            'status' => $status,
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percent' => $percent,
            'message' => $message,
            'color' => $color,
        ];
    }

    // ============================================================
    // 6. STORAGE
    // ============================================================
    private function checkStorage()
    {
        $storagePath = storage_path();
        $publicStorage = storage_path('app/public');
        $symlinkPath = public_path('storage');

        $result = [
            'status' => 'normal',
            'storage_exists' => is_dir($storagePath),
            'storage_writable' => is_writable($storagePath),
            'app_public_exists' => is_dir($publicStorage),
            'app_public_writable' => is_writable($publicStorage),
            'symlink_exists' => is_link($symlinkPath),
            'symlink_valid' => false,
            'temp_file_writable' => false,
            'disk_free_space' => $this->formatBytes(disk_free_space($storagePath)),
            'disk_total_space' => $this->formatBytes(disk_total_space($storagePath)),
        ];

        if ($result['symlink_exists']) {
            $target = readlink($symlinkPath);
            $result['symlink_target'] = $target;
            $result['symlink_valid'] = file_exists($symlinkPath) && is_dir($symlinkPath);
        }

        try {
            $testFile = $storagePath . '/tmp_test_' . uniqid() . '.txt';
            File::put($testFile, 'testing ' . now());
            $result['temp_file_writable'] = File::exists($testFile);
            File::delete($testFile);
        } catch (\Exception $e) {
            $result['temp_file_error'] = $e->getMessage();
        }

        if (!$result['storage_writable'] || !$result['app_public_writable']) {
            $result['status'] = 'error';
        } elseif (!$result['symlink_valid']) {
            $result['status'] = 'warning';
        }

        return $result;
    }

    // ============================================================
    // 7. UPLOAD TEST
    // ============================================================
    private function checkUpload()
    {
        $result = [
            'status' => 'normal',
            'upload_max' => ini_get('upload_max_filesize'),
            'post_max' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'storage_writable' => is_writable(storage_path('app/public')),
            'upload_test' => 'N/A',
        ];

        try {
            $content = 'test upload ' . now();
            $path = 'health_upload_test.txt';
            Storage::disk('public')->put($path, $content);
            $exists = Storage::disk('public')->exists($path);
            if ($exists) {
                $read = Storage::disk('public')->get($path);
                if ($read === $content) {
                    Storage::disk('public')->delete($path);
                    $result['upload_test'] = '✅ Success';
                } else {
                    $result['upload_test'] = '❌ Read failed';
                    $result['status'] = 'error';
                }
            } else {
                $result['upload_test'] = '❌ Write failed';
                $result['status'] = 'error';
            }
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['upload_test'] = '❌ ' . $e->getMessage();
        }

        return $result;
    }

    // ============================================================
    // 8. IMAGE PROCESSING
    // ============================================================
    private function checkImageProcessing()
    {
        $gd = extension_loaded('gd');
        $info = [];

        if ($gd) {
            $gdInfo = gd_info();
            $info['gd_version'] = $gdInfo['GD Version'] ?? 'unknown';
            $info['jpeg'] = function_exists('imagecreatefromjpeg');
            $info['png'] = function_exists('imagecreatefrompng');
            $info['webp'] = function_exists('imagecreatefromwebp');
            $info['gif'] = function_exists('imagecreatefromgif');
        }

        return [
            'status' => $gd ? 'normal' : 'error',
            'gd_loaded' => $gd,
            'info' => $info,
            'fileinfo' => extension_loaded('fileinfo'),
            'imagick' => extension_loaded('imagick'),
        ];
    }

    // ============================================================
    // 9. QR CODE
    // ============================================================
    private function checkQRCode()
    {
        $endroidInstalled = class_exists('Endroid\QrCode\QrCode');
        $gd = extension_loaded('gd');

        $result = [
            'status' => 'normal',
            'endroid_installed' => $endroidInstalled,
            'gd_available' => $gd,
            'test_generate' => false,
        ];

        if ($endroidInstalled && $gd) {
            try {
                $qr = new \Endroid\QrCode\QrCode('test');
                $qr->setSize(100);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $resultData = $writer->write($qr);
                $result['test_generate'] = $resultData->getString() !== '';
            } catch (\Exception $e) {
                $result['status'] = 'error';
                $result['test_generate'] = false;
                $result['error'] = $e->getMessage();
            }
        } else {
            $result['status'] = $endroidInstalled ? 'warning' : 'error';
        }

        return $result;
    }

    // ============================================================
    // 10. CACHE
    // ============================================================
    private function checkCache()
    {
        $result = [
            'status' => 'normal',
            'driver' => config('cache.default'),
            'connected' => false,
            'can_store' => false,
            'can_retrieve' => false,
            'store_driver' => config('cache.stores.' . config('cache.default') . '.driver') ?? 'unknown',
        ];

        try {
            $key = 'health_check_' . uniqid();
            $value = 'test_' . time();

            Cache::put($key, $value, 1);
            $result['can_store'] = true;

            $retrieved = Cache::get($key);
            if ($retrieved === $value) {
                $result['can_retrieve'] = true;
                $result['connected'] = true;
            }

            Cache::forget($key);
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            $result['connected'] = false;
        }

        return $result;
    }

    // ============================================================
    // 11. SESSION
    // ============================================================
    private function checkSession()
    {
        $result = [
            'status' => 'normal',
            'driver' => config('session.driver'),
            'is_active' => false,
            'lifetime' => config('session.lifetime'),
        ];

        try {
            $testKey = '_health_test_';
            session([$testKey => 'ok']);
            $value = session($testKey);
            session()->forget($testKey);

            $result['is_active'] = ($value === 'ok');
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    // ============================================================
    // 12. MAIL
    // ============================================================
    private function checkMail()
    {
        $result = [
            'status' => 'normal',
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host') ?? 'N/A',
            'port' => config('mail.mailers.smtp.port') ?? 'N/A',
            'encryption' => config('mail.mailers.smtp.encryption') ?? 'N/A',
            'from' => config('mail.from.address') ?? 'N/A',
            'from_name' => config('mail.from.name') ?? 'N/A',
            'configured' => false,
            'can_send' => false,
            'test_result' => null,
        ];

        if (config('mail.default') && config('mail.mailers.smtp.host')) {
            $result['configured'] = true;
        }

        return $result;
    }

    // ============================================================
    // 13. QUEUE
    // ============================================================
    private function checkQueue()
    {
        $result = [
            'status' => 'normal',
            'driver' => config('queue.default'),
            'available' => false,
            'pending_jobs' => 0,
            'failed_jobs' => 0,
            'table_exists' => false,
            'failed_table_exists' => false,
        ];

        $driver = config('queue.default');
        if ($driver) {
            $result['available'] = true;
        }

        if ($driver === 'database') {
            $result['table_exists'] = Schema::hasTable('jobs');
            $result['failed_table_exists'] = Schema::hasTable('failed_jobs');

            try {
                if ($result['table_exists']) {
                    $result['pending_jobs'] = DB::table('jobs')->count();
                }
                if ($result['failed_table_exists']) {
                    $result['failed_jobs'] = DB::table('failed_jobs')->count();
                }
            } catch (\Exception $e) {
                $result['status'] = 'warning';
                $result['error'] = $e->getMessage();
            }

            if ($result['failed_jobs'] > 0) {
                $result['status'] = $result['failed_jobs'] > 5 ? 'error' : 'warning';
            }
        }

        return $result;
    }

    // ============================================================
    // 14. ROUTES (Registered Routes, not health check)
    // ============================================================
    private function checkRoutes()
    {
        $coreRoutes = [
            'landing' => 'landing',
            'login' => 'login',
            'owner.dashboard' => 'owner.dashboard',
            'owner.management.dashboard' => 'owner.management.dashboard',
            'owner.rooms.index' => 'owner.rooms.index',
            'owner.kelompok.index' => 'owner.kelompok.index',
            'owner.divisi.index' => 'owner.divisi.index',
            'owner.system.health' => 'owner.system.health',
        ];

        $result = [
            'status' => 'normal',
            'total_registered' => count(Route::getRoutes()),
            'core_routes' => [],
            'failed_routes' => 0,
        ];

        foreach ($coreRoutes as $name => $displayName) {
            try {
                route($name);
                $result['core_routes'][$displayName] = ['registered' => true, 'status' => '✅'];
            } catch (\Exception $e) {
                $result['core_routes'][$displayName] = ['registered' => false, 'status' => '❌'];
                $result['failed_routes']++;
                $result['status'] = 'warning';
            }
        }

        return $result;
    }

    // ============================================================
    // 15. SECURITY
    // ============================================================
    private function checkSecurity()
    {
        $env = app()->environment();
        $debug = config('app.debug');
        $key = config('app.key');

        // CSRF detection
        $csrfEnabled = false;
        $middlewareGroups = config('app.middleware_groups', []);
        if (isset($middlewareGroups['web'])) {
            foreach ($middlewareGroups['web'] as $middleware) {
                if (is_string($middleware) && str_contains($middleware, 'VerifyCsrfToken')) {
                    $csrfEnabled = true;
                    break;
                }
            }
        }
        // Fallback: cek apakah middleware VerifyCsrfToken terdaftar
        if (!$csrfEnabled && class_exists(\App\Http\Middleware\VerifyCsrfToken::class)) {
            $csrfEnabled = true; // default Laravel
        }

        $result = [
            'status' => 'safe',
            'environment' => $env,
            'debug_mode' => $debug ? 'ON' : 'OFF',
            'debug_status' => $debug ? ($env === 'production' ? '🔴 WARNING' : '🟢 OK') : '🟢 OK',
            'app_key_status' => empty($key) || !str_starts_with($key, 'base64:') ? '🔴 TIDAK ADA' : '🟢 TERSEDIA',
            'https' => request()->secure(),
            'https_status' => request()->secure() ? '🟢 AKTIF' : '🟡 TIDAK AKTIF',
            'allowed_hosts' => config('app.allowed_hosts') ?? [],
            'session_secure' => config('session.secure'),
            'session_http_only' => config('session.http_only'),
            'cors' => config('cors.paths') ? 'Configured' : 'Default',
            'rate_limiting' => 'Active (default)',
            'csrf_enabled' => $csrfEnabled,
        ];

        if ($env === 'production' && $debug) {
            $result['status'] = 'critical';
            $result['warning'] = '🔴 APP_DEBUG aktif pada environment production!';
        } elseif ($env === 'production' && !request()->secure()) {
            $result['status'] = 'warning';
            $result['warning'] = '🟡 HTTPS tidak aktif pada production.';
        } elseif (empty($key) || !str_starts_with($key, 'base64:')) {
            $result['status'] = 'critical';
            $result['warning'] = '🔴 APP_KEY tidak valid atau kosong!';
        } else {
            $result['status'] = 'safe';
        }

        return $result;
    }

    // ============================================================
    // 16. ROLES
    // ============================================================
    private function checkRoles()
    {
        $result = [
            'status' => 'normal',
            'roles' => [],
            'user_counts' => [],
            'total_roles' => 0,
        ];

        try {
            $levels = Level::all();
            $result['total_roles'] = count($levels);

            foreach ($levels as $level) {
                $count = User::where('level_id', $level->id)->count();
                $result['user_counts'][$level->name] = $count;
                $result['roles'][$level->name] = '✅';
            }

            if ($result['total_roles'] == 0) {
                $result['status'] = 'warning';
            }
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    // ============================================================
    // 17. LOGS (Recent Errors only)
    // ============================================================
    private function checkLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        $result = [
            'status' => 'normal',
            'exists' => false,
            'size' => 'N/A',
            'last_errors' => [],
            'recent_errors' => 0,
            'warning_count' => 0,
            'info_count' => 0,
            'last_modified' => 'N/A',
        ];

        if (File::exists($logPath)) {
            $result['exists'] = true;
            $result['size'] = $this->formatBytes(File::size($logPath));
            $result['last_modified'] = Carbon::createFromTimestamp(File::lastModified($logPath))->diffForHumans();

            try {
                $lines = $this->tailFile($logPath, 2000);
                $errors = [];
                $warnings = [];
                $infos = [];

                foreach ($lines as $line) {
                    if (str_contains($line, 'ERROR')) {
                        $errors[] = $line;
                    } elseif (str_contains($line, 'WARNING')) {
                        $warnings[] = $line;
                    } elseif (str_contains($line, 'INFO')) {
                        $infos[] = $line;
                    }
                }

                $result['recent_errors'] = count($errors);
                $result['warning_count'] = count($warnings);
                $result['info_count'] = count($infos);
                $result['last_errors'] = array_slice($errors, -5);

                if ($result['recent_errors'] > 5) {
                    $result['status'] = 'error';
                } elseif ($result['recent_errors'] > 0) {
                    $result['status'] = 'warning';
                }
            } catch (\Exception $e) {
                $result['last_errors'] = ['Gagal membaca log: ' . $e->getMessage()];
                $result['status'] = 'error';
            }
        }

        return $result;
    }

    private function tailFile($filePath, $lines = 1000)
    {
        if (!File::exists($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        $bufferSize = 8192;
        $content = '';
        $totalLines = 0;

        fseek($handle, 0, SEEK_END);
        $pos = ftell($handle);

        while ($pos > 0 && $totalLines < $lines) {
            $readSize = min($bufferSize, $pos);
            $pos -= $readSize;
            fseek($handle, $pos);
            $chunk = fread($handle, $readSize);
            $content = $chunk . $content;
            $totalLines = substr_count($content, "\n");
        }

        fclose($handle);

        $allLines = explode("\n", $content);
        return array_slice($allLines, -$lines);
    }

    // ============================================================
    // 18. PHP EXTENSIONS
    // ============================================================
    private function checkExtensions()
    {
        $required = [
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'mbstring' => 'Mbstring',
            'openssl' => 'OpenSSL',
            'gd' => 'GD (Image)',
            'fileinfo' => 'Fileinfo',
            'json' => 'JSON',
            'ctype' => 'Ctype',
            'tokenizer' => 'Tokenizer',
            'xml' => 'XML',
            'curl' => 'cURL',
        ];

        $optional = [
            'bcmath' => 'BCMath',
            'exif' => 'EXIF',
            'zip' => 'Zip',
            'intl' => 'Intl',
            'redis' => 'Redis',
            'imagick' => 'Imagick',
        ];

        $result = ['status' => 'normal', 'loaded' => 0, 'total' => 0];

        foreach ($required as $ext => $label) {
            $loaded = extension_loaded($ext);
            $result['required'][$ext] = [
                'name' => $label,
                'loaded' => $loaded,
                'status' => $loaded ? '✅' : '❌',
            ];
            $result['total']++;
            if ($loaded) $result['loaded']++;
            if (!$loaded) $result['status'] = 'error';
        }

        foreach ($optional as $ext => $label) {
            $loaded = extension_loaded($ext);
            $result['optional'][$ext] = [
                'name' => $label,
                'loaded' => $loaded,
                'status' => $loaded ? '✅' : '⚪',
            ];
            $result['total']++;
            if ($loaded) $result['loaded']++;
        }

        return $result;
    }

    // ============================================================
    // 19. PERFORMANCE
    // ============================================================
    private function checkPerformance()
    {
        $start = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $memoryStart = memory_get_usage();

        try {
            DB::select('SELECT 1');
            $dbTime = round((microtime(true) - $start) * 1000, 2);
        } catch (\Exception $e) {
            $dbTime = -1;
        }

        $memoryEnd = memory_get_usage();
        $memoryUsed = $memoryEnd - $memoryStart;

        return [
            'status' => 'normal',
            'response_time' => round((microtime(true) - $start) * 1000, 2),
            'db_query_time' => $dbTime . ' ms',
            'memory_used' => $this->formatBytes($memoryUsed),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
        ];
    }

    // ============================================================
    // 20. HELPER
    // ============================================================
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === false || $bytes === 0 || $bytes === null) return 'N/A';
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }

    private function getErrorReportingLevel()
    {
        $level = error_reporting();
        $levels = [];
        if ($level & E_ERROR) $levels[] = 'E_ERROR';
        if ($level & E_WARNING) $levels[] = 'E_WARNING';
        if ($level & E_PARSE) $levels[] = 'E_PARSE';
        if ($level & E_NOTICE) $levels[] = 'E_NOTICE';
        if ($level & E_DEPRECATED) $levels[] = 'E_DEPRECATED';
        if ($level & E_STRICT) $levels[] = 'E_STRICT';
        return implode(' | ', $levels) . ' (' . $level . ')';
    }

    // ============================================================
    // 21. MAIL TEST ACTION
    // ============================================================
    public function sendTestMail(Request $request)
    {
        $this->authorizeOwner();

        $email = $request->input('email', auth()->user()->email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('owner.system.health', ['refresh' => 1])
                ->with('mail_error', '❌ Alamat email tidak valid.');
        }

        try {
            Log::info('Mengirim test email', [
                'to' => $email,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.' . config('mail.default') . '.host') ?? 'unknown',
            ]);

            Mail::raw('Test email dari System Health UNICODE @ ' . now(), function ($message) use ($email) {
                $message->to($email)
                    ->subject('✅ Test Health - ' . now()->format('H:i:s'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Test email berhasil dikirim ke ' . $email);

            return redirect()->route('owner.system.health', ['refresh' => 1])
                ->with('mail_success', '✅ Email berhasil dikirim ke ' . $email);

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            Log::error('Gagal mengirim test email', [
                'to' => $email,
                'error' => $errorMsg,
            ]);

            $friendlyMsg = '❌ Gagal mengirim email: ' . $errorMsg;

            if (str_contains($errorMsg, 'Too many emails')) {
                $friendlyMsg = '⚠️ Rate limit terpenuhi. Tunggu beberapa saat sebelum mencoba lagi.';
            } elseif (str_contains($errorMsg, 'Connection refused')) {
                $friendlyMsg = '❌ Server SMTP tidak merespon. Periksa koneksi internet dan konfigurasi mail.';
            } elseif (str_contains($errorMsg, 'Authentication')) {
                $friendlyMsg = '❌ Autentikasi SMTP gagal. Periksa username dan password.';
            } elseif (str_contains($errorMsg, 'Invalid address')) {
                $friendlyMsg = '❌ Alamat email tujuan tidak valid.';
            }

            return redirect()->route('owner.system.health', ['refresh' => 1])
                ->with('mail_error', $friendlyMsg);
        }
    }
}