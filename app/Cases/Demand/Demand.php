<?php

namespace App\Cases\Demand;

use App\Cases\Templates\CaseTemplate;
use Carbon\CarbonImmutable;

readonly class Demand
{
    public function __construct(
        public string $key,
        public CarbonImmutable $opensAt,
        public CaseTemplate $template,
    ) {}
}
