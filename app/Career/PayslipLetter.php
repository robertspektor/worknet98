<?php

namespace App\Career;

use App\Mailbox\EmailDraft;
use App\Models\PerformanceReview;
use App\Work\MonthlySalary;
use Carbon\CarbonImmutable;

class PayslipLetter
{
    private const SECONDS_PER_MINUTE = 60;

    public function __construct(private readonly MonthlySalary $salary) {}

    public function compose(PerformanceReview $review, int $workedSeconds, int $paid): EmailDraft
    {
        $employment = $review->employment;
        $company = $employment->company;
        $locale = $company->locale;
        $replacements = [
            'month' => CarbonImmutable::parse("{$review->period}-01")->settings(['locale' => $locale])->translatedFormat('F Y'),
            'company' => $company->name,
            'worked' => (int) round($workedSeconds / self::SECONDS_PER_MINUTE),
            'target' => (int) config('game.work.target_minutes'),
            'full' => $this->salary->fullFor($employment),
            'salary' => $paid,
            'bonus' => $review->bonus,
            'total' => $paid + $review->bonus,
            'contact' => $company->hr_contact_name,
        ];

        return new EmailDraft(
            senderName: $company->hr_contact_name,
            senderAddress: $company->hr_contact_address,
            subject: __('game_mail.payslip.subject', $replacements, $locale),
            body: implode("\n\n", array_filter([
                __('game_mail.payslip.intro', $replacements, $locale),
                __('game_mail.payslip.lines', $replacements, $locale),
                $paid < $replacements['full'] ? __('game_mail.payslip.short_time', $replacements, $locale) : null,
                __('game_mail.payslip.outro', $replacements, $locale),
            ])),
        );
    }
}
