<?php

namespace App\World;

use App\World\Population\Livelihood;

readonly class PersonRecord
{
    public function __construct(
        public string $slug,
        public string $name,
        public ?string $emailAddress,
        public ?Livelihood $livelihood = null,
    ) {}
}
