<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class AppointmentWithinWorkDays implements Condition
{
    public function __construct(
        private string $customer,
        private int $days,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        $appointment = $state->appointmentFor($this->customer);

        return $appointment !== null && $state->workDaysSinceOpening($appointment->date) <= $this->days;
    }
}
