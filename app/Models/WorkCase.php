<?php

namespace App\Models;

use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $position_id
 * @property int|null $employment_id
 * @property int $customer_id
 * @property WorkCaseKind $kind
 * @property string $case_slug
 * @property string|null $demand_key
 * @property WorkCaseStatus $status
 * @property CarbonImmutable $opened_at
 * @property CarbonImmutable|null $npc_due_at
 * @property CarbonImmutable|null $resolved_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Position $position
 * @property-read Employment|null $employment
 * @property-read Customer $customer
 */
#[Fillable(['branch_id', 'position_id', 'employment_id', 'customer_id', 'kind', 'case_slug', 'demand_key', 'status', 'opened_at', 'npc_due_at', 'resolved_at'])]
class WorkCase extends Model
{
    public function playerEmployment(): Employment
    {
        return $this->employment ?? throw new LogicException("Work case [{$this->id}] is not assigned to a player.");
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @param  Builder<WorkCase>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', WorkCaseStatus::Open);
    }

    /**
     * @param  Builder<WorkCase>  $query
     */
    public function scopeDueForNpc(Builder $query): void
    {
        $query->open()->whereNull('employment_id')->where('npc_due_at', '<=', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => WorkCaseKind::class,
            'status' => WorkCaseStatus::class,
            'opened_at' => 'datetime',
            'npc_due_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }
}
