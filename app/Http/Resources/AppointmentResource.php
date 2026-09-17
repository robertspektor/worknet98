<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Models\Shipment;
use App\Models\User;
use App\Models\WorkCase;
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
            'customer' => ['id' => $this->customer->id, 'name' => $this->customer->person->name],
            'is_own' => $this->isBookedBy($request->user()),
            'booked_by' => $this->bookedBy?->position->title,
            'booked_by_npc' => $this->booked_by_employment_id === null ? $this->bookedByPosition?->person->name : null,
            'failed' => $this->failed_at !== null,
            'part' => $this->partStatus(),
        ];
    }

    /**
     * @return array{contents: string, status: string, arrival: string|null}|null
     */
    private function partStatus(): ?array
    {
        $shipment = $this->customer->workCases->sortByDesc('id')->map(fn (WorkCase $workCase): ?Shipment => $workCase->partShipment)->filter()->first();

        if ($shipment === null) {
            return null;
        }

        return [
            'contents' => $shipment->contents,
            'status' => match (true) {
                $shipment->delivered_at !== null => 'delivered',
                $shipment->isPlanned() => 'planned',
                default => 'ordered',
            },
            'arrival' => $shipment->plannedArrival()?->format('Y-m-d H:i'),
        ];
    }

    private function isBookedBy(mixed $player): bool
    {
        return $player instanceof User
            && $this->booked_by_employment_id !== null
            && $this->booked_by_employment_id === $player->employment?->id;
    }
}
