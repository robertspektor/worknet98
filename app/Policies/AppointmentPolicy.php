<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function delete(User $player, Appointment $appointment): bool
    {
        return $appointment->employment_id === $player->employment?->id;
    }
}
