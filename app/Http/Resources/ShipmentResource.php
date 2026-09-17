<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Shipment
 */
class ShipmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contents' => $this->contents,
            'size' => $this->size->value,
            'sender' => $this->sender->company->name,
            'contact' => $this->dispatchCase?->customer->person->name,
            'recipient' => $this->recipient->company->name,
            'district' => $this->recipient->name,
            'due_date' => $this->due_date->toDateString(),
            'due_slot' => $this->due_slot,
            'plan' => $this->plan($request),
            'delivered_at' => $this->delivered_at === null ? null : app(GameClock::class)->display($this->delivered_at),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function plan(Request $request): ?array
    {
        if ($this->driver === null || $this->tour_date === null || $this->tour === null) {
            return null;
        }

        return [
            'driver_id' => $this->driver->id,
            'driver' => $this->driver->person->name,
            'date' => $this->tour_date->toDateString(),
            'tour' => $this->tour->value,
            'is_own' => $this->isPlannedBy($request->user()),
            'planned_by' => $this->plannedBy?->position->title,
            'planned_by_npc' => $this->planned_by_employment_id === null ? $this->plannedByPosition?->person->name : null,
        ];
    }

    private function isPlannedBy(mixed $player): bool
    {
        return $player instanceof User
            && $this->planned_by_employment_id !== null
            && $this->planned_by_employment_id === $player->employment?->id;
    }
}
