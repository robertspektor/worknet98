<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TechnicianFactory;
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
 * @property list<string> $skills
 * @property list<array{weekday: int, slot: string}> $busy_slots
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Person $person
 */
#[Fillable(['branch_id', 'person_id', 'skills', 'busy_slots'])]
class Technician extends Model
{
    /** @use HasFactory<TechnicianFactory> */
    use HasFactory;

    public function hasSkill(string $skill): bool
    {
        return in_array($skill, $this->skills, true);
    }

    public function isBusyAt(CarbonImmutable $date, string $slot): bool
    {
        return collect($this->busy_slots)->contains(
            fn (array $busy): bool => $busy['weekday'] === $date->dayOfWeekIso && $busy['slot'] === $slot,
        );
    }

    public function isBookedAt(CarbonImmutable $date, string $slot): bool
    {
        return $this->appointments()->whereDate('date', $date->toDateString())->where('slot', $slot)->exists();
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
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @param  Builder<Technician>  $query
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
            'skills' => 'array',
            'busy_slots' => 'array',
        ];
    }
}
