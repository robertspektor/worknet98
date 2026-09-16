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
        $opened = WorkCase::query()->where('employment_id', $employment->id)->pluck('case_slug');

        foreach ($this->catalog->forCompany($employment->company) as $definition) {
            if ($definition->shift <= $shiftNumber && ! $opened->contains($definition->slug)) {
                $this->open($shift, $definition);
            }
        }
    }

    private function open(Shift $shift, CaseDefinition $definition): void
    {
        $employment = $shift->employment;
        $customer = $employment->company->customers()->where('slug', $definition->requestMail['customer'])->firstOrFail();

        $workCase = WorkCase::create([
            'employment_id' => $employment->id,
            'case_slug' => $definition->slug,
            'status' => WorkCaseStatus::Open,
            'opened_at' => now(),
        ]);

        $this->mailbox->deliver($shift->user, $this->mails->request($definition, $customer), $employment);

        CaseOpened::dispatch($workCase);
    }
}
