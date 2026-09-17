<?php

namespace App\Career;

use App\Career\Promotions\PromotionOfferer;
use App\Cases\MetricBook;
use App\Game\GameClock;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\PerformanceReview;
use App\Work\ContractPeriods;
use App\Work\LedgerReason;
use App\Work\MonthlySalary;
use App\Work\WorkedTime;
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
        private readonly PromotionOfferer $promotions,
        private readonly EmployeeOfTheMonth $awards,
        private readonly ContractPeriods $periods,
        private readonly WorkedTime $workedTime,
        private readonly MonthlySalary $salary,
        private readonly PayslipLetter $payslip,
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

        $this->awards->awardFor($period);

        return $reviewed;
    }

    private function review(Employment $employment, CarbonImmutable $period): void
    {
        DB::transaction(function () use ($employment, $period): void {
            $performance = MonthlyPerformance::of($employment, $this->metrics);
            $rating = ReviewRating::forScore($performance->score());

            $review = PerformanceReview::create([
                'employment_id' => $employment->id,
                'position_id' => $employment->position_id,
                'period' => $period->format('Y-m'),
                'metric_totals' => $performance->totals,
                'metric_changes' => $performance->changes,
                'score' => $performance->score(),
                'rating' => $rating,
                'bonus' => $rating === ReviewRating::Excellent ? $employment->daily_salary * self::BONUS_DAILY_SALARIES : 0,
            ]);

            $warnings = $this->warningsOf($employment);
            $this->payBonus($review);
            $this->paySalary($review, $period);
            $this->mailbox->deliver($employment->user, $this->letter->compose($review, $warnings, self::WARNINGS_UNTIL_DISMISSAL), $employment);

            if ($warnings >= self::WARNINGS_UNTIL_DISMISSAL) {
                $this->dismissal->dismiss($employment);

                return;
            }

            $this->promotions->offerIfEligible($review);
        });
    }

    private function paySalary(PerformanceReview $review, CarbonImmutable $period): void
    {
        $employment = $review->employment;
        $workedSeconds = $this->workedTime->secondsIn($employment, $this->periods->of($period));
        $paid = $this->salary->earnedFor($employment, $workedSeconds);

        if ($paid > 0) {
            LedgerEntry::create(['user_id' => $employment->user_id, 'amount' => $paid, 'reason' => LedgerReason::Salary]);
        }

        $this->mailbox->deliver($employment->user, $this->payslip->compose($review, $workedSeconds, $paid), $employment);
    }

    private function payBonus(PerformanceReview $review): void
    {
        if ($review->bonus > 0) {
            LedgerEntry::create(['user_id' => $review->employment->user_id, 'amount' => $review->bonus, 'reason' => LedgerReason::Bonus]);
        }
    }

    private function warningsOf(Employment $employment): int
    {
        return $employment->performanceReviews()->where('position_id', $employment->position_id)->where('rating', ReviewRating::Poor)->count();
    }
}
