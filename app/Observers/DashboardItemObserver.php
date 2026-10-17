<?php

namespace App\Observers;

use App\Models\DashboardItem;
use App\Models\DashboardRevision;

class DashboardItemObserver
{
    public function created(DashboardItem $item): void
    {
        DashboardRevision::bump("created:{$item->type}:{$item->id}");
    }

    public function updated(DashboardItem $item): void
    {
        DashboardRevision::bump("updated:{$item->type}:{$item->id}");
    }

    public function deleted(DashboardItem $item): void
    {
        DashboardRevision::bump("deleted:{$item->type}:{$item->id}");
    }

    public function restored(DashboardItem $item): void
    {
        DashboardRevision::bump("restored:{$item->type}:{$item->id}");
    }

    public function forceDeleted(DashboardItem $item): void
    {
        DashboardRevision::bump("forceDeleted:{$item->type}:{$item->id}");
    }
}