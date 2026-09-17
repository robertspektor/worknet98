<?php

namespace App\Logistics;

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Logistics\Templates\ShipmentTexts;
use App\Mailbox\EmailDraft;
use App\Mailbox\Mailbox;
use App\Models\Shipment;

class CarrierComplaint
{
    public function __construct(
        private readonly ShipmentTemplateCatalog $templates,
        private readonly Mailbox $mailbox,
        private readonly MetricBook $metrics,
    ) {}

    public function send(Shipment $shipment): void
    {
        $dispatchCase = $shipment->dispatchCase;
        $employment = $dispatchCase?->employment;
        $template = $dispatchCase === null ? null : $this->templates->find($shipment->branch->company, $dispatchCase->case_slug);

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
