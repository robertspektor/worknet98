<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Work\ShiftStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ShiftStatus $resource
 */
class ShiftStatusResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->resource;
        $shift = $status->latestShift;

        return [
            'status' => $status->state(),
            'clocked_in_at' => $shift?->isOnDuty() ? app(GameClock::class)->display($shift->clocked_in_at) : null,
            'clocked_out_automatically' => $status->wasClockedOutAutomatically(),
            'worked_seconds' => $status->workedSeconds,
            'target_seconds' => $status->targetSeconds,
            'full_salary' => $status->fullSalary,
            'earned_salary' => $status->earnedSalary,
            'weekly_goal' => [
                'target' => $status->weeklyGoal->target,
                'resolved_cases' => $status->weeklyGoal->resolvedCases,
                'bonus' => $status->weeklyGoal->bonus,
                'achieved' => $status->weeklyGoal->isAchieved,
                'ends_at' => $status->weeklyGoal->week->endsAt->toIso8601String(),
            ],
            'period' => [
                'starts_on' => $status->period->startsOn->toDateString(),
                'ends_on' => $status->period->endsOn->toDateString(),
                'ends_at' => $status->period->endsAt->toIso8601String(),
            ],
        ];
    }
}
