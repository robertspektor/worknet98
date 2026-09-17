<?php

namespace App\Work;

use App\Models\Shift;
use App\Work\Events\ShiftEnded;

class IdleShiftCloser
{
    public function closeIdle(): int
    {
        $closed = 0;

        Shift::query()
            ->whereNull('clocked_out_at')
            ->where('last_active_at', '<=', now()->subMinutes($this->idleMinutes()))
            ->lazyById()
            ->each(function (Shift $shift) use (&$closed): void {
                $shift->update(['clocked_out_at' => $shift->last_active_at, 'clocked_out_automatically' => true]);
                ShiftEnded::dispatch($shift);
                $closed++;
            });

        return $closed;
    }

    private function idleMinutes(): int
    {
        return (int) config('game.work.idle_clock_out_minutes');
    }
}
