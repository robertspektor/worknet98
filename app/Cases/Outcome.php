<?php

namespace App\Cases;

use App\Cases\Conditions\Condition;

readonly class Outcome
{
    /**
     * @param  array<string, int>  $effects
     * @param  array<string, int>  $otherwiseEffects
     */
    public function __construct(
        public Condition $when,
        public array $effects,
        public array $otherwiseEffects,
        public string $feedback,
        public string $otherwiseFeedback,
    ) {}
}
