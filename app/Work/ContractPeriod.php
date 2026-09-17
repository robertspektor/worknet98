<?php

namespace App\Work;

use Carbon\CarbonImmutable;

readonly class ContractPeriod
{
    public function __construct(
        public CarbonImmutable $startsOn,
        public CarbonImmutable $endsOn,
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
    ) {}
}
