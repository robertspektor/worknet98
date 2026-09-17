<?php

namespace App\World;

use App\Models\City;
use App\Models\Household;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class HouseholdWriter
{
    private const CHUNK_SIZE = 500;

    /**
     * @param  list<HouseholdRecord>  $households
     */
    public function write(City $city, array $households): void
    {
        foreach (array_chunk($households, self::CHUNK_SIZE) as $chunk) {
            DB::transaction(fn () => $this->writeChunk($city, $chunk));
        }
    }

    /**
     * @param  list<HouseholdRecord>  $households
     */
    private function writeChunk(City $city, array $households): void
    {
        $now = now();

        Household::query()->upsert(array_map(fn (HouseholdRecord $household): array => [
            'city_id' => $city->id,
            'district' => $household->district,
            'street' => $household->street,
            'phone' => $household->phone,
            'created_at' => $now,
            'updated_at' => $now,
        ], $households), ['city_id', 'district', 'street'], ['phone', 'updated_at']);

        $householdIds = $this->householdIds($city, $households);

        Person::query()->upsert(array_merge(...array_map(fn (HouseholdRecord $household): array => array_map(fn (PersonRecord $member): array => [
            'city_id' => $city->id,
            'household_id' => $householdIds[$household->addressKey()],
            'slug' => $member->slug,
            'name' => $member->name,
            'email_address' => $member->emailAddress,
            'created_at' => $now,
            'updated_at' => $now,
        ], $household->members), $households)), ['city_id', 'slug'], ['household_id', 'name', 'email_address', 'updated_at']);
    }

    /**
     * @param  list<HouseholdRecord>  $households
     * @return array<string, int>
     */
    private function householdIds(City $city, array $households): array
    {
        return Household::query()
            ->whereBelongsTo($city)
            ->whereIn('street', array_unique(array_map(fn (HouseholdRecord $household): string => $household->street, $households)))
            ->get(['id', 'district', 'street'])
            ->mapWithKeys(fn (Household $household): array => ["{$household->district}|{$household->street}" => $household->id])
            ->all();
    }
}
