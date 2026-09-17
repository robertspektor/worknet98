<?php

namespace App\World\Events;

readonly class FollowUp
{
    public function __construct(
        public string $type,
        public FollowUpTrigger $when,
    ) {}
}
