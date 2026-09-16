<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class AppointmentBetween implements Condition
{
    public function __construct(
        private string $customer,
        private string $from,
        private string $to,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        $slot = $state->appointmentFor($this->customer)?->slot;

        return $slot !== null && $slot >= $this->from && $slot < $this->to;
    }
}
