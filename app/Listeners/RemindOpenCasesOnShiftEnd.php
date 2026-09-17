<?php

namespace App\Listeners;

use App\Cases\CaseReminder;
use App\Work\Events\ShiftEnded;

class RemindOpenCasesOnShiftEnd
{
    public function __construct(private readonly CaseReminder $reminder) {}

    public function handle(ShiftEnded $event): void
    {
        $this->reminder->remindAtShiftEnd($event->shift);
    }
}
