<?php

namespace App\Milestones;

use App\Mailbox\EmailDraft;

class MilestoneLetter
{
    public function compose(MilestoneSender $sender, string $milestoneKey, string $locale): EmailDraft
    {
        $replacements = ['sender' => $sender->name];

        return new EmailDraft(
            senderName: $sender->name,
            senderAddress: $sender->address,
            subject: __("milestone.{$milestoneKey}.mail_subject", $replacements, $locale),
            body: __("milestone.{$milestoneKey}.mail_body", $replacements, $locale),
        );
    }
}
