<?php

namespace App\Models;

use App\Logistics\ShipmentSize;
use App\Logistics\Tour;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $sender_branch_id
 * @property int $recipient_branch_id
 * @property int|null $repair_case_id
 * @property string|null $order_key
 * @property string $contents
 * @property ShipmentSize $size
 * @property CarbonImmutable $due_date
 * @property string $due_slot
 * @property int|null $driver_id
 * @property CarbonImmutable|null $tour_date
 * @property Tour|null $tour
 * @property int|null $planned_by_employment_id
 * @property int|null $planned_by_position_id
 * @property CarbonImmutable|null $delivered_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Branch $sender
 * @property-read Branch $recipient
 * @property-read WorkCase|null $repairCase
 * @property-read WorkCase|null $dispatchCase
 * @property-read Driver|null $driver
 * @property-read Employment|null $plannedBy
 * @property-read Position|null $plannedByPosition
 */
#[Fillable(['branch_id', 'sender_branch_id', 'recipient_branch_id', 'repair_case_id', 'order_key', 'contents', 'size', 'due_date', 'due_slot', 'driver_id', 'tour_date', 'tour', 'planned_by_employment_id', 'planned_by_position_id', 'delivered_at'])]
class Shipment extends Model
{
    public function dueAt(): CarbonImmutable
    {
        return $this->due_date->setTimeFromTimeString($this->due_slot);
    }

    public function isPlanned(): bool
    {
        return $this->driver_id !== null && $this->tour_date !== null && $this->tour !== null;
    }

    public function plannedArrival(): ?CarbonImmutable
    {
        return $this->tour_date === null || $this->tour === null ? null : $this->tour->endsAt($this->tour_date);
    }

    public function isPlannedInTime(): bool
    {
        return $this->plannedArrival()?->lte($this->dueAt()) ?? false;
    }

    public function isSupplyOrder(): bool
    {
        return $this->repair_case_id === null;
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sender_branch_id');
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'recipient_branch_id');
    }

    /**
     * @return BelongsTo<WorkCase, $this>
     */
    public function repairCase(): BelongsTo
    {
        return $this->belongsTo(WorkCase::class, 'repair_case_id');
    }

    /**
     * @return HasOne<WorkCase, $this>
     */
    public function dispatchCase(): HasOne
    {
        return $this->hasOne(WorkCase::class);
    }

    /**
     * @return BelongsTo<Driver, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function plannedBy(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'planned_by_employment_id');
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function plannedByPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'planned_by_position_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => ShipmentSize::class,
            'due_date' => 'immutable_date',
            'tour_date' => 'immutable_date',
            'tour' => Tour::class,
            'delivered_at' => 'datetime',
        ];
    }
}
