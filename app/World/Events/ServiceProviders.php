<?php

namespace App\World\Events;

use App\Models\Branch;
use App\Models\City;

class ServiceProviders
{
    public function branchFor(City $city, string $service): ?Branch
    {
        return $city->branches()
            ->with('company')
            ->orderBy('id')
            ->get()
            ->first(fn (Branch $branch): bool => $branch->company->offers($service));
    }

    public function branchOf(City $city, string $companySlug): ?Branch
    {
        return $city->branches()
            ->whereRelation('company', 'slug', $companySlug)
            ->with('company')
            ->orderBy('id')
            ->first();
    }
}
