<?php

namespace App\Work;

use App\Models\Employment;

class MonthlySalary
{
    public const WORK_DAYS_PER_MONTH = 21;

    public function fullFor(Employment $employment): int
    {
        return $employment->daily_salary * self::WORK_DAYS_PER_MONTH;
    }

    public function earnedFor(Employment $employment, int $workedSeconds): int
    {
        return (int) round($this->fullFor($employment) * $this->share($workedSeconds));
    }

    public function share(int $workedSeconds): float
    {
        $targetSeconds = (int) config('game.work.target_minutes') * 60;

        return $targetSeconds <= 0 ? 1.0 : min(1.0, $workedSeconds / $targetSeconds);
    }
}
