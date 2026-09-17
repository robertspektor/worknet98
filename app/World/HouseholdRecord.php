<?php

namespace App\World;

readonly class HouseholdRecord
{
    /**
     * @param  list<PersonRecord>  $members
     */
    public function __construct(
        public string $district,
        public string $street,
        public ?string $phone,
        public array $members,
    ) {}

    public function addressKey(): string
    {
        return "{$this->district}|{$this->street}";
    }
}
