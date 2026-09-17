<?php

namespace App\Http\Resources;

use App\Models\DeskPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DeskPlacement
 */
class DeskPlacementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item' => $this->item,
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
