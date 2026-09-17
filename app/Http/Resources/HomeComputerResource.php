<?php

namespace App\Http\Resources;

use App\Hardware\InstalledCpu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property InstalledCpu $resource
 */
class HomeComputerResource extends JsonResource
{
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
        ];
    }
}
