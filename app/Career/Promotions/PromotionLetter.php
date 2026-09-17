<?php

namespace App\Career\Promotions;

use App\Mailbox\EmailDraft;
use App\Models\Employment;
use LogicException;

class PromotionLetter
{
    public function compose(Employment $employment): EmailDraft
    {
        $position = $employment->position;
        $superior = $position->reportsTo ?? throw new LogicException("Position [{$position->slug}] reports to nobody.");
        $locale = $employment->company->locale;
        $replacements = [
            'position' => $position->title,
            'salary' => $employment->daily_salary,
            'predecessor' => $position->npc_name,
            'manager' => $superior->npc_name,
        ];

        return new EmailDraft(
            senderName: $superior->npc_name,
            senderAddress: $superior->npc_address,
            subject: __('game_mail.promotion.subject', $replacements, $locale),
            body: __('game_mail.promotion.body', $replacements, $locale),
        );
    }
}
