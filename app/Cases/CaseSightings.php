<?php

namespace App\Cases;

use App\Models\Shift;
use App\Models\WorkCase;

class CaseSightings
{
    public function markSeenAtShiftStart(Shift $shift): void
    {
        WorkCase::query()
            ->where('employment_id', $shift->employment_id)
            ->open()
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }
}
