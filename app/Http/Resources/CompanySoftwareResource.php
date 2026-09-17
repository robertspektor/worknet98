<?php

namespace App\Http\Resources;

use App\Models\Branch;
use App\Workplace\CompanySoftwareCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Branch
 */
class CompanySoftwareResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => $this->company->name,
            'branch' => $this->name,
            'office_address' => $this->office_address,
            'app_names' => app(CompanySoftwareCatalog::class)->appNamesFor($this->company),
        ];
    }
}
