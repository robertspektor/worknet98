<?php

namespace App\Career;

use App\Mailbox\EmailDraft;
use App\Models\Employment;

class DismissalLetter
{
    public function compose(Employment $employment): EmailDraft
    {
        $company = $employment->company;
        $replacements = [
            'company' => $company->name,
            'position' => $employment->position->title,
            'contact' => $company->hr_contact_name,
        ];

        return new EmailDraft(
            senderName: $company->hr_contact_name,
            senderAddress: $company->hr_contact_address,
            subject: __('game_mail.dismissal.subject', $replacements, $company->locale),
            body: __('game_mail.dismissal.body', $replacements, $company->locale),
        );
    }
}
