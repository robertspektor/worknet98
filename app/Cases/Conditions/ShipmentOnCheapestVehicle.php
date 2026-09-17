<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class ShipmentOnCheapestVehicle implements Condition
{
    public function isMetBy(CaseState $state): bool
    {
        $shipment = $state->shipment();

        return $shipment?->driver?->vehicle->isCheapestFor($shipment->size) ?? false;
    }
}
