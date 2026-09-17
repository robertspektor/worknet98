<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function unpack(User $player, Order $order): bool
    {
        return $order->user_id === $player->id && $order->isDelivered();
    }
}
