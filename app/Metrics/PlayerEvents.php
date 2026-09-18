<?php

namespace App\Metrics;

use App\Models\PlayerEvent;
use App\Models\User;

class PlayerEvents
{
    public function record(User $player, FunnelStep $step): void
    {
        PlayerEvent::query()->insertOrIgnore([
            'user_id' => $player->id,
            'name' => $step->value,
            'recorded_at' => now(),
        ]);
    }
}
