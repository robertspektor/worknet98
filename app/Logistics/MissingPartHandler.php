<?php

namespace App\Logistics;

use App\Cases\CaseMails;
use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\Templates\TemplateTexts;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Logistics\Templates\ShipmentTexts;
use App\Mailbox\EmailDraft;
use App\Mailbox\Mailbox;
use App\Models\Appointment;
use App\Models\Shipment;
use App\Models\WorkCase;

class MissingPartHandler
{
    public function __construct(
        private readonly CaseTemplateCatalog $caseTemplates,
        private readonly ShipmentTemplateCatalog $shipmentTemplates,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
        private readonly MetricBook $metrics,
    ) {}

    public function handle(WorkCase $repairCase, Appointment $appointment, Shipment $shipment): void
    {
        $appointment->update(['failed_at' => now()]);

        $this->informRepairDesk($repairCase);
        $this->complainToCarrier($shipment);
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

    private function complainToCarrier(Shipment $shipment): void
    {
        $dispatchCase = $shipment->dispatchCase;
        $employment = $dispatchCase?->employment;
        $template = $this->shipmentTemplates->find($shipment->branch->company, $dispatchCase->case_slug ?? '');

        if ($dispatchCase === null || $employment === null || $template === null) {
            return;
        }

        $texts = new ShipmentTexts($template, $shipment, $dispatchCase->customer);
        $draft = new EmailDraft(
            senderName: $shipment->recipient->company->name,
            senderAddress: $shipment->recipient->office_address,
            subject: $texts->fill($template->complaintMail['subject']),
            body: $texts->fill($template->complaintMail['body']),
        );

        $this->mailbox->deliver($employment->user, $draft, $employment);
        $this->metrics->apply($employment, [Metric::CustomerSatisfaction->value => -1]);
    }
}
