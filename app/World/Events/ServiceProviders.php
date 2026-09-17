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
}
