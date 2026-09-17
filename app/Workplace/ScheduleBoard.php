<?php

namespace App\Workplace;

use App\Models\Branch;

class ScheduleBoard
{
    public function __construct(private readonly BookingWindow $window) {}

    public function for(Branch $branch): Schedule
    {
        $days = $this->window->days();

        return new Schedule(
            days: $days,
            technicians: $branch->technicians()->with('person')->get()->sortBy('person.name')->values(),
            appointments: $branch->appointments()
                ->with(['customer.person', 'customer.workCases.partShipment', 'bookedBy.position', 'bookedByPosition.person'])
                ->whereBetween('date', [$days[0]->toDateString(), $days[count($days) - 1]->toDateString()])
                ->orderBy('date')
                ->orderBy('slot')
                ->get(),
        );
    }
}
