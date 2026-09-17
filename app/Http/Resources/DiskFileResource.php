<?php

namespace App\Http\Resources;

use App\Models\DiskFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DiskFile
 */
class DiskFileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'kind' => $this->kind->value,
            'size_bytes' => $this->size_bytes,
            'program' => $this->program,
            'text' => $this->textIn(app()->getLocale()),
        ];
    }
}
