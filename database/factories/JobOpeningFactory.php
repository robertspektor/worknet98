<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobOpening>
 */
class JobOpeningFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'company_id' => Company::factory(),
            'slug' => Str::slug($title),
            'title' => $title,
            'description' => fake()->paragraph(),
            'daily_salary' => 100,
        ];
    }
}
