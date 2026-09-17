<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $branch_id
 * @property int|null $booked_by_employment_id
 * @property int $customer_id
 * @property int $technician_id
 * @property CarbonImmutable $date
 * @property string $slot
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Branch $branch
 * @property-read Employment|null $bookedBy
 * @property-read Customer $customer
 * @property-read Technician $technician
 */
#[Fillable(['branch_id', 'booked_by_employment_id', 'customer_id', 'technician_id', 'date', 'slot'])]
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo<Employment, $this>
     */
    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'booked_by_employment_id');
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo<Technician, $this>
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'immutable_date',
        ];
    }
}
