<?php

namespace App\World;

readonly class PersonRecord
{
    public function __construct(
        public string $slug,
        public string $name,
        public ?string $emailAddress,
    ) {}
}
