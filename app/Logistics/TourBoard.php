<?php

namespace App\Logistics;

use App\Models\Branch;
use App\Models\Driver;
use App\Workplace\BookingWindow;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class TourBoard
{
    public function __construct(private readonly BookingWindow $window) {}

    /**
     * @return list<CarbonImmutable>
     */
    public function days(): array
    {
        return $this->window->days();
    }

    /**
     * @return Collection<int, Driver>
     */
    public function driversOf(Branch $branch): Collection
    {
        return $branch->drivers()->with('person')->get()->sortBy('person.name')->values();
    }
}
