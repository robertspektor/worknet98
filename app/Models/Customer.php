<?php

namespace App\Models;

use App\Workplace\Availability;
use Carbon\CarbonImmutable;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $branch_id
 * @property string $slug
 * @property string $name
 * @property string $street
 * @property string $city
 * @property string $phone
 * @property string $email_address
 * @property string $notes
 * @property Availability $availability
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 */
#[Fillable(['branch_id', 'slug', 'name', 'street', 'city', 'phone', 'email_address', 'notes', 'availability'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany<WorkCase, $this>
     */
    public function workCases(): HasMany
    {
        return $this->hasMany(WorkCase::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'availability' => Availability::class,
        ];
    }
}
