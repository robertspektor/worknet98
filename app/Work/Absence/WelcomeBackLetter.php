<?php

namespace App\Work\Absence;

use App\Mailbox\EmailDraft;
use App\Models\Employment;
use LogicException;

class WelcomeBackLetter
{
    public function compose(Employment $employment, AbsenceReport $report): EmailDraft
    {
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");
        $locale = $employment->company->locale;
        $replacements = [
            'manager' => $superior->person->name,
            'away' => trans_choice('game_mail.welcome_back.days', $report->awayDays, [], $locale),
            'balance' => $report->balance,
        ];

        return new EmailDraft(
            senderName: $superior->person->name,
            senderAddress: $superior->work_address,
            subject: __('game_mail.welcome_back.subject', $replacements, $locale),
            body: implode("\n\n", array_filter([
                __('game_mail.welcome_back.intro', $replacements, $locale),
                $this->lines($report, $locale),
                __('game_mail.welcome_back.outro', $replacements, $locale),
            ])),
        );
    }

    private function lines(AbsenceReport $report, string $locale): string
    {
        $lines = array_filter([
            $report->waitingCases > 0 ? trans_choice('game_mail.welcome_back.waiting', $report->waitingCases, [], $locale) : null,
            $report->takenOverCases > 0 ? trans_choice('game_mail.welcome_back.taken_over', $report->takenOverCases, [], $locale) : null,
            $report->newMails > 0 ? trans_choice('game_mail.welcome_back.mails', $report->newMails, [], $locale) : null,
            __('game_mail.welcome_back.balance', ['balance' => $report->balance], $locale),
        ]);

        return implode("\n", $lines);
    }
}
