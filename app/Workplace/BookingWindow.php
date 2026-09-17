<?php

namespace App\Workplace;

use App\Game\GameClock;
use Carbon\CarbonImmutable;

class BookingWindow
{
    public function __construct(private readonly GameClock $clock) {}

    private const WORK_DAYS = 5;

    /**
     * @return list<CarbonImmutable>
     */
    public function days(): array
    {
        $days = [];
        $day = $this->clock->today();

        while (count($days) < self::WORK_DAYS) {
            $day = $day->addDay();

            if ($day->isWeekday()) {
                $days[] = $day;
            }
        }

        return $days;
    }

    public function contains(CarbonImmutable $date): bool
    {
        return collect($this->days())->contains(fn (CarbonImmutable $day): bool => $day->isSameDay($date));
    }

    public function workDaysBetween(CarbonImmutable $from, CarbonImmutable $to): int
    {
        return (int) $from->startOfDay()->diffInWeekdays($to->startOfDay());
    }
}
