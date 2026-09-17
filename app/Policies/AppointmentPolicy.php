<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function delete(User $player, Appointment $appointment): bool
    {
        return $appointment->failed_at === null
            && $appointment->booked_by_employment_id !== null
            && $appointment->booked_by_employment_id === $player->employment?->id;
    }
}
