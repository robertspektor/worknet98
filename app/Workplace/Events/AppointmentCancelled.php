<?php

namespace App\Workplace\Events;

use App\Models\Appointment;
use Illuminate\Foundation\Events\Dispatchable;

class AppointmentCancelled
{
    use Dispatchable;

    public function __construct(public readonly Appointment $appointment) {}
}
