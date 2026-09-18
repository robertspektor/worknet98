<?php

namespace App\Milestones\Conditions;

use App\Career\Promotions\PromotionOfferStatus;
use App\Models\PromotionOffer;
use App\Models\User;

readonly class PromotionCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return PromotionOffer::query()
            ->where('status', PromotionOfferStatus::Accepted)
            ->whereRelation('employment', 'user_id', $player->id)
            ->count() >= $this->count;
    }
}
