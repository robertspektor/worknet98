<?php

namespace App\Policies;

use App\Models\DiskFile;
use App\Models\User;

class DiskFilePolicy
{
    public function delete(User $player, DiskFile $file): bool
    {
        return $file->disk->user_id === $player->id;
    }

    public function install(User $player, DiskFile $file): bool
    {
        return $file->disk->user_id === $player->id && $file->isInstallable();
    }
}
