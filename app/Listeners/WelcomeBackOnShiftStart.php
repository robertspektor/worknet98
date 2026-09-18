<?php

namespace App\Listeners;

use App\Work\Absence\WelcomeBack;
use App\Work\Events\ShiftStarted;

class WelcomeBackOnShiftStart
{
    public function __construct(private readonly WelcomeBack $welcomeBack) {}

    public function handle(ShiftStarted $event): void
    {
        $this->welcomeBack->greet($event->shift);
    }
}
