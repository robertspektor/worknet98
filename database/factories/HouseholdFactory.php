<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Household>
 */
class HouseholdFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'district' => fake()->city(),
            'street' => fake()->unique()->streetAddress(),
            'phone' => fake()->numerify('555-####'),
        ];
    }
}
