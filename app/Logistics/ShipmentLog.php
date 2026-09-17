<?php

namespace App\Logistics;

use App\Models\Branch;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShipmentLog
{
    private const DELIVERED_VISIBLE_REAL_HOURS = 24;

    /**
     * @return Collection<int, Shipment>
     */
    public function recentOf(Branch $branch): Collection
    {
        return Shipment::query()
            ->whereBelongsTo($branch)
            ->where(fn (Builder $query) => $query
                ->whereNull('delivered_at')
                ->orWhere('delivered_at', '>=', now()->subHours(self::DELIVERED_VISIBLE_REAL_HOURS)))
            ->with(['sender.company', 'recipient.company', 'driver.person', 'plannedByPosition.person', 'plannedBy.position', 'dispatchCase.customer.person'])
            ->orderBy('due_date')
            ->orderBy('due_slot')
            ->get();
    }
}
