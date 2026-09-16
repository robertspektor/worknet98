<?php

namespace App\Http\Resources;

use App\Models\Company;
use App\Workplace\CompanySoftwareCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanySoftwareResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => $this->name,
            'office_address' => $this->office_address,
            'app_names' => app(CompanySoftwareCatalog::class)->appNamesFor($this->resource),
        ];
    }
}
