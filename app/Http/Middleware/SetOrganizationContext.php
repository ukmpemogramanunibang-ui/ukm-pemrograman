<?php
namespace App\Http\Middleware;

use App\Services\OrganizationContext;
use Closure;

class SetOrganizationContext
{
    public function handle($request, Closure $next)
    {
        if ($request->user()) {
            // Priority: query param > session > default UNICODE
            $orgId = $request->query('org_id')
                ?? OrganizationContext::id()
                ?? optional($request->user()->activeOrganizations()->first())->id;

            if ($orgId && OrganizationContext::authorize($request->user(), $orgId)) {
                OrganizationContext::set($orgId);
            }
        }
        return $next($request);
    }
}