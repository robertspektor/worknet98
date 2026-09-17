<?php

namespace App\Http\Resources;

use App\Models\PlayerFloppyDisk;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PlayerFloppyDisk
 */
class PlayerFloppyDiskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->floppyDisk->slug,
            'kind' => $this->floppyDisk->kind->value,
            'color' => $this->floppyDisk->color,
            'label' => $this->label,
            'is_labelable' => $this->isLabelable(),
            'is_write_protected' => $this->is_write_protected,
            'capacity_bytes' => PlayerFloppyDisk::CAPACITY_BYTES,
        ];
    }
}
