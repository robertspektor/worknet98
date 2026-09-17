<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class ParcelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'storefront' => $this->product->storefront()->value,
            'product' => [
                'type' => $this->product_type,
                ...ProductResource::of($this->product)->resolve($request),
            ],
        ];
    }
}
