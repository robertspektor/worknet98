<?php

namespace App\Http\Resources;

use App\Game\GameClock;
use App\Work\ShiftStatus;
use Carbon\CarbonImmutable;
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
            'clocked_in_at' => $this->displayOf($this->resource->shift?->clocked_in_at),
            'clocked_out_at' => $this->displayOf($this->resource->shift?->clocked_out_at),
        ];
    }

    private function displayOf(?CarbonImmutable $time): ?string
    {
        return $time === null ? null : app(GameClock::class)->display($time);
    }
}
