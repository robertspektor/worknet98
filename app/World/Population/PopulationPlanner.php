<?php

namespace App\World\Population;

use App\World\HouseholdRecord;
use App\World\PersonRecord;
use Illuminate\Support\Str;
use Random\Engine\Mt19937;
use Random\Randomizer;

class PopulationPlanner
{
    private const HOUSEHOLD_SIZE_WEIGHTS = [1 => 30, 2 => 30, 3 => 15, 4 => 17, 5 => 8];

    private const EMAIL_PERCENT = 25;

    private const PHONE_PERCENT = 92;

    private const MAX_HOUSE_NUMBER = 180;

    private Randomizer $randomizer;

    private NamePool $pool;

    public function __construct(private readonly Livelihoods $livelihoods) {}

    /** @var array<string, true> */
    private array $takenAddresses = [];

    /** @var array<string, true> */
    private array $takenSlugs = [];

    /**
     * @param  list<string>  $districts
     * @param  list<HouseholdRecord>  $namedHouseholds
     * @return list<HouseholdRecord>
     */
    public function plan(string $citySlug, array $districts, int $residents, NamePool $pool, array $namedHouseholds): array
    {
        $this->randomizer = new Randomizer(new Mt19937(crc32("population|{$citySlug}")));
        $this->pool = $pool;
        $this->reserve($namedHouseholds);

        $households = [];
        $remaining = $residents;

        while ($remaining > 0) {
            $household = $this->household($districts, min($this->householdSize(), $remaining));
            $households[] = $household;
            $remaining -= count($household->members);
        }

        return $households;
    }

    /**
     * @param  list<HouseholdRecord>  $namedHouseholds
     */
    private function reserve(array $namedHouseholds): void
    {
        $this->takenAddresses = [];
        $this->takenSlugs = [];

        foreach ($namedHouseholds as $household) {
            $this->takenAddresses[$household->addressKey()] = true;

            foreach ($household->members as $member) {
                $this->takenSlugs[$member->slug] = true;
            }
        }
    }

    /**
     * @param  list<string>  $districts
     */
    private function household(array $districts, int $size): HouseholdRecord
    {
        [$district, $street] = $this->freeAddress($districts);
        $lastName = $this->pick($this->pool->lastNames);

        return new HouseholdRecord(
            district: $district,
            street: $street,
            phone: $this->chance(self::PHONE_PERCENT) ? $this->phone() : null,
            members: array_map(fn (): PersonRecord => $this->person($lastName), range(1, $size)),
        );
    }

    /**
     * @param  list<string>  $districts
     * @return array{string, string}
     */
    private function freeAddress(array $districts): array
    {
        do {
            $district = $this->pick($districts);
            $street = strtr($this->pool->streetFormat, [
                ':street' => $this->pick($this->pool->streets),
                ':number' => (string) $this->randomizer->getInt(1, self::MAX_HOUSE_NUMBER),
            ]);
        } while (isset($this->takenAddresses["{$district}|{$street}"]));

        $this->takenAddresses["{$district}|{$street}"] = true;

        return [$district, $street];
    }

    private function person(string $lastName): PersonRecord
    {
        $firstName = $this->pick($this->pool->firstNames);
        $slug = $this->freeSlug(Str::slug("{$firstName} {$lastName}", language: $this->pool->locale));

        return new PersonRecord(
            slug: $slug,
            name: "{$firstName} {$lastName}",
            emailAddress: $this->chance(self::EMAIL_PERCENT) ? $this->emailAddress($slug) : null,
            livelihood: $this->livelihoods->draw($this->randomizer, $this->pool),
        );
    }

    private function freeSlug(string $base): string
    {
        $slug = $base;

        for ($suffix = 2; isset($this->takenSlugs[$slug]); $suffix++) {
            $slug = "{$base}-{$suffix}";
        }

        $this->takenSlugs[$slug] = true;

        return $slug;
    }

    private function emailAddress(string $slug): string
    {
        return str_replace('-', '.', $slug).'@'.$this->pick($this->pool->emailDomains);
    }

    private function phone(): string
    {
        return preg_replace_callback('/#/', fn (): string => (string) $this->randomizer->getInt(0, 9), $this->pool->phoneFormat) ?? $this->pool->phoneFormat;
    }

    private function householdSize(): int
    {
        $roll = $this->randomizer->getInt(1, array_sum(self::HOUSEHOLD_SIZE_WEIGHTS));

        foreach (self::HOUSEHOLD_SIZE_WEIGHTS as $size => $weight) {
            $roll -= $weight;

            if ($roll <= 0) {
                return $size;
            }
        }

        return 1;
    }

    private function chance(int $percent): bool
    {
        return $this->randomizer->getInt(1, 100) <= $percent;
    }

    /**
     * @template T
     *
     * @param  list<T>  $items
     * @return T
     */
    private function pick(array $items): mixed
    {
        return $items[$this->randomizer->getInt(0, count($items) - 1)];
    }
}
