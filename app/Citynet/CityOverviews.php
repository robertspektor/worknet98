<?php

namespace App\Citynet;

use App\Models\City;
use App\Models\Company;
use App\Models\Household;
use App\Models\Person;
use App\Models\Position;
use App\World\Population\Occupation;

class CityOverviews
{
    /**
     * @return list<CityOverview>
     */
    public function all(): array
    {
        $overviews = [];

        foreach (City::query()->orderBy('name')->get() as $city) {
            $overviews[] = $this->of($city);
        }

        return $overviews;
    }

    public function of(City $city): CityOverview
    {
        $residents = Person::query()->whereBelongsTo($city);

        return new CityOverview(
            city: $city,
            residents: (clone $residents)->count(),
            households: Household::query()->whereBelongsTo($city)->count(),
            employed: (clone $residents)->where('occupation', Occupation::Employed)->count(),
            unemployed: (clone $residents)->where('occupation', Occupation::Unemployed)->count(),
            averageIncome: (int) round((float) (clone $residents)->avg('monthly_income')),
            organizations: Company::query()->whereHas('branches', fn ($branches) => $branches->whereBelongsTo($city))->count(),
            openPositions: Position::query()->whereRelation('branch', 'city_id', $city->id)->whereRelation('jobOpening', 'is_open', true)->vacant()->count(),
        );
    }
}
