<?php

namespace App\Cases;

use App\Cases\Conditions\Condition;
use App\Cases\Deadlines\CaseBooking;
use App\Cases\Deadlines\CaseTakeover;
use App\Mailbox\Mailbox;
use App\Models\Shift;
use App\Models\WorkCase;

class CaseReminder
{
    public function __construct(
        private readonly CaseDefinitions $definitions,
        private readonly CaseStateLoader $states,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
        private readonly CaseBooking $booking,
        private readonly CaseTakeover $takeover,
    ) {}

    public function remindAtShiftEnd(Shift $shift): void
    {
        WorkCase::query()
            ->where('employment_id', $shift->employment_id)
            ->open()
            ->get()
            ->each(fn (WorkCase $workCase) => $this->check($shift, $workCase));
    }

    private function check(Shift $shift, WorkCase $workCase): void
    {
        $isBooked = $this->booking->isBookedByAssignee($workCase);

        if (! $isBooked && $workCase->reminded_at !== null) {
            $this->takeover->takeOver($workCase);

            return;
        }

        $this->remindIfUnfinished($shift, $workCase);

        if (! $isBooked) {
            $workCase->update(['reminded_at' => now()]);
        }
    }

    private function remindIfUnfinished(Shift $shift, WorkCase $workCase): void
    {
        $definition = $this->definitions->for($workCase);

        if ($definition === null || $this->goalsMet($definition, $workCase)) {
            return;
        }

        $this->mailbox->deliver($shift->user, $this->mails->reminder($definition, $shift->employment), $shift->employment);
    }

    private function goalsMet(CaseDefinition $definition, WorkCase $workCase): bool
    {
        $state = $this->states->for($workCase);

        return collect($definition->goals)->every(fn (Condition $goal): bool => $goal->isMetBy($state));
    }
}
