<?php

namespace App\Milestones\Conditions;

use App\Models\PlayerHardwarePart;
use App\Models\User;

readonly class OwnHardwareInstalled implements MilestoneCondition
{
    public function isMetBy(User $player): bool
    {
        return PlayerHardwarePart::query()
            ->where('user_id', $player->id)
            ->installed()
            ->whereRelation('hardwarePart', 'is_starter', false)
            ->exists();
    }
}
