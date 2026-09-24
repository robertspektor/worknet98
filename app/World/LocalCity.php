<?php

namespace App\World;

use App\Models\City;

class LocalCity
{
    public function nameFor(string $locale): ?string
    {
        return City::query()->where('locale', $locale)->value('name');
    }
}
