<?php

namespace App\Cases;

use App\Mailbox\EmailDraft;
use App\Models\Customer;
use App\Models\Employment;
use LogicException;

class CaseMails
{
    public function request(CaseDefinition $definition, Customer $customer): EmailDraft
    {
        return new EmailDraft(
            senderName: $customer->person->name,
            senderAddress: $customer->emailAddress() ?? throw new LogicException("Customer [{$customer->id}] has no e-mail address."),
            subject: $definition->requestMail['subject'],
            body: $definition->requestMail['body'],
        );
    }

    /**
     * @param  list<string>  $feedback
     */
    public function feedback(CaseDefinition $definition, Employment $employment, array $feedback): EmailDraft
    {
        $mail = $definition->feedbackMail;

        return $this->fromSuperior($employment, $mail['subject'], implode("\n\n", [$mail['intro'], ...$feedback, $mail['outro']]));
    }

    /**
     * @param  array{subject: string, body: string}  $briefingMail
     */
    public function briefing(array $briefingMail, Employment $employment): EmailDraft
    {
        return $this->fromSuperior($employment, $briefingMail['subject'], $briefingMail['body']);
    }

    public function reminder(CaseDefinition $definition, Employment $employment): EmailDraft
    {
        return $this->fromSuperior($employment, $definition->reminderMail['subject'], $definition->reminderMail['body']);
    }

    public function fromSuperior(Employment $employment, string $subject, string $body): EmailDraft
    {
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");

        return new EmailDraft(
            senderName: $superior->person->name,
            senderAddress: $superior->work_address,
            subject: $subject,
            body: $body,
        );
    }
}
