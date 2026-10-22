<?php

namespace App\Http\Middleware;

use App\Services\OrganizationContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetOrganizationContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        // ── GUARD: jika tabel belum ada (misal saat test tanpa migrate), skip ──
        if (! Schema::hasTable('organizations') || ! Schema::hasTable('organization_memberships')) {
            return $next($request);
        }

        // ── Tentukan kandidat organisasi ──
        $candidateId = $request->query('org_id')
            ?? OrganizationContext::id()
            ?? optional($user->activeOrganizations()->first())->id;

        if (! $candidateId) {
            return $next($request);
        }

        // ── Validasi keanggotaan AKTIF (role apa pun) atau platform admin ──
        $isMember = $user->organizationMemberships()
            ->where('organization_id', $candidateId)
            ->where('membership_status', 'active')
            ->exists();

        if ($isMember || $user->isPlatformAdmin()) {
            OrganizationContext::set($candidateId);
        }

        return $next($request);
    }
}