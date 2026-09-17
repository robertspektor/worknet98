<?php

namespace App\Career\Promotions;

use App\Mailbox\EmailDraft;
use App\Models\PromotionOffer;
use App\Models\PromotionOfferOption;
use Carbon\CarbonImmutable;
use LogicException;

class PromotionOfferLetter
{
    public function compose(PromotionOffer $offer): EmailDraft
    {
        $employment = $offer->employment;
        $superior = $employment->position->reportsTo ?? throw new LogicException("Position [{$employment->position->slug}] reports to nobody.");
        $locale = $employment->company->locale;
        $replacements = [
            'manager' => $superior->person->name,
            'salary' => $employment->daily_salary,
            'month' => CarbonImmutable::parse("{$offer->performanceReview->period}-01")->addMonth()->settings(['locale' => $locale])->translatedFormat('F Y'),
        ];

        return new EmailDraft(
            senderName: $superior->person->name,
            senderAddress: $superior->work_address,
            subject: __('game_mail.promotion_offer.subject', $replacements, $locale),
            body: implode("\n\n", [
                __('game_mail.promotion_offer.intro', $replacements, $locale),
                $offer->options->map(fn (PromotionOfferOption $option): string => __('game_mail.promotion_offer.option', [
                    'position' => $option->position->title,
                    'salary' => $option->daily_salary,
                ], $locale))->implode("\n"),
                __('game_mail.promotion_offer.outro', $replacements, $locale),
            ]),
        );
    }
}
