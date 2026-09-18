<?php

namespace App\Metrics;

use App\Models\PlayerSession;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ReturnRate
{
    public function dayTwo(): DayTwoReturn
    {
        $returned = 0;
        $eligible = 0;

        foreach ($this->sessionDaysPerPlayer() as $days) {
            $first = $days[0];

            if ($first > now()->subDays(2)->toDateString()) {
                continue;
            }

            $eligible++;
            $returned += in_array($this->dayAfter($first), $days, true) ? 1 : 0;
        }

        return new DayTwoReturn($returned, $eligible);
    }

    /**
     * @return array<int, list<string>>
     */
    private function sessionDaysPerPlayer(): array
    {
        return PlayerSession::query()
            ->orderBy('started_at')
            ->get(['user_id', 'started_at'])
            ->toBase()
            ->groupBy('user_id')
            ->map(fn (Collection $sessions): array => array_values(array_unique(
                $sessions->map(fn (PlayerSession $session): string => $session->started_at->toDateString())->all()
            )))
            ->values()
            ->all();
    }

    private function dayAfter(string $day): string
    {
        return CarbonImmutable::parse($day)->addDay()->toDateString();
    }
}
