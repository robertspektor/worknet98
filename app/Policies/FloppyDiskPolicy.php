<?php

namespace App\Policies;

use App\FloppyDisks\DiskBox;
use App\Models\FloppyDisk;
use App\Models\User;

class FloppyDiskPolicy
{
    public function __construct(private readonly DiskBox $diskBox) {}

    public function install(User $player, FloppyDisk $disk): bool
    {
        return $disk->isInstallable() && $this->diskBox->contains($player, $disk);
    }
}
