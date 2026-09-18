<?php

namespace App\Milestones\Conditions;

use App\Models\Employment;
use App\Models\User;

readonly class EmploymentCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return Employment::query()->whereBelongsTo($player)->count() >= $this->count;
    }
}
