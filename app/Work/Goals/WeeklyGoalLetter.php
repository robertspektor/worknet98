<?php

namespace App\Work\Goals;

use App\Mailbox\EmailDraft;
use App\Models\Employment;
use LogicException;

class WeeklyGoalLetter
{
    public function compose(Employment $employment, WeeklyGoalProgress $progress): EmailDraft
    {
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");
        $locale = $employment->company->locale;
        $replacements = [
            'manager' => $superior->person->name,
            'cases' => $progress->resolvedCases,
            'target' => $progress->target,
            'bonus' => $progress->bonus,
        ];

        return new EmailDraft(
            senderName: $superior->person->name,
            senderAddress: $superior->work_address,
            subject: __('game_mail.weekly_goal.subject', $replacements, $locale),
            body: __('game_mail.weekly_goal.body', $replacements, $locale),
        );
    }
}
