<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->person->name,
            'street' => $this->person->household->street,
            'city' => $this->person->household->district,
            'phone' => $this->person->household->phone,
            'email_address' => $this->emailAddress(),
            'notes' => $this->notes,
        ];
    }
}
