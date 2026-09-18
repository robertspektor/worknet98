<?php

namespace App\Work\Goals;

use Carbon\CarbonImmutable;

readonly class WeekPeriod
{
    public function __construct(
        public string $key,
        public CarbonImmutable $startsOn,
        public CarbonImmutable $endsOn,
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
    ) {}
}
