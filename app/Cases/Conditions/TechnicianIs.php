<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class TechnicianIs implements Condition
{
    public function __construct(
        private string $customer,
        private string $technician,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        return $state->appointmentFor($this->customer)?->technician->person->slug === $this->technician;
    }
}
