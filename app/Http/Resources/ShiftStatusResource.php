<?php

namespace App\Http\Resources;

use App\Work\ShiftStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read ShiftStatus $resource
 */
class ShiftStatusResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->resource->state(),
            'daily_salary' => $this->resource->employment->daily_salary,
            'clocked_in_at' => $this->resource->shift?->clocked_in_at->toIso8601String(),
            'clocked_out_at' => $this->resource->shift?->clocked_out_at?->toIso8601String(),
        ];
    }
}
