<?php

namespace App\Logistics;

use App\Models\Branch;
use App\Models\Driver;
use App\Workplace\BookingWindow;
use Carbon\CarbonImmutable;

class ReachableDue
{
    public function __construct(private readonly BookingWindow $window) {}

    public function forCarrier(?Branch $carrier, ShipmentSize $size, CarbonImmutable $due): CarbonImmutable
    {
        $arrival = $carrier === null ? null : $this->earliestArrival($carrier, $size);

        while ($arrival !== null && $due->lt($arrival)) {
            $due = $due->addWeekday();
        }

        return $due;
    }

    private function earliestArrival(Branch $carrier, ShipmentSize $size): ?CarbonImmutable
    {
        $drivers = $carrier->drivers()->get()->filter(fn (Driver $driver): bool => $driver->vehicle->carries($size));

        foreach ($this->window->days() as $date) {
            foreach (Tour::cases() as $tour) {
                if ($drivers->contains(fn (Driver $driver): bool => ! $driver->isBusyOn($date, $tour))) {
                    return $tour->endsAt($date);
                }
            }
        }

        return null;
    }
}
