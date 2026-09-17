<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    public function read(User $player, ForumThread $thread): bool
    {
        return $thread->company_id === $player->employment?->company_id;
    }
}
