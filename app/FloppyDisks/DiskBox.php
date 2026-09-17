<?php

namespace App\FloppyDisks;

use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DiskBox
{
    public function __construct(private readonly StarterDisks $starterDisks) {}

    /**
     * @return Collection<int, PlayerFloppyDisk>
     */
    public function disksFor(User $player): Collection
    {
        $this->starterDisks->handOutTo($player);

        return PlayerFloppyDisk::query()
            ->where('user_id', $player->id)
            ->with('floppyDisk')
            ->orderBy('id')
            ->get();
    }
}
