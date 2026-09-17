<?php

namespace App\Work;

use App\Game\GameClock;

class ContractPeriods
{
    public function __construct(private readonly GameClock $clock) {}

    public function current(): ContractPeriod
    {
        $month = $this->clock->today()->startOfMonth();
        $nextMonth = $month->addMonthNoOverflow();

        return new ContractPeriod(
            startsOn: $month,
            endsOn: $nextMonth->subDay(),
            startsAt: $this->clock->toReal($month),
            endsAt: $this->clock->toReal($nextMonth),
        );
    }
}
