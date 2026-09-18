<?php

namespace App\Metrics;

readonly class DayTwoReturn
{
    public function __construct(
        public int $returned,
        public int $eligible,
    ) {}

    public function percentage(): int
    {
        return $this->eligible === 0 ? 0 : (int) round($this->returned / $this->eligible * 100);
    }
}
