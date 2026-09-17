<?php

namespace App\Work;

use App\Models\Shift;
use Carbon\CarbonImmutable;

class ShiftActivity
{
    public function recordAt(Shift $shift, CarbonImmutable $at): void
    {
        $gap = (int) $shift->last_active_at->diffInSeconds($at, true);

        if ($gap <= $this->maxGapSeconds()) {
            $shift->worked_seconds += $gap;
        }

        $shift->last_active_at = $at;
        $shift->save();
    }

    private function maxGapSeconds(): int
    {
        return (int) config('game.work.heartbeat_gap_seconds');
    }
}
