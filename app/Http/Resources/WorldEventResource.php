<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Models\WorldEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WorldEvent
 */
class WorldEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'city' => $this->city->name,
            'person' => $this->person->name,
            'occurred_at' => app(GameClock::class)->display($this->occurred_at),
            'caused_by' => $this->parent?->type,
            'handled_by' => $this->workCase?->branch->company->name,
        ];
    }
}
