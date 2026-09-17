<?php

namespace App\Cases;

use App\Cases\Events\CaseResolved;
use App\Mailbox\Mailbox;
use App\Models\Shift;
use App\Models\WorkCase;

class CaseReviewer
{
    public function __construct(
        private readonly CaseCatalog $catalog,
        private readonly CaseStateLoader $states,
        private readonly MetricBook $metrics,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function reviewAtShiftEnd(Shift $shift): void
    {
        WorkCase::query()
            ->where('employment_id', $shift->employment_id)
            ->where('status', WorkCaseStatus::Open)
            ->get()
            ->each(fn (WorkCase $workCase) => $this->review($shift, $workCase));
    }

    private function review(Shift $shift, WorkCase $workCase): void
    {
        $company = $shift->employment->company;
        $definition = $this->catalog->find($company, $workCase->case_slug);

        if ($definition === null) {
            return;
        }

        $state = $this->states->for($workCase);
        $goalsMet = collect($definition->goals)->every(fn (Conditions\Condition $goal): bool => $goal->isMetBy($state));

        if (! $goalsMet) {
            $this->mailbox->deliver($shift->user, $this->mails->reminder($definition, $shift->employment), $shift->employment);

            return;
        }

        $this->resolve($shift, $workCase, $definition, $state);
    }

    private function resolve(Shift $shift, WorkCase $workCase, CaseDefinition $definition, CaseState $state): void
    {
        $feedback = [];

        foreach ($definition->outcomes as $outcome) {
            $isMet = $outcome->when->isMetBy($state);
            $this->metrics->apply($shift->employment, $isMet ? $outcome->effects : $outcome->otherwiseEffects);
            $feedback[] = $isMet ? $outcome->feedback : $outcome->otherwiseFeedback;
        }

        $workCase->update(['status' => WorkCaseStatus::Resolved, 'resolved_at' => now()]);
        $this->mailbox->deliver($shift->user, $this->mails->feedback($definition, $shift->employment, $feedback), $shift->employment);

        CaseResolved::dispatch($workCase);
    }
}
