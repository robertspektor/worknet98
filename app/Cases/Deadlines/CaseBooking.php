<?php

namespace App\Cases\Deadlines;

use App\Models\Appointment;
use App\Models\WorkCase;

class CaseBooking
{
    public function isBookedByAssignee(WorkCase $workCase): bool
    {
        return Appointment::query()
            ->where('booked_by_employment_id', $workCase->employment_id)
            ->where('customer_id', $workCase->customer_id)
            ->exists();
    }
}
