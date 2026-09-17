<?php

namespace Database\Factories;

use App\Hardware\HardwareSlot;
use App\Models\HardwarePart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HardwarePart>
 */
class HardwarePartFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'slot' => HardwareSlot::Cpu,
            'speed_mhz' => 133,
            'is_starter' => false,
            'price' => null,
        ];
    }

    public function starter(int $speedMhz = 75): static
    {
        return $this->state(fn (): array => ['is_starter' => true, 'speed_mhz' => $speedMhz]);
    }

    public function forSale(int $price = 380): static
    {
        return $this->state(fn (): array => ['price' => $price]);
    }
}
