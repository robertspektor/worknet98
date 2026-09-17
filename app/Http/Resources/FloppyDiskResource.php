<?php

namespace App\Http\Resources;

use App\Models\FloppyDisk;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FloppyDisk
 */
class FloppyDiskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'kind' => $this->kind->value,
            'color' => $this->color,
            'pack_size' => $this->pack_size,
        ];
    }
}
