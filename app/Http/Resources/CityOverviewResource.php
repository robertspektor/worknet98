<?php

namespace App\Http\Resources;

use App\Citynet\CityOverview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read CityOverview $resource
 */
class CityOverviewResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->city->name,
            'locale' => $this->resource->city->locale,
            'residents' => $this->resource->residents,
            'households' => $this->resource->households,
            'employed' => $this->resource->employed,
            'unemployed' => $this->resource->unemployed,
            'unemployment_rate' => $this->resource->unemploymentRate(),
            'average_income' => $this->resource->averageIncome,
            'organizations' => $this->resource->organizations,
            'open_positions' => $this->resource->openPositions,
        ];
    }
}
