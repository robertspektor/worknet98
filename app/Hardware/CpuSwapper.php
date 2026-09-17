<?php

namespace App\Hardware;

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CpuSwapper
{
    public function swap(User $player, PlayerHardwarePart $newCpu, bool $thermalPasteApplied): void
    {
        DB::transaction(function () use ($player, $newCpu, $thermalPasteApplied): void {
            User::query()->whereKey($player->id)->lockForUpdate()->first();

            $this->removeInstalledCpu($player);
            $newCpu->update([
                'installed_at' => now(),
                'removed_at' => null,
                'needs_thermal_paste' => ! $thermalPasteApplied,
            ]);
        });
    }

    private function removeInstalledCpu(User $player): void
    {
        $installed = PlayerHardwarePart::query()
            ->where('user_id', $player->id)
            ->installed()
            ->whereRelation('hardwarePart', 'slot', HardwareSlot::Cpu)
            ->first();

        $installed ??= PlayerHardwarePart::firstOrNew([
            'user_id' => $player->id,
            'hardware_part_id' => HardwarePart::query()->starter()->forSlot(HardwareSlot::Cpu)->sole()->id,
        ]);

        $installed->fill(['installed_at' => null, 'removed_at' => now(), 'needs_thermal_paste' => false])->save();
    }
}
