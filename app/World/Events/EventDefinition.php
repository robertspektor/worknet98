<?php

namespace App\World\Events;

readonly class EventDefinition
{
    /**
     * @param  list<FollowUp>  $followUps
     */
    public function __construct(
        public string $type,
        public float $dailyRate,
        public ?string $service,
        public ?string $caseTemplate,
        public array $followUps,
    ) {}

    public function opensCase(): bool
    {
        return $this->service !== null && $this->caseTemplate !== null;
    }
}
