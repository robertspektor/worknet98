<?php

namespace App\Cases;

use App\Cases\Events\CaseOpened;
use App\Mailbox\Mailbox;
use App\Models\Shift;
use App\Models\WorkCase;

class CaseOpener
{
    public function __construct(
        private readonly CaseCatalog $catalog,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function openForShift(Shift $shift): void
    {
        $employment = $shift->employment;
        $shiftNumber = Shift::query()->where('employment_id', $employment->id)->count();
        $opened = WorkCase::query()->where('employment_id', $employment->id)->where('kind', WorkCaseKind::Scripted)->pluck('case_slug');

        foreach ($this->catalog->forCompany($employment->company) as $scripted) {
            if ($scripted->shift <= $shiftNumber && ! $opened->contains($scripted->definition->slug)) {
                $this->open($shift, $scripted);
            }
        }
    }

    private function open(Shift $shift, ScriptedCase $scripted): void
    {
        $definition = $scripted->definition;
        $employment = $shift->employment;
        $customer = $employment->branch()->customers()->ofPerson($definition->requestMail['customer'])->firstOrFail();

        $workCase = WorkCase::create([
            'branch_id' => $employment->position->branch_id,
            'position_id' => $employment->position_id,
            'employment_id' => $employment->id,
            'customer_id' => $customer->id,
            'kind' => WorkCaseKind::Scripted,
            'case_slug' => $definition->slug,
            'status' => WorkCaseStatus::Open,
            'opened_at' => now(),
            'seen_at' => now(),
        ]);

        if ($scripted->briefingMail !== null) {
            $this->mailbox->deliver($shift->user, $this->mails->briefing($scripted->briefingMail, $employment), $employment);
        }

        $this->mailbox->deliver($shift->user, $this->mails->request($definition, $customer), $employment);

        CaseOpened::dispatch($workCase);
    }
}
