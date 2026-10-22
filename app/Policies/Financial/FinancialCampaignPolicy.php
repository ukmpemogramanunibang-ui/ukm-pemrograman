<?php

namespace App\Policies\Financial;

use App\Models\User;
use App\Models\Financial\FinancialCampaign;

class FinancialCampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4], true);
    }

    public function view(User $user, FinancialCampaign $campaign): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4], true);
    }

    public function update(User $user, FinancialCampaign $campaign): bool
    {
        // Owner/superadmin/ketua selalu boleh. Pengurus hanya bila creator.
        if (in_array($user->level_id, [1, 2, 3], true)) return true;
        return $user->level_id === 4 && $campaign->created_by === $user->id;
    }

    public function delete(User $user, FinancialCampaign $campaign): bool
    {
        return in_array($user->level_id, [1, 2, 3], true);
    }

    public function activate(User $user, FinancialCampaign $campaign): bool
    {
        return $this->update($user, $campaign);
    }

    public function close(User $user, FinancialCampaign $campaign): bool
    {
        return $this->update($user, $campaign);
    }

    public function cancel(User $user, FinancialCampaign $campaign): bool
    {
        return in_array($user->level_id, [1, 2, 3], true);
    }
}