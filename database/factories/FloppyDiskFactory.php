<?php

namespace Database\Factories;

use App\FloppyDisks\FloppyDiskKind;
use App\Models\FloppyDisk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FloppyDisk>
 */
class FloppyDiskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'kind' => FloppyDiskKind::Data,
            'color' => 'black',
            'program' => null,
            'is_starter' => false,
            'price' => null,
        ];
    }

    public function forSale(int $price = 40): static
    {
        return $this->state(fn (): array => ['price' => $price]);
    }

    public function starter(): static
    {
        return $this->state(fn (): array => ['is_starter' => true]);
    }

    public function program(string $program): static
    {
        return $this->state(fn (): array => ['kind' => FloppyDiskKind::Game, 'program' => $program]);
    }
}
