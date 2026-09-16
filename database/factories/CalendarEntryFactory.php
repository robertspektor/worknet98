<?php

namespace Database\Factories;

use App\Models\CalendarEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEntry>
 */
class CalendarEntryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00',
            'title' => fake()->sentence(3),
        ];
    }
}
