<?php

namespace App\Cases\Deadlines;

use App\Models\WorkCase;
use App\Work\Presence;

class StaleCaseWatcher
{
    public function __construct(
        private readonly CaseBooking $booking,
        private readonly CaseTakeover $takeover,
        private readonly CaseHandover $handover,
        private readonly Presence $presence,
    ) {}

    public function takeOverStale(): int
    {
        $takenOver = 0;

        WorkCase::query()
            ->open()
            ->whereNotNull('employment_id')
            ->where('opened_at', '<=', now()->subHours((int) config('game.case_takeover_after_hours')))
            ->with(['branch.company', 'customer.person', 'employment.position.reportsTo.person', 'employment.company', 'employment.user'])
            ->lazyById()
            ->each(function (WorkCase $workCase) use (&$takenOver): void {
                if ($this->booking->isBookedByAssignee($workCase)) {
                    return;
                }

                $this->presence->isAway($workCase->playerEmployment())
                    ? $this->handover->handOverToNpc($workCase)
                    : $this->takeover->takeOver($workCase);

                $takenOver++;
            });

        return $takenOver;
    }
}
