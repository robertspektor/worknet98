<?php

namespace App\Work\Goals;

use App\Models\Employment;
use App\Models\WeeklyGoal;

class WeeklyGoalBoard
{
    public function __construct(private readonly ResolvedCases $resolvedCases) {}

    public function for(Employment $employment, WeekPeriod $week): WeeklyGoalProgress
    {
        return new WeeklyGoalProgress(
            week: $week,
            target: $this->target(),
            resolvedCases: $this->resolvedCases->countIn($employment, $week),
            bonus: $this->bonusFor($employment),
            isAchieved: WeeklyGoal::query()->where('employment_id', $employment->id)->where('week', $week->key)->exists(),
        );
    }

    public function target(): int
    {
        return (int) config('game.work.weekly_cases');
    }

    public function bonusFor(Employment $employment): int
    {
        return $employment->daily_salary;
    }
}
