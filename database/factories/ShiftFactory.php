<?php

namespace Database\Factories;

use App\Models\Employment;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employment_id' => Employment::factory(),
            'user_id' => fn (array $attributes): int => Employment::query()->whereKey($attributes['employment_id'])->sole()->user_id,
            'work_date' => now()->toDateString(),
            'clocked_in_at' => now(),
        ];
    }

    public function clockedOut(): static
    {
        return $this->state(fn (): array => ['clocked_out_at' => now()]);
    }
}
