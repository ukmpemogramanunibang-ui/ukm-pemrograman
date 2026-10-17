@php
if (!function_exists('role_safe_route')) {
    function role_safe_route(string $name, array $params = [], string $fallback = '#'): string {
        try {
            return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }
}

if (!function_exists('role_is_active')) {
    function role_is_active(array|string $patterns): bool {
        foreach ((array) $patterns as $pattern) {
            if (request()->routeIs($pattern)) return true;
        }
        return false;
    }
}
@endphp