<?php

namespace App\Milestones;

use Carbon\CarbonImmutable;

readonly class MilestoneProgress
{
    public function __construct(
        public string $key,
        public ?CarbonImmutable $achievedAt,
    ) {}

    public function isAchieved(): bool
    {
        return $this->achievedAt !== null;
    }
}
