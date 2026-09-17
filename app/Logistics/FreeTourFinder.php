<?php

namespace App\Logistics;

use App\Models\Driver;
use App\Models\Shipment;
use App\Workplace\BookingWindow;
use Illuminate\Support\Collection;

class FreeTourFinder
{
    public function __construct(private readonly BookingWindow $window) {}

    public function bestFor(Shipment $shipment): ?ShipmentPlan
    {
        $plans = $this->freePlansFor($shipment);

        return $plans->first(fn (ShipmentPlan $plan): bool => $plan->arrival()->lte($shipment->dueAt())) ?? $plans->first();
    }

    /**
     * @return Collection<int, ShipmentPlan>
     */
    private function freePlansFor(Shipment $shipment): Collection
    {
        $drivers = $this->driversFor($shipment);
        $plans = collect();

        foreach ($this->window->days() as $date) {
            foreach (Tour::cases() as $tour) {
                $driver = $drivers->first(fn (Driver $driver): bool => ! $driver->isBusyOn($date, $tour) && $driver->loadOn($date, $tour) < $driver->vehicle->capacity());

                if ($driver !== null) {
                    $plans->push(new ShipmentPlan($shipment, $driver, $date, $tour));
                }
            }
        }

        return $plans;
    }

    /**
     * @return Collection<int, Driver>
     */
    private function driversFor(Shipment $shipment): Collection
    {
        return Driver::query()
            ->whereBelongsTo($shipment->branch)
            ->orderBy('id')
            ->get()
            ->filter(fn (Driver $driver): bool => $driver->vehicle->carries($shipment->size))
            ->sortBy(fn (Driver $driver): int => $driver->vehicle->isCheapestFor($shipment->size) ? 0 : 1)
            ->values();
    }
}
