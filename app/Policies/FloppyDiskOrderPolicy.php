<?php

namespace App\Policies;

use App\Models\FloppyDiskOrder;
use App\Models\User;

class FloppyDiskOrderPolicy
{
    public function unpack(User $player, FloppyDiskOrder $order): bool
    {
        return $order->user_id === $player->id && $order->isDelivered();
    }
}
