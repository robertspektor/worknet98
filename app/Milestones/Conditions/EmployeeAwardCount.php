<?php

namespace App\Milestones\Conditions;

use App\Models\EmployeeAward;
use App\Models\User;

readonly class EmployeeAwardCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return EmployeeAward::query()->whereRelation('employment', 'user_id', $player->id)->count() >= $this->count;
    }
}
