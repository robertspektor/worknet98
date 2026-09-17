<?php

namespace App\Career;

use App\Cases\MetricBook;
use App\Game\GameClock;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\PerformanceReview;
use App\Work\LedgerReason;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class MonthClose
{
    private const BONUS_DAILY_SALARIES = 3;

    private const WARNINGS_UNTIL_DISMISSAL = 3;

    public function __construct(
        private readonly GameClock $clock,
        private readonly MetricBook $metrics,
        private readonly ReviewLetter $letter,
        private readonly Mailbox $mailbox,
        private readonly Dismissal $dismissal,
    ) {}

    public function closeDue(): int
    {
        $period = $this->clock->today()->startOfMonth()->subMonth();
        $reviewed = 0;

        Employment::query()
            ->active()
            ->where('hired_at', '<', $this->clock->toReal($period->endOfMonth()))
            ->whereDoesntHave('performanceReviews', fn ($query) => $query->where('period', $period->format('Y-m')))
            ->with(['user', 'company', 'position.reportsTo'])
            ->lazyById()
            ->each(function (Employment $employment) use ($period, &$reviewed): void {
                $this->review($employment, $period);
                $reviewed++;
            });

        return $reviewed;
    }

    private function review(Employment $employment, CarbonImmutable $period): void
    {
        DB::transaction(function () use ($employment, $period): void {
            $performance = MonthlyPerformance::of($employment, $this->metrics);
            $rating = ReviewRating::forScore($performance->score());

            $review = PerformanceReview::create([
                'employment_id' => $employment->id,
                'period' => $period->format('Y-m'),
                'metric_totals' => $performance->totals,
                'metric_changes' => $performance->changes,
                'score' => $performance->score(),
                'rating' => $rating,
                'bonus' => $rating === ReviewRating::Excellent ? $employment->daily_salary * self::BONUS_DAILY_SALARIES : 0,
            ]);

            $warnings = $this->warningsOf($employment);
            $this->payBonus($review);
            $this->mailbox->deliver($employment->user, $this->letter->compose($review, $warnings, self::WARNINGS_UNTIL_DISMISSAL), $employment);

            if ($warnings >= self::WARNINGS_UNTIL_DISMISSAL) {
                $this->dismissal->dismiss($employment);
            }
        });
    }

    private function payBonus(PerformanceReview $review): void
    {
        if ($review->bonus > 0) {
            LedgerEntry::create(['user_id' => $review->employment->user_id, 'amount' => $review->bonus, 'reason' => LedgerReason::Bonus]);
        }
    }

    private function warningsOf(Employment $employment): int
    {
        return $employment->performanceReviews()->where('rating', ReviewRating::Poor)->count();
    }
}
