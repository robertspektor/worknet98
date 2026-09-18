<?php

namespace App\Auth;

use App\Models\User;

class LastSeen
{
    private const REFRESH_MINUTES = 5;

    public function touch(User $player): void
    {
        if ($player->last_seen_at !== null && $player->last_seen_at->gt(now()->subMinutes(self::REFRESH_MINUTES))) {
            return;
        }

        $player->update(['last_seen_at' => now()]);
    }
}
