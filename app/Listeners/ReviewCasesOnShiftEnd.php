<?php

namespace App\Listeners;

use App\Cases\CaseReviewer;
use App\Work\Events\ShiftEnded;

class ReviewCasesOnShiftEnd
{
    public function __construct(private readonly CaseReviewer $reviewer) {}

    public function handle(ShiftEnded $event): void
    {
        $this->reviewer->reviewAtShiftEnd($event->shift);
    }
}
