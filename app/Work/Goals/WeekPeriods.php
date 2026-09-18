<?php

namespace App\Work\Goals;

use App\Game\GameClock;
use Carbon\CarbonImmutable;

class WeekPeriods
{
    public function __construct(private readonly GameClock $clock) {}

    public function current(): WeekPeriod
    {
        return $this->of($this->clock->today());
    }

    public function previous(): WeekPeriod
    {
        return $this->of($this->clock->today()->subWeek());
    }

    public function of(CarbonImmutable $day): WeekPeriod
    {
        $startsOn = $day->startOfWeek();
        $endsOn = $startsOn->addDays(6);

        return new WeekPeriod(
            key: $startsOn->format('o-\WW'),
            startsOn: $startsOn,
            endsOn: $endsOn,
            startsAt: $this->clock->toReal($startsOn),
            endsAt: $this->clock->toReal($startsOn->addWeek()),
        );
    }
}
