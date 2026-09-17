<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\JobOpening;
use App\Models\Position;
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
            'is_open' => true,
        ];
    }

    public function withVacancy(): static
    {
        return $this->afterCreating(function (JobOpening $opening): void {
            Position::factory()
                ->for(Branch::factory()->for($opening->company))
                ->create(['job_opening_id' => $opening->id]);
        });
    }

    public function closed(): static
    {
        return $this->state(fn (): array => ['is_open' => false]);
    }
}
