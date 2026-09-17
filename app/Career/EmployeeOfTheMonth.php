<?php

namespace App\Career;

use App\Forum\ForumWriter;
use App\Models\Branch;
use App\Models\EmployeeAward;
use App\Models\PerformanceReview;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeOfTheMonth
{
    public function __construct(private readonly ForumWriter $forum) {}

    public function awardFor(CarbonImmutable $period): int
    {
        $awarded = 0;

        foreach ($this->branchesWithReviews($period->format('Y-m')) as $branch) {
            if ($this->award($branch, $period)) {
                $awarded++;
            }
        }

        return $awarded;
    }

    /**
     * @return Collection<int, Branch>
     */
    private function branchesWithReviews(string $month): Collection
    {
        $branchIds = PerformanceReview::query()
            ->where('period', $month)
            ->join('employments', 'employments.id', '=', 'performance_reviews.employment_id')
            ->join('positions', 'positions.id', '=', 'employments.position_id')
            ->distinct()
            ->pluck('positions.branch_id');

        return Branch::query()->whereIn('id', $branchIds)->with('company')->get();
    }

    private function award(Branch $branch, CarbonImmutable $period): bool
    {
        $month = $period->format('Y-m');
        $best = PerformanceReview::query()
            ->where('period', $month)
            ->whereRelation('employment.position', 'branch_id', $branch->id)
            ->with('employment.position.person')
            ->orderByDesc('score')
            ->oldest('id')
            ->first();

        if ($best === null || $best->score <= 0 || EmployeeAward::query()->whereBelongsTo($branch)->where('period', $month)->exists()) {
            return false;
        }

        DB::transaction(function () use ($branch, $best, $month): void {
            EmployeeAward::create([
                'branch_id' => $branch->id,
                'employment_id' => $best->employment_id,
                'period' => $month,
                'score' => $best->score,
            ]);

            $this->announce($branch, $best, $month);
        });

        return true;
    }

    private function announce(Branch $branch, PerformanceReview $best, string $month): void
    {
        $manager = $branch->positions()->whereNull('reports_to_position_id')->first();

        if ($manager === null) {
            return;
        }

        $this->forum->announce(
            $branch->company,
            $manager,
            __('career.award.title', ['month' => $month], $branch->company->locale),
            __('career.award.body', [
                'name' => $best->employment->position->person->name,
                'title' => $best->employment->position->title,
                'month' => $month,
            ], $branch->company->locale),
        );
    }
}
