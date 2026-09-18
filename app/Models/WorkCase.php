<?php

namespace App\Models;

use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $position_id
 * @property int|null $employment_id
 * @property int|null $taken_over_from_employment_id
 * @property int $customer_id
 * @property WorkCaseKind $kind
 * @property string $case_slug
 * @property int|null $world_event_id
 * @property int|null $shipment_id
 * @property int|null $civil_application_id
 * @property WorkCaseStatus $status
 * @property CarbonImmutable $opened_at
 * @property CarbonImmutable|null $npc_due_at
 * @property CarbonImmutable|null $seen_at
 * @property CarbonImmutable|null $taken_over_at
 * @property CarbonImmutable|null $reminded_at
 * @property CarbonImmutable|null $resolved_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Position $position
 * @property-read Employment|null $employment
 * @property-read Customer $customer
 * @property-read WorldEvent|null $worldEvent
 * @property-read Shipment|null $shipment
 * @property-read Shipment|null $partShipment
 * @property-read CivilApplication|null $civilApplication
 */
#[Fillable(['branch_id', 'position_id', 'employment_id', 'taken_over_from_employment_id', 'taken_over_at', 'customer_id', 'kind', 'case_slug', 'world_event_id', 'shipment_id', 'civil_application_id', 'status', 'opened_at', 'npc_due_at', 'seen_at', 'reminded_at', 'resolved_at'])]
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
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return BelongsTo<CivilApplication, $this>
     */
    public function civilApplication(): BelongsTo
    {
        return $this->belongsTo(CivilApplication::class);
    }

    /**
     * @return HasOne<Shipment, $this>
     */
    public function partShipment(): HasOne
    {
        return $this->hasOne(Shipment::class, 'repair_case_id');
    }

    /**
     * @return BelongsTo<WorldEvent, $this>
     */
    public function worldEvent(): BelongsTo
    {
        return $this->belongsTo(WorldEvent::class);
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
            'seen_at' => 'datetime',
            'reminded_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }
}
