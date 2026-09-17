<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;

class ShiftStatusQuery
{
    public function __construct(
        private readonly ContractPeriods $periods,
        private readonly WorkedTime $workedTime,
    ) {}

    public function for(Employment $employment): ShiftStatus
    {
        $period = $this->periods->current();

        return new ShiftStatus(
            latestShift: Shift::query()->where('employment_id', $employment->id)->latest('clocked_in_at')->latest('id')->first(),
            period: $period,
            workedSeconds: $this->workedTime->secondsIn($employment, $period),
            targetSeconds: (int) config('game.work.target_minutes') * 60,
        );
    }
}
