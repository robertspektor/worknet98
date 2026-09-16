<?php

namespace App\Workplace;

use App\Models\Appointment;
use App\Models\Technician;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

readonly class Schedule
{
    /**
     * @param  list<CarbonImmutable>  $days
     * @param  Collection<int, Technician>  $technicians
     * @param  Collection<int, Appointment>  $appointments
     */
    public function __construct(
        public array $days,
        public Collection $technicians,
        public Collection $appointments,
    ) {}
}
