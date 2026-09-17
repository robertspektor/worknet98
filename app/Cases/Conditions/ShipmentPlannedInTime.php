<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class ShipmentPlannedInTime implements Condition
{
    public function isMetBy(CaseState $state): bool
    {
        return $state->shipment()?->isPlannedInTime() ?? false;
    }
}
