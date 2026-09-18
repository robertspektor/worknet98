<?php

namespace App\Milestones;

use App\Milestones\Conditions\MilestoneCondition;

readonly class Milestone
{
    public function __construct(
        public string $key,
        public MilestoneCondition $condition,
        public ?string $senderKey = null,
    ) {}
}
