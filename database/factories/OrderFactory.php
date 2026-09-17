<?php

namespace Database\Factories;

use App\Models\FloppyDisk;
use App\Models\Order;
use App\Models\User;
use App\Shop\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_type' => (new FloppyDisk)->getMorphClass(),
            'product_id' => FloppyDisk::factory()->forSale(),
            'price' => 40,
            'delivers_at' => now()->addDay(),
        ];
    }

    public function of(Product&Model $product): static
    {
        return $this->state(fn (): array => [
            'product_type' => $product->getMorphClass(),
            'product_id' => $product->getKey(),
        ]);
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
