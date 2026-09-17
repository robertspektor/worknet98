<?php

namespace App\Career;

use App\Mailbox\EmailDraft;
use App\Models\PerformanceReview;
use Carbon\CarbonImmutable;
use LogicException;

class ReviewLetter
{
    public function compose(PerformanceReview $review): EmailDraft
    {
        $employment = $review->employment;
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");
        $locale = $employment->company->locale;
        $replacements = [
            'month' => CarbonImmutable::parse("{$review->period}-01")->settings(['locale' => $locale])->translatedFormat('F Y'),
            'bonus' => $review->bonus,
            'manager' => $superior->npc_name,
        ];

        return new EmailDraft(
            senderName: $superior->npc_name,
            senderAddress: $superior->npc_address,
            subject: __('game_mail.review.subject', $replacements, $locale),
            body: implode("\n\n", [
                __("game_mail.review.{$review->rating->value}", $replacements, $locale),
                $this->metricLines($review, $locale),
                __('game_mail.review.outro', $replacements, $locale),
            ]),
        );
    }

    private function metricLines(PerformanceReview $review, string $locale): string
    {
        return collect($review->metric_changes)
            ->map(fn (int $change, string $metric): string => __("game_mail.review.metric.{$metric}", [], $locale).': '.sprintf('%+d', $change))
            ->implode("\n");
    }
}
