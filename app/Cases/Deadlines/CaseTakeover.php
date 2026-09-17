<?php

namespace App\Cases\Deadlines;

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Mailbox\Mailbox;
use App\Models\WorkCase;
use Illuminate\Support\Facades\DB;

class CaseTakeover
{
    public function __construct(
        private readonly CaseHandover $handover,
        private readonly MetricBook $metrics,
        private readonly TakeoverLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function takeOver(WorkCase $workCase): void
    {
        $employment = $workCase->playerEmployment();

        DB::transaction(function () use ($workCase, $employment): void {
            $colleague = $this->handover->handOverToNpc($workCase);
            $draft = $colleague === null ? $this->letter->lost($workCase, $employment) : $this->letter->handedOver($workCase, $employment, $colleague);

            $this->metrics->apply($employment, [Metric::Reliability->value => -1]);
            $this->mailbox->deliver($employment->user, $draft, $employment);
        });
    }
}
