<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;
use App\Work\Goals\WeeklyGoalBoard;
use App\Work\Goals\WeekPeriods;

class ShiftStatusQuery
{
    public function __construct(
        private readonly ContractPeriods $periods,
        private readonly WorkedTime $workedTime,
        private readonly MonthlySalary $salary,
        private readonly WeekPeriods $weeks,
        private readonly WeeklyGoalBoard $goals,
    ) {}

    public function for(Employment $employment): ShiftStatus
    {
        $period = $this->periods->current();
        $workedSeconds = $this->workedTime->secondsIn($employment, $period);

        return new ShiftStatus(
            latestShift: Shift::query()->where('employment_id', $employment->id)->latest('clocked_in_at')->latest('id')->first(),
            period: $period,
            workedSeconds: $workedSeconds,
            targetSeconds: (int) config('game.work.target_minutes') * 60,
            fullSalary: $this->salary->fullFor($employment),
            earnedSalary: $this->salary->earnedFor($employment, $workedSeconds),
            weeklyGoal: $this->goals->for($employment, $this->weeks->current()),
        );
    }
}
