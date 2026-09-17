<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $branch_id
 * @property int|null $job_opening_id
 * @property int|null $reports_to_position_id
 * @property string $slug
 * @property string $title
 * @property string $npc_name
 * @property string $npc_address
 * @property list<string> $responsibilities
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read JobOpening|null $jobOpening
 * @property-read Position|null $reportsTo
 * @property-read Employment|null $holder
 */
#[Fillable(['branch_id', 'job_opening_id', 'reports_to_position_id', 'slug', 'title', 'npc_name', 'npc_address', 'responsibilities'])]
class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory;

    public function isHeldByPlayer(): bool
    {
        return $this->holder()->exists();
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<JobOpening, $this>
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'reports_to_position_id');
    }

    /**
     * @return HasOne<Employment, $this>
     */
    public function holder(): HasOne
    {
        return $this->hasOne(Employment::class)->whereNull('ended_at');
    }

    /**
     * @return HasMany<WorkCase, $this>
     */
    public function workCases(): HasMany
    {
        return $this->hasMany(WorkCase::class);
    }

    /**
     * @param  Builder<Position>  $query
     */
    public function scopeResponsibleFor(Builder $query, string $responsibility): void
    {
        $query->whereJsonContains('responsibilities', $responsibility);
    }

    /**
     * @param  Builder<Position>  $query
     */
    public function scopeVacant(Builder $query): void
    {
        $query->whereDoesntHave('holder');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responsibilities' => 'array',
        ];
    }
}
