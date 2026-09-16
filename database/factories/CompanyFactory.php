<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'slug' => Str::slug($name),
            'locale' => 'en',
            'name' => $name,
            'industry' => 'Logistics',
            'tagline' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'hr_contact_name' => 'Brenda Kowalczyk',
            'hr_contact_address' => 'brenda.kowalczyk@company.wn',
            'hiring_note' => 'Please bring your own chair.',
        ];
    }

    public function locale(string $locale): static
    {
        return $this->state(fn (): array => ['locale' => $locale]);
    }
}
