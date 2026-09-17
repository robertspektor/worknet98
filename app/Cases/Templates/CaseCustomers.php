<?php

namespace App\Cases\Templates;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CaseCustomers
{
    public function hasOpenCase(Customer $customer): bool
    {
        return $customer->workCases()->open()->exists();
    }

    public function nextWithoutOpenCase(Branch $branch): ?Customer
    {
        return $branch->customers()
            ->whereDoesntHave('workCases', fn (Builder $query) => $query->open())
            ->orderBy('id')
            ->first();
    }
}
