<?php

namespace App\Work\Goals;

use App\Cases\WorkCaseStatus;
use App\Models\Employment;
use App\Models\WorkCase;

class ResolvedCases
{
    public function countIn(Employment $employment, WeekPeriod $week): int
    {
        return WorkCase::query()
            ->where('employment_id', $employment->id)
            ->where('status', WorkCaseStatus::Resolved)
            ->where('resolved_at', '>=', $week->startsAt)
            ->where('resolved_at', '<', $week->endsAt)
            ->count();
    }
}
