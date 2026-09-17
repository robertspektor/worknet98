<?php

namespace App\Hardware;

use App\Game\ActionRefused;
use App\Models\PlayerHardwarePart;
use App\Models\User;

class ThermalPasteApplier
{
    public function apply(User $player): void
    {
        $updated = PlayerHardwarePart::query()
            ->where('user_id', $player->id)
            ->installed()
            ->where('needs_thermal_paste', true)
            ->update(['needs_thermal_paste' => false]);

        if ($updated === 0) {
            throw new ActionRefused(HardwareRefusal::ThermalPasteNotNeeded);
        }
    }
}
