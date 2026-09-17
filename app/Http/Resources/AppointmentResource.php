<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Appointment
 */
class AppointmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toDateString(),
            'slot' => $this->slot,
            'technician_id' => $this->technician_id,
            'customer' => ['id' => $this->customer->id, 'name' => $this->customer->name],
            'is_own' => $this->isBookedBy($request->user()),
            'booked_by' => $this->bookedBy?->position->title,
            'booked_by_npc' => $this->booked_by_employment_id === null ? $this->bookedByPosition?->npc_name : null,
        ];
    }

    private function isBookedBy(mixed $player): bool
    {
        return $player instanceof User
            && $this->booked_by_employment_id !== null
            && $this->booked_by_employment_id === $player->employment?->id;
    }
}
