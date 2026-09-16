<?php

namespace App\Http\Resources;

use App\Models\CalendarEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CalendarEntry
 */
class CalendarEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toDateString(),
            'time' => $this->time,
            'title' => $this->title,
        ];
    }
}
