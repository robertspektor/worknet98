<?php

namespace Database\Factories;

use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employment>
 */
class EmploymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_opening_id' => JobOpening::factory(),
            'company_id' => fn (array $attributes): int => JobOpening::query()->whereKey($attributes['job_opening_id'])->sole()->company_id,
            'daily_salary' => 100,
            'hired_at' => now(),
        ];
    }
}
