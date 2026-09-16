<?php

namespace App\FloppyDisks;

use App\Models\FloppyDisk;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DiskBox
{
    /**
     * @return Collection<int, FloppyDisk>
     */
    public function disksFor(User $player): Collection
    {
        return FloppyDisk::query()
            ->where(fn (Builder $query) => $query
                ->where('is_starter', true)
                ->orWhereHas('orders', fn (Builder $orders) => $orders->unpacked()->where('user_id', $player->id)))
            ->orderBy('id')
            ->get();
    }

    public function contains(User $player, FloppyDisk $disk): bool
    {
        return $this->disksFor($player)->contains($disk);
    }
}
