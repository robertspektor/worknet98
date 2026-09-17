<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class ShipmentPlanned implements Condition
{
    public function isMetBy(CaseState $state): bool
    {
        return $state->shipment()?->isPlanned() ?? false;
    }
}
