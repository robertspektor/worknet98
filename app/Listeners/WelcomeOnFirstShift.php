<?php

namespace App\Listeners;

use App\Careers\FirstDay;
use App\Work\Events\ShiftStarted;

class WelcomeOnFirstShift
{
    public function __construct(private readonly FirstDay $firstDay) {}

    public function handle(ShiftStarted $event): void
    {
        $this->firstDay->welcome($event->shift);
    }
}
