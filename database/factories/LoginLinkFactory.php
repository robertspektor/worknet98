<?php

namespace Database\Factories;

use App\Models\LoginLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LoginLink>
 */
class LoginLinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'token_hash' => hash('sha256', Str::random(64)),
            'locale' => 'en',
            'age_confirmed_at' => now(),
            'expires_at' => now()->addMinutes(15),
        ];
    }

    public function forToken(string $token): static
    {
        return $this->state(fn (): array => ['token_hash' => hash('sha256', $token)]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => ['expires_at' => now()->subMinute()]);
    }

    public function consumed(): static
    {
        return $this->state(fn (): array => ['consumed_at' => now()->subMinute()]);
    }
}
