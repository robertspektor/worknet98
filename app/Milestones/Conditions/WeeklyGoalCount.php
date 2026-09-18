<?php

namespace App\Milestones\Conditions;

use App\Models\User;
use App\Models\WeeklyGoal;

readonly class WeeklyGoalCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return WeeklyGoal::query()->whereRelation('employment', 'user_id', $player->id)->count() >= $this->count;
    }
}
