<?php

namespace App\Policies;

use App\Models\ForumPost;
use App\Models\User;

class ForumPostPolicy
{
    public function delete(User $player, ForumPost $post): bool
    {
        return $post->author_employment_id !== null && $post->author_employment_id === $player->employment?->id;
    }
}
