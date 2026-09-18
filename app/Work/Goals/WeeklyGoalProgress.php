<?php

namespace App\Work\Goals;

readonly class WeeklyGoalProgress
{
    public function __construct(
        public WeekPeriod $week,
        public int $target,
        public int $resolvedCases,
        public int $bonus,
        public bool $isAchieved,
    ) {}
}
