<?php

namespace App\World;

use Illuminate\Support\Facades\File;

class CityCatalog
{
    /**
     * @return list<array{slug: string, name: string, locale: string, population: int, districts: list<string>}>
     */
    public function cities(): array
    {
        /** @var list<array{slug: string, name: string, locale: string, population: int, districts: list<string>}> */
        return File::json(database_path('content/cities.json'), JSON_THROW_ON_ERROR);
    }

    /**
     * @return list<HouseholdRecord>
     */
    public function namedHouseholdsOf(string $citySlug): array
    {
        /** @var list<array{district: string, street: string, phone?: string, members: list<array{slug: string, name: string, email_address?: string}>}> $households */
        $households = File::json(database_path("content/people/{$citySlug}.json"), JSON_THROW_ON_ERROR);

        return array_map(fn (array $household): HouseholdRecord => new HouseholdRecord(
            district: $household['district'],
            street: $household['street'],
            phone: $household['phone'] ?? null,
            members: array_map(
                fn (array $member): PersonRecord => new PersonRecord($member['slug'], $member['name'], $member['email_address'] ?? null),
                $household['members'],
            ),
        ), $households);
    }
}
