<?php

namespace App\Models;

use App\World\Population\Occupation;
use Carbon\CarbonImmutable;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $city_id
 * @property int $household_id
 * @property string $slug
 * @property string $name
 * @property string|null $email_address
 * @property Occupation|null $occupation
 * @property string|null $profession
 * @property int|null $monthly_income
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read City $city
 * @property-read Household $household
 */
#[Fillable(['city_id', 'household_id', 'slug', 'name', 'email_address', 'occupation', 'profession', 'monthly_income'])]
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occupation' => Occupation::class,
        ];
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * @return HasMany<Customer, $this>
     */
    public function customerships(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
