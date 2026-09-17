<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\WorldEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $city_id
 * @property int|null $parent_id
 * @property int $person_id
 * @property string $key
 * @property string $type
 * @property CarbonImmutable $occurred_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read City $city
 * @property-read WorldEvent|null $parent
 * @property-read Person $person
 * @property-read WorkCase|null $workCase
 */
#[Fillable(['city_id', 'parent_id', 'person_id', 'key', 'type', 'occurred_at'])]
class WorldEvent extends Model
{
    /** @use HasFactory<WorldEventFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<WorldEvent, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorldEvent::class, 'parent_id');
    }

    /**
     * @return HasMany<WorldEvent, $this>
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(WorldEvent::class, 'parent_id');
    }

    /**
     * @return BelongsTo<Person, $this>
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return HasOne<WorkCase, $this>
     */
    public function workCase(): HasOne
    {
        return $this->hasOne(WorkCase::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }
}
