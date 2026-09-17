<?php

namespace App\Careers;

use App\Mailbox\EmailDraft;
use App\Models\JobApplication;

class RejectionLetter
{
    public function compose(JobApplication $application): EmailDraft
    {
        $opening = $application->jobOpening;
        $company = $opening->company;
        $replacements = [
            'company' => $company->name,
            'job' => $opening->title,
            'contact' => $company->hr_contact_name,
        ];

        return new EmailDraft(
            senderName: $company->hr_contact_name,
            senderAddress: $company->hr_contact_address,
            subject: __('game_mail.rejection.subject', $replacements, $company->locale),
            body: __('game_mail.rejection.body', $replacements, $company->locale),
        );
    }
}
