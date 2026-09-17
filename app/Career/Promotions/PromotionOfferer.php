<?php

namespace App\Career\Promotions;

use App\Game\GameClock;
use App\Mailbox\Mailbox;
use App\Models\PerformanceReview;
use App\Models\Position;
use App\Models\PromotionOffer;
use Carbon\CarbonImmutable;

class PromotionOfferer
{
    private const VALID_FOR_GAME_MONTHS = 1;

    public function __construct(
        private readonly PromotionEligibility $eligibility,
        private readonly PromotionTargets $targets,
        private readonly PromotionOfferLetter $letter,
        private readonly Mailbox $mailbox,
        private readonly GameClock $clock,
    ) {}

    public function offerIfEligible(PerformanceReview $review): ?PromotionOffer
    {
        $employment = $review->employment;

        if (! $this->eligibility->isEligible($employment)) {
            return null;
        }

        $targets = $this->targets->vacantFor($employment);

        if ($targets->isEmpty()) {
            return null;
        }

        $offer = PromotionOffer::create([
            'employment_id' => $employment->id,
            'performance_review_id' => $review->id,
            'status' => PromotionOfferStatus::Pending,
            'expires_at' => $this->expiryAfter($review),
        ]);

        $targets->each(fn (Position $target) => $offer->options()->create([
            'position_id' => $target->id,
            'daily_salary' => $target->daily_salary,
        ]));

        $email = $this->mailbox->deliver($employment->user, $this->letter->compose($offer), $employment);
        $offer->update(['email_id' => $email->id]);

        return $offer;
    }

    private function expiryAfter(PerformanceReview $review): CarbonImmutable
    {
        $nextMonthClose = CarbonImmutable::parse("{$review->period}-01")->addMonths(1 + self::VALID_FOR_GAME_MONTHS);

        return $this->clock->toReal($nextMonthClose);
    }
}
