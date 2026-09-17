<?php

namespace App\Http\Resources;

use App\Models\HardwarePart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HardwarePart
 */
class HardwarePartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'slot' => $this->slot->value,
            'speed_mhz' => $this->speed_mhz,
        ];
    }
}
