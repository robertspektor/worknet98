<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Employment;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employment_id' => Employment::factory(),
            'customer_id' => Customer::factory(),
            'technician_id' => Technician::factory(),
            'date' => now()->addWeekday()->toDateString(),
            'slot' => '10:00',
        ];
    }
}
