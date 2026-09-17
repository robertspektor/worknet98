<?php

namespace App\Hardware;

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;

class HomeComputer
{
    public function cpuOf(User $player): InstalledCpu
    {
        $installed = PlayerHardwarePart::query()
            ->where('user_id', $player->id)
            ->installed()
            ->whereRelation('hardwarePart', 'slot', HardwareSlot::Cpu)
            ->with('hardwarePart')
            ->first();

        return $installed === null
            ? new InstalledCpu($this->starterCpu(), needsThermalPaste: false)
            : new InstalledCpu($installed->hardwarePart, $installed->needs_thermal_paste);
    }

    private function starterCpu(): HardwarePart
    {
        return HardwarePart::query()->starter()->forSlot(HardwareSlot::Cpu)->sole();
    }
}
