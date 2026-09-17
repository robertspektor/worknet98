<?php

namespace App\Http\Resources;

use App\Models\Employment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Employment
 */
class WorkplaceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => $this->company->name,
            'jobTitle' => $this->position->title,
            'award' => $this->awards()->latest('period')->value('period'),
        ];
    }
}
