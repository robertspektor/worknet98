<?php

namespace App\Work\Absence;

readonly class AbsenceReport
{
    public function __construct(
        public int $awayDays,
        public int $waitingCases,
        public int $takenOverCases,
        public int $newMails,
        public int $balance,
    ) {}
}
