<?php

namespace App\World\Events;

use App\Models\Branch;
use App\Models\City;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Random\Engine\Mt19937;
use Random\Randomizer;

class SubjectPicker
{
    private const REGULAR_CUSTOMER_PERCENT = 60;

    public function pick(City $city, ?Branch $provider, string $seed, bool $prefersRegularCustomers = true): ?Person
    {
        $randomizer = new Randomizer(new Mt19937(crc32($seed)));
        $candidates = $this->reachable($city, $provider);

        if ($provider !== null && $prefersRegularCustomers && $randomizer->getInt(1, 100) <= self::REGULAR_CUSTOMER_PERCENT) {
            $regular = $this->pickFrom((clone $candidates)->whereRelation('customerships', 'branch_id', $provider->id), $randomizer);

            if ($regular !== null) {
                return $regular;
            }
        }

        return $this->pickFrom($candidates, $randomizer);
    }

    /**
     * @return Builder<Person>
     */
    private function reachable(City $city, ?Branch $provider): Builder
    {
        return Person::query()
            ->whereBelongsTo($city)
            ->whereNotNull('email_address')
            ->when($provider !== null, fn (Builder $query) => $query->whereDoesntHave(
                'customerships',
                fn (Builder $customers) => $customers->where('branch_id', $provider?->id)->whereHas('workCases', fn (Builder $cases) => $cases->open()),
            ));
    }

    /**
     * @param  Builder<Person>  $candidates
     */
    private function pickFrom(Builder $candidates, Randomizer $randomizer): ?Person
    {
        $count = $candidates->count();

        return $count === 0 ? null : $candidates->orderBy('id')->offset($randomizer->getInt(0, $count - 1))->first();
    }
}
