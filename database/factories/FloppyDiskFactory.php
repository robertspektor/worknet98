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
            'is_starter' => false,
            'price' => null,
            'pack_size' => 1,
            'files' => [],
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

    public function blankPack(int $packSize): static
    {
        return $this->state(fn (): array => ['kind' => FloppyDiskKind::Blank, 'pack_size' => $packSize]);
    }

    public function program(string $program): static
    {
        return $this->state(fn (array $attributes): array => [
            'kind' => FloppyDiskKind::Game,
            'files' => [
                ['name' => 'README.TXT', 'kind' => 'text', 'content_key' => "floppy_disk.{$attributes['slug']}.readme"],
                ['name' => 'SETUP.EXE', 'kind' => 'setup', 'program' => $program, 'size_bytes' => 300_000],
            ],
        ]);
    }
}
