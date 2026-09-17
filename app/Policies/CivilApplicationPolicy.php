<?php

namespace App\Policies;

use App\Models\CivilApplication;
use App\Models\User;

class CivilApplicationPolicy
{
    public function decide(User $player, CivilApplication $application): bool
    {
        $employmentId = $application->workCase?->employment_id;

        return $employmentId !== null && $employmentId === $player->employment?->id;
    }
}
