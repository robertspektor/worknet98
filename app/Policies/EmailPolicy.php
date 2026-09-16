<?php

namespace App\Policies;

use App\Models\Email;
use App\Models\User;

class EmailPolicy
{
    public function update(User $player, Email $email): bool
    {
        return $email->user_id === $player->id;
    }
}
