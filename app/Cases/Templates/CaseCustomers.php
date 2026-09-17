<?php

namespace App\Cases\Templates;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Random\Engine\Mt19937;
use Random\Randomizer;

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

    public function randomWithoutOpenCase(Branch $branch, string $seed): ?Customer
    {
        $candidates = $this->withoutOpenCase($branch)->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates[(new Randomizer(new Mt19937(crc32($seed))))->getInt(0, $candidates->count() - 1)];
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
