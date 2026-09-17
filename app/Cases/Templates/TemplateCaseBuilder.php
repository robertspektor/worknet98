<?php

namespace App\Cases\Templates;

use App\Cases\CaseDefinition;
use App\Cases\Conditions\AppointmentBooked;
use App\Cases\Conditions\AppointmentWithinAvailability;
use App\Cases\Conditions\AppointmentWithinWorkDays;
use App\Cases\Conditions\CalendarEntryForAppointment;
use App\Cases\Conditions\Condition;
use App\Cases\Conditions\EmailSent;
use App\Cases\Conditions\TechnicianHasSkill;
use App\Cases\Metric;
use App\Cases\Outcome;
use App\Mailbox\EmailAction;
use App\Models\Customer;
use App\Workplace\Availability;

class TemplateCaseBuilder
{
    public function build(CaseTemplate $template, Customer $customer): CaseDefinition
    {
        $texts = new TemplateTexts($template, $customer);

        return new CaseDefinition(
            slug: $template->slug,
            requestMail: [
                'customer' => $customer->slug,
                'subject' => $texts->fill($template->requestMail['subject']),
                'body' => $texts->fill($template->requestMail['body']),
            ],
            goals: [
                new AppointmentBooked($customer->slug),
                new EmailSent($customer->slug, EmailAction::ConfirmAppointment),
                new CalendarEntryForAppointment($customer->slug),
            ],
            messages: [],
            outcomes: $this->outcomes($template, $customer, $texts),
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
     * @return list<Outcome>
     */
    private function outcomes(CaseTemplate $template, Customer $customer, TemplateTexts $texts): array
    {
        $outcomes = [
            $this->outcome($texts, 'skill', new TechnicianHasSkill($customer->slug, $template->skill), [], [Metric::Cost->value => -2]),
            $this->outcome($texts, 'urgency', new AppointmentWithinWorkDays($customer->slug, $template->urgentWithinWorkDays), [Metric::Punctuality->value => 1], [Metric::Punctuality->value => -1]),
        ];

        if ($customer->availability !== Availability::Any) {
            array_unshift($outcomes, $this->outcome($texts, 'availability', new AppointmentWithinAvailability($customer->slug, $customer->availability), [Metric::CustomerSatisfaction->value => 2], [Metric::CustomerSatisfaction->value => -2]));
        }

        return $outcomes;
    }

    /**
     * @param  array<string, int>  $effects
     * @param  array<string, int>  $otherwise
     */
    private function outcome(TemplateTexts $texts, string $aspect, Condition $when, array $effects, array $otherwise): Outcome
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
