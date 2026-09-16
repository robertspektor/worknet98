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
            ...(new FloppyDiskResource($this->resource->disk))->toArray($request),
            'price' => $this->resource->disk->price,
            'status' => $this->resource->status()->value,
            'delivers_at' => $this->resource->order?->delivers_at->toIso8601String(),
        ];
    }
}
