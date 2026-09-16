<?php

namespace Database\Factories;

use App\Models\FloppyDisk;
use App\Models\FloppyDiskOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FloppyDiskOrder>
 */
class FloppyDiskOrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'floppy_disk_id' => FloppyDisk::factory()->forSale(),
            'price' => 40,
            'delivers_at' => now()->addDay(),
        ];
    }

    public function due(): static
    {
        return $this->state(fn (): array => ['delivers_at' => now()->subMinute()]);
    }

    public function delivered(): static
    {
        return $this->state(fn (): array => ['delivers_at' => now()->subHour(), 'delivered_at' => now()->subHour()]);
    }

    public function unpacked(): static
    {
        return $this->delivered()->state(fn (): array => ['unpacked_at' => now()->subMinute()]);
    }
}
