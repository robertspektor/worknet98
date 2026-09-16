<?php

namespace App\Work;

use App\Models\LedgerEntry;
use App\Models\Shift;

class SalaryPayout
{
    public function payFor(Shift $shift): LedgerEntry
    {
        return LedgerEntry::firstOrCreate(
            ['shift_id' => $shift->id],
            [
                'user_id' => $shift->user_id,
                'amount' => $shift->employment->daily_salary,
                'reason' => LedgerReason::Salary,
            ],
        );
    }
}
