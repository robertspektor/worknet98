<?php

namespace App\Http\Resources;

use App\Models\EmployeeAward;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EmployeeAward
 */
class EmployeeAwardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period' => $this->period,
            'name' => $this->employment->position->person->name,
            'title' => $this->employment->position->title,
            'branch' => $this->branch->name,
            'is_own' => $this->employment_id === $request->user()?->employment?->id,
        ];
    }
}
