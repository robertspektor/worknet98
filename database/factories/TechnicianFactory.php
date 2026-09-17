<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Technician;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->unique()->name();

        return [
            'branch_id' => Branch::factory(),
            'slug' => Str::slug($name),
            'name' => $name,
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
