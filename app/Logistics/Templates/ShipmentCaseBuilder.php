<?php

namespace App\Logistics\Templates;

use App\Cases\CaseDefinition;
use App\Cases\Conditions\Condition;
use App\Cases\Conditions\EmailSent;
use App\Cases\Conditions\ShipmentOnCheapestVehicle;
use App\Cases\Conditions\ShipmentPlanned;
use App\Cases\Conditions\ShipmentPlannedInTime;
use App\Cases\Metric;
use App\Cases\Outcome;
use App\Mailbox\EmailAction;
use App\Models\Customer;
use App\Models\Shipment;

class ShipmentCaseBuilder
{
    public function build(ShipmentTemplate $template, Shipment $shipment, Customer $contact): CaseDefinition
    {
        $texts = new ShipmentTexts($template, $shipment, $contact);

        return new CaseDefinition(
            slug: $template->slug,
            requestMail: [
                'customer' => $contact->person->slug,
                'subject' => $texts->fill($template->requestMail['subject']),
                'body' => $texts->fill($template->requestMail['body']),
            ],
            goals: [
                new ShipmentPlanned,
                new EmailSent($contact->person->slug, EmailAction::ConfirmShipment),
            ],
            messages: [],
            outcomes: [
                $this->outcome($texts, 'vehicle', new ShipmentOnCheapestVehicle, [], [Metric::Cost->value => -2]),
                $this->outcome($texts, 'punctuality', new ShipmentPlannedInTime, [Metric::Punctuality->value => 1], [Metric::Punctuality->value => -2]),
            ],
            feedbackMail: [
                'subject' => $texts->fill($template->feedbackMail['subject']),
                'intro' => $texts->fill($template->feedbackMail['intro']),
                'outro' => $texts->fill($template->feedbackMail['outro']),
            ],
            reminderMail: [
                'subject' => $texts->fill($template->reminderMail['subject']),
                'body' => $texts->fill($template->reminderMail['body']),
            ],
        );
    }

    /**
     * @param  array<string, int>  $effects
     * @param  array<string, int>  $otherwise
     */
    private function outcome(ShipmentTexts $texts, string $aspect, Condition $when, array $effects, array $otherwise): Outcome
    {
        return new Outcome(
            when: $when,
            effects: $effects,
            otherwiseEffects: $otherwise,
            feedback: $texts->feedback($aspect, 'met'),
            otherwiseFeedback: $texts->feedback($aspect, 'missed'),
        );
    }
}
