<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Employment;
use App\Models\JobOpening;
use App\Models\Position;
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
            'position_id' => fn (array $attributes): int => Position::factory()->create([
                'branch_id' => Branch::factory()->create(['company_id' => $attributes['company_id']])->id,
                'job_opening_id' => $attributes['job_opening_id'],
            ])->id,
            'daily_salary' => 100,
            'hired_at' => now(),
        ];
    }

    public function at(Position $position): static
    {
        return $this->state(fn (): array => [
            'position_id' => $position->id,
            'job_opening_id' => $position->job_opening_id ?? JobOpening::factory()->create(['company_id' => $position->branch->company_id])->id,
            'company_id' => $position->branch->company_id,
        ]);
    }
}
