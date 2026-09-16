<?php

namespace Database\Factories;

use App\Careers\JobApplicationStatus;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_opening_id' => JobOpening::factory(),
            'message' => null,
            'status' => JobApplicationStatus::Pending,
            'responds_at' => now()->addMinute(),
        ];
    }

    public function due(): static
    {
        return $this->state(fn (): array => ['responds_at' => now()->subSecond()]);
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => [
            'status' => JobApplicationStatus::Accepted,
            'responds_at' => now()->subMinute(),
            'responded_at' => now()->subMinute(),
        ]);
    }
}
