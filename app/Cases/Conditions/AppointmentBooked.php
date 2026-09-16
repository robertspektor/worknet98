<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class AppointmentBooked implements Condition
{
    public function __construct(private string $customer) {}

    public function isMetBy(CaseState $state): bool
    {
        return $state->appointmentFor($this->customer) !== null;
    }
}
