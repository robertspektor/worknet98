<?php

namespace App\Metrics;

readonly class FunnelRow
{
    public function __construct(
        public string $label,
        public int $players,
        public int $lost,
    ) {}
}
