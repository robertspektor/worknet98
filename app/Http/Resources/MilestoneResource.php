<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Milestones\MilestoneProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MilestoneProgress
 */
class MilestoneResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->key,
            'achieved' => $this->isAchieved(),
            'achieved_at' => $this->achievedAt === null ? null : app(GameClock::class)->display($this->achievedAt),
        ];
    }
}
