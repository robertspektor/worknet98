<?php

namespace App\Listeners;

use App\Cases\CaseSightings;
use App\Work\Events\ShiftStarted;

class MarkCasesSeenOnShiftStart
{
    public function __construct(private readonly CaseSightings $sightings) {}

    public function handle(ShiftStarted $event): void
    {
        $this->sightings->markSeenAtShiftStart($event->shift);
    }
}
