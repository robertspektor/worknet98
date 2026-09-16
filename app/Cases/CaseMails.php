<?php

namespace App\Cases;

use App\Mailbox\EmailDraft;
use App\Models\Company;
use App\Models\Customer;

class CaseMails
{
    public function request(CaseDefinition $definition, Customer $customer): EmailDraft
    {
        return new EmailDraft(
            senderName: $customer->name,
            senderAddress: $customer->email_address,
            subject: $definition->requestMail['subject'],
            body: $definition->requestMail['body'],
        );
    }

    /**
     * @param  list<string>  $feedback
     */
    public function feedback(CaseDefinition $definition, Company $company, array $feedback): EmailDraft
    {
        $mail = $definition->feedbackMail;

        return $this->fromManager($company, $mail['subject'], implode("\n\n", [$mail['intro'], ...$feedback, $mail['outro']]));
    }

    public function reminder(CaseDefinition $definition, Company $company): EmailDraft
    {
        return $this->fromManager($company, $definition->reminderMail['subject'], $definition->reminderMail['body']);
    }

    private function fromManager(Company $company, string $subject, string $body): EmailDraft
    {
        return new EmailDraft(
            senderName: (string) $company->manager_name,
            senderAddress: (string) $company->manager_address,
            subject: $subject,
            body: $body,
        );
    }
}
