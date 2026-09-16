<?php

namespace App\Work;

use App\Game\ActionRefused;
use App\Models\Employment;
use App\Models\User;

class DutyCheck
{
    public function __construct(private readonly TodaysShift $todaysShift) {}

    public function employmentOnDuty(User $player): Employment
    {
        $employment = $player->employment ?? throw new ActionRefused(ShiftRefusal::NotEmployed);

        if (! $this->todaysShift->of($employment)?->isOnDuty()) {
            throw new ActionRefused(ShiftRefusal::NotOnDuty);
        }

        return $employment;
    }
}
