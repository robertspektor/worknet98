<?php

namespace App\Cases\Templates;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseCustomers
{
    public function hasOpenCase(Customer $customer): bool
    {
        return $customer->workCases()->open()->exists();
    }

    public function nextWithoutOpenCase(Branch $branch): ?Customer
    {
        return $this->withoutOpenCase($branch)->first();
    }

    /**
     * @return HasMany<Customer, Branch>
     */
    private function withoutOpenCase(Branch $branch): HasMany
    {
        return $branch->customers()
            ->whereDoesntHave('workCases', fn (Builder $query) => $query->open())
            ->orderBy('id');
    }
}
