<?php

namespace App\World\Population;

use Illuminate\Support\Facades\File;

readonly class NamePool
{
    /**
     * @param  list<string>  $firstNames
     * @param  list<string>  $lastNames
     * @param  list<string>  $streets
     * @param  list<string>  $emailDomains
     * @param  list<string>  $petNames
     * @param  list<string>  $petBreeds
     * @param  list<string>  $professions
     * @param  array<string, int>  $occupationWeights
     * @param  array<string, array{int, int}>  $incomeRanges
     */
    public function __construct(
        public string $locale,
        public array $firstNames,
        public array $lastNames,
        public array $streets,
        public string $streetFormat,
        public string $phoneFormat,
        public array $emailDomains,
        public array $petNames,
        public array $petBreeds,
        public array $professions,
        public array $occupationWeights,
        public array $incomeRanges,
    ) {}

    public static function forLocale(string $locale): self
    {
        /** @var array{first_names: list<string>, last_names: list<string>, streets: list<string>, street_format: string, phone_format: string, email_domains: list<string>, pet_names: list<string>, pet_breeds: list<string>, professions: list<string>, occupation_weights: array<string, int>, income_ranges: array<string, array{int, int}>} $pool */
        $pool = File::json(database_path("content/name_pools/{$locale}.json"), JSON_THROW_ON_ERROR);

        return new self(
            locale: $locale,
            firstNames: $pool['first_names'],
            lastNames: $pool['last_names'],
            streets: $pool['streets'],
            streetFormat: $pool['street_format'],
            phoneFormat: $pool['phone_format'],
            emailDomains: $pool['email_domains'],
            petNames: $pool['pet_names'],
            petBreeds: $pool['pet_breeds'],
            professions: $pool['professions'],
            occupationWeights: $pool['occupation_weights'],
            incomeRanges: $pool['income_ranges'],
        );
    }
}
