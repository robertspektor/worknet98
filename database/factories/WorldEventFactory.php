<?php

namespace Database\Factories;

use App\Models\Person;
use App\Models\WorldEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WorldEvent>
 */
class WorldEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'city_id' => fn (array $attributes): int => Person::query()->whereKey($attributes['person_id'])->sole()->city_id,
            'parent_id' => null,
            'key' => (string) Str::uuid(),
            'type' => 'dripping_pipe',
            'occurred_at' => now(),
        ];
    }
}
