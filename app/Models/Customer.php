<?php

namespace App\Models;

use App\Workplace\Availability;
use Carbon\CarbonImmutable;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $person_id
 * @property string $notes
 * @property string|null $contact_address
 * @property Availability $availability
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Person $person
 */
#[Fillable(['branch_id', 'person_id', 'notes', 'contact_address', 'availability'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    public function emailAddress(): ?string
    {
        return $this->contact_address ?? $this->person->email_address;
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
     * @return HasMany<WorkCase, $this>
     */
    public function workCases(): HasMany
    {
        return $this->hasMany(WorkCase::class);
    }

    /**
     * @param  Builder<Customer>  $query
     */
    public function scopeOfPerson(Builder $query, string $slug): void
    {
        $query->whereRelation('person', 'slug', $slug);
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
