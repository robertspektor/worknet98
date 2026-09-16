<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\Employment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employment_id' => Employment::factory(),
            'contact_name' => 'Bev Mercer',
            'is_from_player' => false,
            'body' => fake()->sentence(),
            'replies' => null,
            'sent_at' => now(),
        ];
    }
}
