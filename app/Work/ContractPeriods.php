<?php

namespace App\Work;

use App\Game\GameClock;
use Carbon\CarbonImmutable;

class ContractPeriods
{
    public function __construct(private readonly GameClock $clock) {}

    public function current(): ContractPeriod
    {
        return $this->of($this->clock->today()->startOfMonth());
    }

    public function of(CarbonImmutable $month): ContractPeriod
    {
        $nextMonth = $month->addMonthNoOverflow();

        return new ContractPeriod(
            startsOn: $month,
            endsOn: $nextMonth->subDay(),
            startsAt: $this->clock->toReal($month),
            endsAt: $this->clock->toReal($nextMonth),
        );
    }
}
