<?php

namespace App\Http\Resources;

use App\Models\Employment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Employment
 */
class RankingEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $player = $request->user();

        return [
            'employment_id' => $this->id,
            'name' => $this->position->person->name,
            'title' => $this->position->title,
            'company' => $this->company->name,
            'score' => (int) ($this->review_score ?? 0),
            'excellent_reviews' => (int) ($this->excellent_reviews ?? 0),
            'is_own' => $player instanceof User && $this->id === $player->employment?->id,
        ];
    }
}
