<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ForumPost
 */
class ForumPostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $player = $request->user();

        return [
            'id' => $this->id,
            'body' => $this->body,
            'author' => $this->authorPosition->person->name,
            'author_title' => $this->authorPosition->title,
            'is_own' => $player instanceof User && $this->author_employment_id !== null && $this->author_employment_id === $player->employment?->id,
            'posted_at' => app(GameClock::class)->display($this->created_at ?? now()),
        ];
    }
}
