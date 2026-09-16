<?php

namespace App\Careers;

use App\Mailbox\EmailDraft;
use App\Models\JobApplication;

class OfferLetter
{
    public function compose(JobApplication $application): EmailDraft
    {
        $opening = $application->jobOpening;
        $company = $opening->company;
        $replacements = [
            'company' => $company->name,
            'job' => $opening->title,
            'salary' => $opening->daily_salary,
            'contact' => $company->hr_contact_name,
            'note' => $company->hiring_note,
        ];

        return new EmailDraft(
            senderName: $company->hr_contact_name,
            senderAddress: $company->hr_contact_address,
            subject: __('game_mail.offer.subject', $replacements, $company->locale),
            body: __('game_mail.offer.body', $replacements, $company->locale),
        );
    }
}
