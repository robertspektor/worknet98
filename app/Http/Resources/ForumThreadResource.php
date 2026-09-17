<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumThread
 */
class ForumThreadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $player = $request->user();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->authorPosition->person->name,
            'author_title' => $this->authorPosition->title,
            'is_own' => $player instanceof User && $this->author_employment_id !== null && $this->author_employment_id === $player->employment?->id,
            'posts_count' => $this->posts_count ?? $this->posts()->count(),
            'last_posted_at' => app(GameClock::class)->display($this->last_posted_at),
        ];
    }
}
