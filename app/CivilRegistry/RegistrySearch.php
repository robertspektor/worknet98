<?php

namespace App\CivilRegistry;

use App\Models\Branch;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RegistrySearch
{
    private const LIMIT = 25;

    /**
     * @return Collection<int, Person>
     */
    public function find(Branch $office, string $query): Collection
    {
        $pattern = '%'.addcslashes($query, '%_\\').'%';

        return Person::query()
            ->where('city_id', $office->city_id)
            ->where(fn (Builder $people) => $people
                ->whereLike('name', $pattern)
                ->orWhereHas('household', fn (Builder $household) => $household->whereLike('street', $pattern)))
            ->with('household.members')
            ->orderBy('name')
            ->limit(self::LIMIT)
            ->get();
    }
}
