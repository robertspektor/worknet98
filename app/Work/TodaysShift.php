<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;

class TodaysShift
{
    public function __construct(private readonly WorkDay $workDay) {}

    public function of(Employment $employment): ?Shift
    {
        return Shift::query()
            ->where('employment_id', $employment->id)
            ->whereDate('work_date', $this->workDay->today())
            ->first();
    }
}
