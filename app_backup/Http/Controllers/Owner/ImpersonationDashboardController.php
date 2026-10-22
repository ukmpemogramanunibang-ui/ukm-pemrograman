<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpersonationDashboardController extends Controller
{
    /**
     * Entry point: /owner/{roleSlug}/dashboard
     * Middleware 'impersonate.require' sudah handle:
     *   - cek Owner asli
     *   - cek session impersonation
     *   - switch Auth ke target user
     */
    public function dashboard(Request $request, string $roleSlug): View
    {
        $targetUser = auth()->user();

        // Forward ke controller dashboard sesuai role
        return match ($roleSlug) {
            'anggota'    => $this->forward(
                $request,
                \App\Http\Controllers\Anggota\DashboardController::class,
                'index'
            ),
            'ketua'      => $this->forward(
                $request,
                \App\Http\Controllers\KetuaDashboardController::class,
                'index'
            ),
            'pengurus'   => view('pengurus.dashboard', [
                'user' => $targetUser,
            ]),
            'superadmin' => view('superadmin.dashboard', [
                'user' => $targetUser,
            ]),
            'pengunjung' => view('explore', [
                'user' => $targetUser,
            ]),
            default => abort(404, 'Role tidak dikenali.'),
        };
    }

    /**
     * Halaman profil user target — render sebagai mereka.
     */
    public function profile(Request $request, string $roleSlug): View
    {
        $targetUser = auth()->user();

        return view('owner.impersonate.profile', [
            'user'     => $targetUser,
            'roleSlug' => $roleSlug,
        ]);
    }
protected function targetUser(): ?\App\Models\User
{
    $imp = session('impersonation_current');
    if (! is_array($imp) || empty($imp['target_user_id'])) return null;
    return \App\Models\User::with('level')->find($imp['target_user_id']);
}


public function poin()
{
    $target = $this->targetUser();
    abort_unless($target, 403);
    return view('owner.impersonate.poin', ['user' => $target]);
}
    /**
     * Forward request ke controller lain.
     */
    private function forward(Request $request, string $controllerClass, string $method)
    {
        if (! class_exists($controllerClass)) {
            return view('owner.impersonate.fallback', [
                'roleSlug' => $request->route('roleSlug'),
                'user'     => auth()->user(),
            ]);
        }

        try {
            return app()->call([app($controllerClass), $method], [
                'request' => $request,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('[Impersonation] forward gagal', [
                'controller' => $controllerClass,
                'method'     => $method,
                'error'      => $e->getMessage(),
            ]);

            return view('owner.impersonate.fallback', [
                'roleSlug' => $request->route('roleSlug'),
                'user'     => auth()->user(),
            ]);
        }
    }
}