<?php

namespace App\Milestones\Conditions;

use App\Models\User;

interface MilestoneCondition
{
    public function isMetBy(User $player): bool;
}
