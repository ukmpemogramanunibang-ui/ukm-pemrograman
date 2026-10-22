<?php

/**
 * Global Helper Functions — App\Helpers
 * ------------------------------------------------------------
 * File ini di-autoload via composer.json (files).
 * Tidak dalam namespace → fungsi langsung global.
 */

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (! function_exists('effective_user')) {
    /**
     * Mengembalikan user efektif:
     * - Jika sedang impersonate → user target
     * - Jika tidak → user login
     */
    function effective_user(): ?User
    {
        $targetId = null;

        if (session('impersonate_active')) {
            $targetId = session('impersonate_target_id');
        }

        if (! $targetId) {
            $targetId = session('impersonated_user_id')
                ?? session('impersonate_user_id')
                ?? session('explore_user_id')
                ?? session('target_user_id');
        }

        if ($targetId && $u = User::find($targetId)) {
            return $u;
        }

        return Auth::user();
    }
}

if (! function_exists('is_impersonating')) {
    /**
     * Cek apakah user sedang dalam mode impersonate.
     */
    function is_impersonating(): bool
    {
        if (session('impersonate_active')) return true;
        if (request()->routeIs('owner.impersonate.*')) return true;

        foreach ([
            'impersonating', 'impersonate', 'impersonation',
            'impersonated_user_id', 'impersonate_user_id',
            'explore_user_id', 'target_user_id', 'impersonator_id',
        ] as $key) {
            if (session()->has($key)) return true;
        }
        return false;
    }
}