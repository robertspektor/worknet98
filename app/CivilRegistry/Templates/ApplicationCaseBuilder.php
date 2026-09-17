<?php

namespace App\CivilRegistry\Templates;

use App\Cases\CaseDefinition;
use App\Cases\Conditions\ApplicationDecided;
use App\Cases\Conditions\ApplicationDecidedCorrectly;
use App\Cases\Metric;
use App\Cases\Outcome;
use App\CivilRegistry\ApplicationDecision;
use App\Models\CivilApplication;

class ApplicationCaseBuilder
{
    public function build(ApplicationTemplate $template, CivilApplication $application): CaseDefinition
    {
        $texts = new ApplicationTexts($template, $application);

        return new CaseDefinition(
            slug: $template->slug,
            requestMail: [
                'customer' => $application->applicant->slug,
                'subject' => $texts->fill($template->requestMail['subject']),
                'body' => $texts->fill($template->requestMail['body']),
            ],
            goals: [new ApplicationDecided],
            messages: [],
            outcomes: [$this->outcome($template, $texts, $application->decision)],
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

    private function outcome(ApplicationTemplate $template, ApplicationTexts $texts, ?ApplicationDecision $decision): Outcome
    {
        $feedback = $template->outcomeFeedback;

        return $decision === ApplicationDecision::Rejected
            ? new Outcome(
                when: new ApplicationDecidedCorrectly,
                effects: [Metric::Reliability->value => 1],
                otherwiseEffects: [Metric::CustomerSatisfaction->value => -2],
                feedback: $texts->fill($feedback['rejected_invalid']),
                otherwiseFeedback: $texts->fill($feedback['rejected_valid']),
            )
            : new Outcome(
                when: new ApplicationDecidedCorrectly,
                effects: [Metric::CustomerSatisfaction->value => 1],
                otherwiseEffects: [Metric::Reliability->value => -2],
                feedback: $texts->fill($feedback['approved_valid']),
                otherwiseFeedback: $texts->fill($feedback['approved_invalid']),
            );
    }
}
