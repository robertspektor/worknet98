<?php

namespace App\Logistics;

use App\Cases\CaseMails;
use App\Cases\Events\CaseOpened;
use App\Cases\Routing\CaseAssignment;
use App\Cases\Routing\CaseRouter;
use App\Cases\WorkCaseKind;
use App\Logistics\Templates\ShipmentCaseBuilder;
use App\Logistics\Templates\ShipmentTemplate;
use App\Mailbox\Mailbox;
use App\Models\Shipment;
use App\Models\WorkCase;
use App\Workplace\CustomerIntake;

class ShipmentCaseOpener
{
    private const SENDER_RESPONSIBILITY = 'sales';

    public function __construct(
        private readonly CaseRouter $router,
        private readonly CustomerIntake $intake,
        private readonly CaseAssignment $assignment,
        private readonly ShipmentCaseBuilder $builder,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function open(Shipment $shipment, ShipmentTemplate $template): WorkCase
    {
        $contact = $this->intake->businessContactFor($shipment->branch, $this->router->assigneeFor($shipment->sender, self::SENDER_RESPONSIBILITY));
        $workCase = $this->assignment->assign($shipment->branch, $template->responsibility, [
            'customer_id' => $contact->id,
            'kind' => WorkCaseKind::Shipment,
            'case_slug' => $template->slug,
            'shipment_id' => $shipment->id,
        ]);
        $employment = $workCase->employment;

        if ($employment !== null) {
            $request = $this->mails->request($this->builder->build($template, $shipment, $contact), $contact);
            $this->mailbox->deliver($employment->user, $request, $employment);

            CaseOpened::dispatch($workCase);
        }

        return $workCase;
    }
}
