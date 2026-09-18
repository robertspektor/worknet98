<?php

namespace App\Metrics;

readonly class SessionLengths
{
    public function __construct(
        public int $sessions,
        public int $players,
        public int $medianMinutes,
        public int $longestMinutes,
    ) {}

    public function perPlayer(): float
    {
        return $this->players === 0 ? 0.0 : round($this->sessions / $this->players, 1);
    }
}
