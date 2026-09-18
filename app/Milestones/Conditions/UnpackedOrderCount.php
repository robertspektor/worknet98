<?php

namespace App\Milestones\Conditions;

use App\Models\Order;
use App\Models\User;

readonly class UnpackedOrderCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return Order::query()->whereBelongsTo($player)->unpacked()->count() >= $this->count;
    }
}
