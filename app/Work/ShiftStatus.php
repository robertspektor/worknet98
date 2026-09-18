<?php

namespace App\Work;

use App\Models\Shift;
use App\Work\Goals\WeeklyGoalProgress;

readonly class ShiftStatus
{
    public function __construct(
        public ?Shift $latestShift,
        public ContractPeriod $period,
        public int $workedSeconds,
        public int $targetSeconds,
        public int $fullSalary,
        public int $earnedSalary,
        public WeeklyGoalProgress $weeklyGoal,
    ) {}

    public function state(): string
    {
        return $this->latestShift?->isOnDuty() ? 'on_duty' : 'off_duty';
    }

    public function wasClockedOutAutomatically(): bool
    {
        return (bool) $this->latestShift?->clocked_out_automatically;
    }
}
