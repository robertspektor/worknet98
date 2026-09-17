<?php

namespace App\Http\Resources;

use App\Shop\ShopItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ShopItem $resource
 */
class ShopItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...ProductResource::of($this->resource->product)->resolve($request),
            'price' => $this->resource->product->salePrice(),
            'status' => $this->resource->status()->value,
            'delivers_at' => $this->resource->order?->delivers_at->toIso8601String(),
        ];
    }
}
