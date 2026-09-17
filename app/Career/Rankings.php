<?php

namespace App\Career;

use App\Models\Branch;
use App\Models\Employment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Rankings
{
    private const LIMIT = 20;

    /**
     * @return Collection<int, Employment>
     */
    public function ofBranch(Branch $branch): Collection
    {
        return $this->scored($this->ranked()->whereRelation('position', 'branch_id', $branch->id));
    }

    /**
     * @return Collection<int, Employment>
     */
    public function ofWorld(): Collection
    {
        return $this->scored($this->ranked());
    }

    /**
     * @param  Builder<Employment>  $ranked
     * @return Collection<int, Employment>
     */
    private function scored(Builder $ranked): Collection
    {
        return $ranked->get()->filter(fn (Employment $employment): bool => (int) ($employment->review_score ?? 0) > 0)->values();
    }

    /**
     * @return Builder<Employment>
     */
    private function ranked(): Builder
    {
        return Employment::query()
            ->active()
            ->withSum('performanceReviews as review_score', 'score')
            ->withCount(['performanceReviews as excellent_reviews' => fn (Builder $reviews) => $reviews->where('rating', ReviewRating::Excellent)])
            ->with(['position.person', 'company'])
            ->orderByDesc('review_score')
            ->orderBy('hired_at')
            ->limit(self::LIMIT);
    }
}
