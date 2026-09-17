<?php

namespace App\Http\Resources;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Person
 */
class RegistryPersonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'street' => $this->household->street,
            'district' => $this->household->district,
            'phone' => $this->household->phone,
            'occupation' => $this->occupation?->value,
            'profession' => $this->profession,
            'household' => $this->household->members
                ->reject(fn (Person $member): bool => $member->id === $this->id)
                ->pluck('name')
                ->values()
                ->all(),
        ];
    }
}
