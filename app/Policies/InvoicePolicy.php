<?php

namespace App\Policies;

use App\Models\Financial\Invoice;
use App\Models\User;
use App\Services\OrganizationContext; 
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        $orgId = OrganizationContext::id();
        return $orgId && OrganizationContext::authorize($user, $orgId, 'officer');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        return $user->organizationMemberships()
            ->where('organization_id', $invoice->organization_id)
            ->where('membership_status', 'active')
            ->exists();
    }

    public function create(User $user): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        $orgId = OrganizationContext::id();
        return $orgId && OrganizationContext::authorize($user, $orgId, 'officer');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        return OrganizationContext::authorize($user, $invoice->organization_id, 'officer');
    }

public function delete(User $user, Invoice $invoice): bool
{
    if (in_array((int) $user->level_id, [1, 2])) return true;
    return OrganizationContext::authorize($user, $invoice->organization_id, 'officer');
}

    public function restore(User $user, Invoice $invoice): bool
    {
        return false;
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return false;
    }
}