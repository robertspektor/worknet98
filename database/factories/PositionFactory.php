<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Person;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'branch_id' => Branch::factory(),
            'job_opening_id' => null,
            'reports_to_position_id' => null,
            'person_id' => Person::factory()->named($name),
            'slug' => Str::slug($name),
            'title' => 'Office Assistant',
            'work_address' => Str::slug($name, '.').'@company.wn',
        ];
    }
}
