<?php

namespace App\Logistics;

use App\Game\GameClock;
use App\Models\Appointment;
use App\Models\WorkCase;

class PartAvailability
{
    public function __construct(private readonly GameClock $clock) {}

    public function isMissingFor(WorkCase $repairCase, Appointment $appointment): bool
    {
        $shipment = $repairCase->partShipment;

        if ($shipment === null) {
            return false;
        }

        return $shipment->delivered_at === null
            || $this->clock->fromReal($shipment->delivered_at)->gt($appointment->startsAt());
    }
}
