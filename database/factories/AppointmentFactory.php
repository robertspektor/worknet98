<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Customer;
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
            'branch_id' => Branch::factory(),
            'booked_by_employment_id' => null,
            'customer_id' => fn (array $attributes): int => Customer::factory()->create(['branch_id' => $attributes['branch_id']])->id,
            'technician_id' => fn (array $attributes): int => Technician::factory()->create(['branch_id' => $attributes['branch_id']])->id,
            'date' => now()->addWeekday()->toDateString(),
            'slot' => '10:00',
        ];
    }
}
