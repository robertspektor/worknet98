<?php

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\User;

class ChatMessagePolicy
{
    public function reply(User $player, ChatMessage $message): bool
    {
        return $message->employment_id === $player->employment?->id;
    }
}
