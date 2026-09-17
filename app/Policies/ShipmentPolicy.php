<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function plan(User $player, Shipment $shipment): bool
    {
        return $player->employment !== null && $shipment->branch_id === $player->employment->position->branch_id;
    }
}
