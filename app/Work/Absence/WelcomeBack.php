<?php

namespace App\Work\Absence;

use App\Mailbox\Mailbox;
use App\Models\Shift;
use App\Work\Presence;

class WelcomeBack
{
    public function __construct(
        private readonly Presence $presence,
        private readonly AbsenceReporter $reporter,
        private readonly WelcomeBackLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function greet(Shift $shift): void
    {
        $lastActiveAt = $this->presence->lastActiveBefore($shift);

        if ($lastActiveAt === null || $lastActiveAt->gt(now()->subHours($this->presence->awayAfterHours()))) {
            return;
        }

        $employment = $shift->employment;
        $report = $this->reporter->since($employment, $lastActiveAt);

        $this->mailbox->deliver($shift->user, $this->letter->compose($employment, $report), $employment);
    }
}
