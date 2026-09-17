<?php

namespace App\Careers;

use App\Career\ReviewRating;
use App\Models\JobApplication;
use App\Models\PerformanceReview;
use Illuminate\Support\Collection;

class CandidateRanking
{
    /**
     * @param  Collection<int, JobApplication>  $applications
     * @return Collection<int, JobApplication>
     */
    public function sort(Collection $applications): Collection
    {
        $excellentReviews = $this->excellentReviewsOf($applications);

        return $applications
            ->sortBy([
                fn (JobApplication $application): int => -($excellentReviews[$application->user_id] ?? 0),
                fn (JobApplication $application): int => $application->id,
            ])
            ->values();
    }

    /**
     * @param  Collection<int, JobApplication>  $applications
     * @return array<int, int>
     */
    private function excellentReviewsOf(Collection $applications): array
    {
        return PerformanceReview::query()
            ->join('employments', 'employments.id', '=', 'performance_reviews.employment_id')
            ->whereIn('employments.user_id', $applications->pluck('user_id')->all())
            ->where('performance_reviews.rating', ReviewRating::Excellent)
            ->groupBy('employments.user_id')
            ->selectRaw('employments.user_id, count(*) as excellent')
            ->pluck('excellent', 'employments.user_id')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();
    }
}
