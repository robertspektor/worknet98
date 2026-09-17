<?php

namespace Database\Factories;

use App\Models\HardwarePart;
use App\Models\PlayerHardwarePart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerHardwarePart>
 */
class PlayerHardwarePartFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'hardware_part_id' => HardwarePart::factory()->forSale(),
            'installed_at' => null,
            'needs_thermal_paste' => false,
        ];
    }

    public function installed(): static
    {
        return $this->state(fn (): array => ['installed_at' => now()]);
    }

    public function withoutThermalPaste(): static
    {
        return $this->state(fn (): array => ['needs_thermal_paste' => true]);
    }
}
