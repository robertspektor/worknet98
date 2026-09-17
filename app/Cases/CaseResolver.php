<?php

namespace App\Cases;

use App\Cases\Events\CaseResolved;
use App\Mailbox\Mailbox;
use App\Models\WorkCase;

class CaseResolver
{
    public function __construct(
        private readonly CaseDefinitions $definitions,
        private readonly CaseStateLoader $states,
        private readonly MetricBook $metrics,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function resolve(WorkCase $workCase): void
    {
        $definition = $this->definitions->for($workCase);

        if ($workCase->employment !== null && $definition !== null) {
            $this->evaluateForPlayer($workCase, $definition);
        }

        $workCase->update(['status' => WorkCaseStatus::Resolved, 'resolved_at' => now()]);

        CaseResolved::dispatch($workCase);
    }

    private function evaluateForPlayer(WorkCase $workCase, CaseDefinition $definition): void
    {
        $employment = $workCase->playerEmployment();
        $state = $this->states->for($workCase);
        $feedback = [];

        foreach ($definition->outcomes as $outcome) {
            $isMet = $outcome->when->isMetBy($state);
            $this->metrics->apply($employment, $isMet ? $outcome->effects : $outcome->otherwiseEffects);
            $feedback[] = $isMet ? $outcome->feedback : $outcome->otherwiseFeedback;
        }

        $this->mailbox->deliver($employment->user, $this->mails->feedback($definition, $employment, $feedback), $employment);
    }
}
