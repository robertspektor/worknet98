<?php

namespace App\Http\Resources;

use App\Hardware\InstalledCpu;
use App\Models\PlayerHardwarePart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property InstalledCpu $resource
 */
class HomeComputerResource extends JsonResource
{
    /**
     * @param  Collection<int, PlayerHardwarePart>  $deskParts
     */
    public function __construct(InstalledCpu $cpu, private readonly Collection $deskParts)
    {
        parent::__construct($cpu);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cpu' => [
                'slug' => $this->resource->part->slug,
                'speed_mhz' => $this->resource->part->speed_mhz,
                'needs_thermal_paste' => $this->resource->needsThermalPaste,
            ],
            'desk_parts' => $this->deskParts->map(fn (PlayerHardwarePart $deskPart): array => [
                'id' => $deskPart->id,
                'slug' => $deskPart->hardwarePart->slug,
                'slot' => $deskPart->hardwarePart->slot->value,
                'speed_mhz' => $deskPart->hardwarePart->speed_mhz,
                'is_used' => $deskPart->isUsed(),
            ])->values()->all(),
        ];
    }
}
