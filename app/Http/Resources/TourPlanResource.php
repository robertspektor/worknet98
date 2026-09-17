<?php

namespace App\Http\Resources;

use App\Logistics\Tour;
use App\Models\Driver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read array{days: list<CarbonImmutable>, drivers: Collection<int, Driver>} $resource
 */
class TourPlanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $days = $this->resource['days'];

        return [
            'days' => array_map(fn (CarbonImmutable $day): string => $day->toDateString(), $days),
            'tours' => array_map(fn (Tour $tour): array => [
                'id' => $tour->value,
                'starts' => $tour->startsAt($days[0])->format('H:i'),
                'ends' => $tour->endsAt($days[0])->format('H:i'),
            ], Tour::cases()),
            'drivers' => $this->resource['drivers']->map(fn (Driver $driver): array => [
                'id' => $driver->id,
                'name' => $driver->person->name,
                'vehicle' => $driver->vehicle->value,
                'capacity' => $driver->vehicle->capacity(),
                'busy' => $this->busyToursOf($driver, $days),
            ])->all(),
        ];
    }

    /**
     * @param  list<CarbonImmutable>  $days
     * @return list<string>
     */
    private function busyToursOf(Driver $driver, array $days): array
    {
        $busy = [];

        foreach ($days as $day) {
            foreach (Tour::cases() as $tour) {
                if ($driver->isBusyOn($day, $tour)) {
                    $busy[] = $day->toDateString().' '.$tour->value;
                }
            }
        }

        return $busy;
    }
}
