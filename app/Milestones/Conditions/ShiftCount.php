<?php

namespace App\Milestones\Conditions;

use App\Models\Shift;
use App\Models\User;

readonly class ShiftCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return Shift::query()->whereBelongsTo($player)->count() >= $this->count;
    }
}
