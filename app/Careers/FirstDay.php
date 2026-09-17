<?php

namespace App\Careers;

use App\Cases\CaseMails;
use App\Mailbox\Mailbox;
use App\Models\Shift;

class FirstDay
{
    public function __construct(
        private readonly OnboardingCatalog $catalog,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
        private readonly FirstTask $firstTask,
    ) {}

    public function welcome(Shift $shift): void
    {
        $employment = $shift->employment;

        if (Shift::query()->where('employment_id', $employment->id)->count() > 1) {
            return;
        }

        $briefing = $this->catalog->briefingFor($employment->company);

        if ($briefing !== null) {
            $this->mailbox->deliver($shift->user, $this->mails->briefing($briefing, $employment), $employment);
        }

        $this->firstTask->ensureFor($employment);
    }
}
