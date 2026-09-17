<?php

namespace App\Cases\Deadlines;

use App\Mailbox\EmailDraft;
use App\Models\Employment;
use App\Models\Position;
use App\Models\WorkCase;
use LogicException;

class TakeoverLetter
{
    public function handedOver(WorkCase $workCase, Employment $employment, Position $colleague): EmailDraft
    {
        return $this->compose('handed_over', $workCase, $employment, ['colleague' => $colleague->npc_name]);
    }

    public function lost(WorkCase $workCase, Employment $employment): EmailDraft
    {
        return $this->compose('lost', $workCase, $employment, []);
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function compose(string $kind, WorkCase $workCase, Employment $employment, array $replacements): EmailDraft
    {
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");
        $replacements = [...$replacements, 'customer' => $workCase->customer->name, 'manager' => $superior->npc_name];
        $locale = $employment->company->locale;

        return new EmailDraft(
            senderName: $superior->npc_name,
            senderAddress: $superior->npc_address,
            subject: __("game_mail.case_{$kind}.subject", $replacements, $locale),
            body: __("game_mail.case_{$kind}.body", $replacements, $locale),
        );
    }
}
