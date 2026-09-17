<?php

namespace App\World\Population;

readonly class Livelihood
{
    public function __construct(
        public Occupation $occupation,
        public ?string $profession,
        public int $monthlyIncome,
    ) {}
}
