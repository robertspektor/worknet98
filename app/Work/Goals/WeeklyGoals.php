<?php

namespace App\Work\Goals;

use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\LedgerEntry;
use App\Models\WeeklyGoal;
use App\Work\LedgerReason;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class WeeklyGoals
{
    public function __construct(
        private readonly WeekPeriods $weeks,
        private readonly WeeklyGoalBoard $board,
        private readonly WeeklyGoalLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function awardReached(): int
    {
        $weeks = [$this->weeks->previous(), $this->weeks->current()];
        $awarded = 0;

        Employment::query()
            ->active()
            ->with(['position.reportsTo.person', 'company', 'user'])
            ->lazyById()
            ->each(function (Employment $employment) use ($weeks, &$awarded): void {
                foreach ($weeks as $week) {
                    $awarded += $this->award($employment, $week) ? 1 : 0;
                }
            });

        return $awarded;
    }

    private function award(Employment $employment, WeekPeriod $week): bool
    {
        $progress = $this->board->for($employment, $week);

        if ($progress->isAchieved || $progress->resolvedCases < $progress->target) {
            return false;
        }

        try {
            DB::transaction(function () use ($employment, $week, $progress): void {
                WeeklyGoal::create([
                    'employment_id' => $employment->id,
                    'week' => $week->key,
                    'target' => $progress->target,
                    'resolved_cases' => $progress->resolvedCases,
                    'bonus' => $progress->bonus,
                    'achieved_at' => now(),
                ]);

                LedgerEntry::create(['user_id' => $employment->user_id, 'amount' => $progress->bonus, 'reason' => LedgerReason::Bonus]);
                $this->mailbox->deliver($employment->user, $this->letter->compose($employment, $progress), $employment);
            });
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }
}
