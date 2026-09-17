<?php

namespace App\Logistics\Supply;

use Carbon\CarbonImmutable;

readonly class PlannedSupplyOrder
{
    private const DUE_TIME = '12:00';

    public function __construct(
        public string $key,
        public SupplyRoute $route,
        public CarbonImmutable $placedAt,
    ) {}

    public function dueAt(): CarbonImmutable
    {
        return $this->placedAt->startOfDay()->addWeekdays($this->route->leadWorkDays)->setTimeFromTimeString(self::DUE_TIME);
    }
}
