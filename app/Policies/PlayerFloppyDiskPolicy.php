<?php

namespace App\Policies;

use App\Models\PlayerFloppyDisk;
use App\Models\User;

class PlayerFloppyDiskPolicy
{
    public function view(User $player, PlayerFloppyDisk $disk): bool
    {
        return $disk->user_id === $player->id;
    }

    public function write(User $player, PlayerFloppyDisk $disk): bool
    {
        return $disk->user_id === $player->id;
    }

    public function label(User $player, PlayerFloppyDisk $disk): bool
    {
        return $disk->user_id === $player->id && $disk->isLabelable();
    }
}
