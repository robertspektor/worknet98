<?php

namespace App\Workplace;

use App\Models\Employment;

class ScheduleBoard
{
    public function __construct(private readonly BookingWindow $window) {}

    public function for(Employment $employment): Schedule
    {
        $days = $this->window->days();

        return new Schedule(
            days: $days,
            technicians: $employment->company->technicians()->orderBy('name')->get(),
            appointments: $employment->appointments()
                ->with('customer')
                ->whereBetween('date', [$days[0]->toDateString(), $days[count($days) - 1]->toDateString()])
                ->get(),
        );
    }
}
