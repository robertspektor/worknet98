<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'company_id' => Company::factory(),
            'slug' => Str::slug($name),
            'name' => $name,
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'phone' => fake()->numerify('555-####'),
            'email_address' => fake()->unique()->safeEmail(),
            'notes' => fake()->sentence(),
        ];
    }
}
