<?php

namespace App\World\Events;

use Carbon\CarbonImmutable;

readonly class PlannedEvent
{
    public function __construct(
        public string $key,
        public EventDefinition $definition,
        public CarbonImmutable $occursAt,
    ) {}
}
