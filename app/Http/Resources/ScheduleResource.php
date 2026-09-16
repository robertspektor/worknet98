<?php

namespace App\Http\Resources;

use App\Models\Technician;
use App\Workplace\Schedule;
use App\Workplace\ServiceSlots;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Schedule $resource
 */
class ScheduleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'days' => array_map(fn (CarbonImmutable $day): string => $day->toDateString(), $this->resource->days),
            'slots' => ServiceSlots::ALL,
            'technicians' => $this->resource->technicians->map(fn (Technician $technician): array => [
                'id' => $technician->id,
                'name' => $technician->name,
                'skills' => $technician->skills,
                'busy' => $this->busySlotsOf($technician),
            ])->all(),
            'appointments' => AppointmentResource::collection($this->resource->appointments)->resolve($request),
        ];
    }

    /**
     * @return list<string>
     */
    private function busySlotsOf(Technician $technician): array
    {
        $busy = [];

        foreach ($this->resource->days as $day) {
            foreach (ServiceSlots::ALL as $slot) {
                if ($technician->isBusyAt($day, $slot)) {
                    $busy[] = $day->toDateString().' '.$slot;
                }
            }
        }

        return $busy;
    }
}
