<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TechnicianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $branch_id
 * @property string $slug
 * @property string $name
 * @property list<string> $skills
 * @property list<array{weekday: int, slot: string}> $busy_slots
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 */
#[Fillable(['branch_id', 'slug', 'name', 'skills', 'busy_slots'])]
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

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
