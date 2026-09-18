<?php

namespace App\Milestones\Conditions;

use App\Cases\WorkCaseStatus;
use App\Models\User;
use App\Models\WorkCase;

readonly class ResolvedCaseCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return WorkCase::query()
            ->where('status', WorkCaseStatus::Resolved)
            ->whereRelation('employment', 'user_id', $player->id)
            ->count() >= $this->count;
    }
}
