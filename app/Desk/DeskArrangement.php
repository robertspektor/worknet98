<?php

namespace App\Desk;

use App\Models\DeskPlacement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DeskArrangement
{
    /**
     * @return Collection<int, DeskPlacement>
     */
    public function of(User $player): Collection
    {
        return $player->deskPlacements()->orderBy('item')->get();
    }

    public function place(User $player, string $item, float $x, float $y): DeskPlacement
    {
        return $player->deskPlacements()->updateOrCreate(['item' => $item], ['x' => $x, 'y' => $y]);
    }
}
