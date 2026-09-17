<?php

namespace App\Career\Promotions;

use App\Career\ReviewRating;
use App\Models\Employment;
use App\Models\PerformanceReview;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromotionEligibility
{
    public function isEligible(Employment $employment): bool
    {
        $required = $employment->position->promotion_excellent_reviews;

        return $required !== null
            && ! $this->hasOpenOffer($employment)
            && $this->excellentReviewsSinceLastOffer($employment) >= $required
            && $this->hasCleanRecentMonths($employment);
    }

    private function hasOpenOffer(Employment $employment): bool
    {
        return $employment->promotionOffers()->open()->exists();
    }

    private function excellentReviewsSinceLastOffer(Employment $employment): int
    {
        $lastOfferedReviewId = $employment->promotionOffers()->max('performance_review_id') ?? 0;

        return $this->reviewsInPosition($employment)
            ->where('id', '>', $lastOfferedReviewId)
            ->where('rating', ReviewRating::Excellent)
            ->count();
    }

    private function hasCleanRecentMonths(Employment $employment): bool
    {
        $months = $employment->position->promotion_clean_months ?? 0;

        return $this->reviewsInPosition($employment)
            ->latest('period')
            ->limit($months)
            ->get()
            ->doesntContain(fn (PerformanceReview $review): bool => $review->rating === ReviewRating::Poor);
    }

    /**
     * @return HasMany<PerformanceReview, Employment>
     */
    private function reviewsInPosition(Employment $employment): HasMany
    {
        return $employment->performanceReviews()->where('position_id', $employment->position_id);
    }
}
