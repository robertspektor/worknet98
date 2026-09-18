<?php

namespace App\Milestones\Conditions;

use App\Career\ReviewRating;
use App\Models\PerformanceReview;
use App\Models\User;

readonly class ExcellentReviewCount implements MilestoneCondition
{
    public function __construct(private int $count) {}

    public function isMetBy(User $player): bool
    {
        return PerformanceReview::query()
            ->where('rating', ReviewRating::Excellent)
            ->whereRelation('employment', 'user_id', $player->id)
            ->count() >= $this->count;
    }
}
