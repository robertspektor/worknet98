<?php

namespace Database\Factories;

use App\Logistics\Vehicle;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'person_id' => Person::factory(),
            'vehicle' => Vehicle::Van,
            'busy_tours' => [],
        ];
    }

    public function truck(): static
    {
        return $this->state(fn (): array => ['vehicle' => Vehicle::Truck]);
    }
}
