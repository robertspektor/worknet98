<?php

namespace Database\Factories;

use App\Models\Household;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'household_id' => Household::factory(),
            'city_id' => fn (array $attributes): int => Household::query()->whereKey($attributes['household_id'])->sole()->city_id,
            'slug' => Str::slug($name),
            'name' => $name,
            'email_address' => fake()->unique()->safeEmail(),
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn (): array => ['name' => $name, 'slug' => Str::slug($name)]);
    }
}
