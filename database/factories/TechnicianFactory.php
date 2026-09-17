<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Person;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technician>
 */
class TechnicianFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'person_id' => Person::factory(),
            'skills' => ['plumbing'],
            'busy_slots' => [],
        ];
    }

    /**
     * @param  list<string>  $skills
     */
    public function skilled(array $skills): static
    {
        return $this->state(fn (): array => ['skills' => $skills]);
    }
}
