<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;
use App\Workplace\Availability;

readonly class AppointmentWithinAvailability implements Condition
{
    public function __construct(
        private string $customer,
        private Availability $availability,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        $slot = $state->appointmentFor($this->customer)?->slot;

        return $slot !== null && $this->availability->includes($slot);
    }
}
