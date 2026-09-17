<?php

namespace App\Hardware;

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DeskParts
{
    public function place(User $player, HardwarePart $part): PlayerHardwarePart
    {
        return PlayerHardwarePart::firstOrCreate(['user_id' => $player->id, 'hardware_part_id' => $part->id]);
    }

    /**
     * @return Collection<int, PlayerHardwarePart>
     */
    public function of(User $player): Collection
    {
        return PlayerHardwarePart::query()
            ->where('user_id', $player->id)
            ->onDesk()
            ->with('hardwarePart')
            ->orderBy('id')
            ->get();
    }
}
