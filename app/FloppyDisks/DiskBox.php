<?php

namespace App\FloppyDisks;

use App\Models\FloppyDisk;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DiskBox
{
    /**
     * @return Collection<int, FloppyDisk>
     */
    public function disksFor(User $player): Collection
    {
        return FloppyDisk::query()->starter()->orderBy('id')->get();
    }

    public function contains(User $player, FloppyDisk $disk): bool
    {
        return $this->disksFor($player)->contains($disk);
    }
}
