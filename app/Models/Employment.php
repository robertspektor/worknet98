<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\EmploymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property int $job_opening_id
 * @property int $position_id
 * @property int $daily_salary
 * @property CarbonImmutable $hired_at
 * @property CarbonImmutable $position_started_at
 * @property CarbonImmutable|null $ended_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Company $company
 * @property-read JobOpening $jobOpening
 * @property-read Position $position
 * @property int|null $review_score
 * @property int|null $excellent_reviews
 * @property-read User $user
 */
#[Fillable(['user_id', 'company_id', 'job_opening_id', 'position_id', 'daily_salary', 'hired_at', 'position_started_at', 'ended_at'])]
class Employment extends Model
{
    /** @use HasFactory<EmploymentFactory> */
    use HasFactory;

    public function branch(): Branch
    {
        return $this->position->branch;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function bookedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'booked_by_employment_id');
    }

    /**
     * @return HasMany<Email, $this>
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * @param  Builder<Employment>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('ended_at');
    }

    /**
     * @return HasMany<EmployeeAward, $this>
     */
    public function awards(): HasMany
    {
        return $this->hasMany(EmployeeAward::class);
    }

    /**
     * @return HasMany<PerformanceReview, $this>
     */
    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    /**
     * @return HasMany<PromotionOffer, $this>
     */
    public function promotionOffers(): HasMany
    {
        return $this->hasMany(PromotionOffer::class);
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return BelongsTo<JobOpening, $this>
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hired_at' => 'datetime',
            'position_started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }
}
