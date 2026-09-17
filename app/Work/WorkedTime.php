<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;

class WorkedTime
{
    public function secondsIn(Employment $employment, ContractPeriod $period): int
    {
        return (int) Shift::query()
            ->where('employment_id', $employment->id)
            ->where('clocked_in_at', '>=', $period->startsAt)
            ->where('clocked_in_at', '<', $period->endsAt)
            ->sum('worked_seconds');
    }
}
