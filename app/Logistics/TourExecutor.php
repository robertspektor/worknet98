<?php

namespace App\Logistics;

use App\Cases\CaseResolver;
use App\Cases\WorkCaseStatus;
use App\Game\GameClock;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class TourExecutor
{
    public function __construct(
        private readonly GameClock $clock,
        private readonly CaseResolver $resolver,
    ) {}

    public function executeDue(): int
    {
        $now = $this->clock->now();
        $delivered = 0;

        Shipment::query()
            ->whereNull('delivered_at')
            ->whereNotNull('driver_id')
            ->whereDate('tour_date', '<=', $now->toDateString())
            ->lazyById()
            ->filter(fn (Shipment $shipment): bool => $shipment->plannedArrival()?->lte($now) ?? false)
            ->each(function (Shipment $shipment) use (&$delivered): void {
                if ($this->deliver($shipment)) {
                    $delivered++;
                }
            });

        return $delivered;
    }

    private function deliver(Shipment $shipment): bool
    {
        return DB::transaction(function () use ($shipment): bool {
            $isPending = Shipment::query()->whereKey($shipment->id)->whereNull('delivered_at')->lockForUpdate()->exists();
            $arrival = $shipment->plannedArrival();

            if (! $isPending || $arrival === null) {
                return false;
            }

            $shipment->update(['delivered_at' => $this->clock->toReal($arrival)]);
            $dispatchCase = $shipment->dispatchCase;

            if ($dispatchCase !== null && $dispatchCase->status === WorkCaseStatus::Open) {
                $this->resolver->resolve($dispatchCase);
            }

            return true;
        });
    }
}
