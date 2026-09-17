<?php

namespace App\Cases;

use App\Cases\Conditions\Condition;
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
    ) {}

    public function remindAtShiftEnd(Shift $shift): void
    {
        WorkCase::query()
            ->where('employment_id', $shift->employment_id)
            ->open()
            ->get()
            ->each(fn (WorkCase $workCase) => $this->remindIfUnfinished($shift, $workCase));
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
