<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class TechnicianHasSkill implements Condition
{
    public function __construct(
        private string $customer,
        private string $skill,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        return (bool) $state->appointmentFor($this->customer)?->technician->hasSkill($this->skill);
    }
}
