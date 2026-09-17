<?php

namespace App\Models;

use App\Logistics\Tour;
use App\Logistics\Vehicle;
use Carbon\CarbonImmutable;
use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $person_id
 * @property Vehicle $vehicle
 * @property list<array{weekday: int, tour: string}> $busy_tours
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Person $person
 */
#[Fillable(['branch_id', 'person_id', 'vehicle', 'busy_tours'])]
class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    public function isBusyOn(CarbonImmutable $date, Tour $tour): bool
    {
        return collect($this->busy_tours)->contains(
            fn (array $busy): bool => $busy['weekday'] === $date->dayOfWeekIso && $busy['tour'] === $tour->value,
        );
    }

    public function loadOn(CarbonImmutable $date, Tour $tour): int
    {
        return $this->shipments()->whereDate('tour_date', $date->toDateString())->where('tour', $tour)->count();
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return HasMany<Shipment, $this>
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vehicle' => Vehicle::class,
            'busy_tours' => 'array',
        ];
    }
}
