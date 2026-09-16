<?php

namespace App\Http\Resources;

use App\Models\FloppyDiskOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FloppyDiskOrder
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
            'floppy_disk' => new FloppyDiskResource($this->floppyDisk),
        ];
    }
}
