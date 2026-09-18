<?php

namespace App\Milestones;

readonly class MilestoneSender
{
    public function __construct(
        public string $name,
        public string $address,
    ) {}
}
