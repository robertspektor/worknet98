<?php

namespace App\Listeners;

use App\Cases\CaseOpener;
use App\Work\Events\ShiftStarted;

class OpenCasesOnShiftStart
{
    public function __construct(private readonly CaseOpener $opener) {}

    public function handle(ShiftStarted $event): void
    {
        $this->opener->openForShift($event->shift);
    }
}
