<?php

namespace App\Logistics;

use App\Cases\CaseMails;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateTexts;
use App\Mailbox\Mailbox;
use App\Models\Appointment;
use App\Models\Shipment;
use App\Models\WorkCase;

class MissingPartHandler
{
    public function __construct(
        private readonly CaseTemplateCatalog $caseTemplates,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
        private readonly CarrierComplaint $complaint,
    ) {}

    public function handle(WorkCase $repairCase, Appointment $appointment, Shipment $shipment): void
    {
        $appointment->update(['failed_at' => now()]);

        $this->informRepairDesk($repairCase);
        $this->complaint->send($shipment);
    }

    private function informRepairDesk(WorkCase $repairCase): void
    {
        $employment = $repairCase->employment;

        if ($employment === null) {
            $repairCase->update(['npc_due_at' => now()->addSeconds((int) config('game.npc_case_delay_seconds'))]);

            return;
        }

        $template = $this->caseTemplates->find($repairCase->branch->company, $repairCase->case_slug);
        $mail = $template?->part?->missingMail;

        if ($template !== null && $mail !== null) {
            $texts = new TemplateTexts($template, $repairCase->customer);
            $draft = $this->mails->fromSuperior($employment, $texts->fill($mail['subject']), $texts->fill($mail['body']));
            $this->mailbox->deliver($employment->user, $draft, $employment);
        }
    }
}
