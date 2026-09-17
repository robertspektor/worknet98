<?php

namespace App\Policies;

use App\Models\PlayerHardwarePart;
use App\Models\User;

class PlayerHardwarePartPolicy
{
    public function install(User $player, PlayerHardwarePart $deskPart): bool
    {
        return $deskPart->user_id === $player->id && $deskPart->installed_at === null;
    }
}
