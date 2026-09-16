<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;

readonly class CalendarEntryForAppointment implements Condition
{
    public function __construct(private string $customer) {}

    public function isMetBy(CaseState $state): bool
    {
        $appointment = $state->appointmentFor($this->customer);

        return $appointment !== null && $state->hasCalendarEntryAt($appointment->date, $appointment->slot);
    }
}
